<?php
namespace app\frontend\modules\goods\controllers;

use app\backend\modules\goods\models\Brand;
use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\exceptions\MemberNotLoginException;
use app\common\facades\Setting;
use app\common\models\Category;
use app\common\models\goods\Privilege;
use app\common\models\OrderGoods;
use app\frontend\models\Member;
use app\frontend\modules\goods\models\Goods;
use app\common\models\GoodsSpecItem;
use app\common\services\goods\SaleGoods;
use app\common\services\goods\VideoDemandCourseGoods;
use app\common\models\MemberShopInfo;
use app\frontend\modules\member\controllers\ServiceController;
use app\frontend\modules\member\listeners\Order;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\memberCart\MemberCartCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\IFTTTHandler;
use Yunshop\Commission\Common\Services\GoodsDetailService;
use Yunshop\Designer\models\ViewSet;
use Yunshop\EnterpriseWechat\services\QyWeiBanService;
use Yunshop\Room\models\Room;
use Yunshop\ServiceFee\models\ServiceFeeModel;
use Yunshop\StoreCashier\store\models\StoreGoods;
use Yunshop\TeamDividend\Common\Services\TeamDividendGoodsDetailService;
use Yunshop\Commission\models\Agents;
use Yunshop\Love\Common\Models\GoodsLove;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\controllers\MemberCouponController;
use app\common\services\goods\LeaseToyGoods;
use Yunshop\Supplier\common\models\SupplierGoods;
use Yunshop\TeamDividend\models\TeamDividendAgencyModel;
use app\common\models\MemberLevel;
use app\common\models\MemberGroup;
use Yunshop\StoreCashier\common\models\StoreSetting;
use app\frontend\modules\goods\models\Comment;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 22:16
 */
class GoodsController extends GoodsApiController
{
    protected $publicAction = ['getRecommendGoods'];
    protected $ignoreAction = ['getRecommendGoods'];

    // 拆分getGoods方法，分离和插件相关的部分，只提取属于商品的信息。和插件相关的部分在getGoods中处理
    protected function _getGoods($id, $integrated = null)
    {
        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        //fixby-zhd-商品详情免登陆20201101
        $member_id = \YunShop::app()->getMemberId();

        if ($member_id) {
            try {
                $member = Member::current()->yzMember;
            } catch (MemberNotLoginException  $e) {
                if (\YunShop::request()->type == 1 || \YunShop::request()->type == 2) {
                    return;
                }

                throw new MemberNotLoginException($e->getMessage());
            }
        }

        $goodsModel = $goods_model->uniacid()
            ->with([
                'hasManyParams' => function ($query) {
                    return $query->select('goods_id', 'title', 'value')->orderby('displayorder', 'asc');
                },
                'hasManySpecs' => function ($query) {
                    return $query->select('id', 'goods_id', 'title', 'description')->orderby('display_order', 'asc');
                },
                'hasManyOptions' => function ($query) {
                    return $query->select('id', 'goods_id', 'title', 'thumb', 'product_price', 'market_price', 'stock', 'specs', 'weight');
                },
                'hasManyDiscount' => function ($query) use ($member) {
                    return $query->where('level_id', $member->level_id);
                },
                'hasOneBrand' => function ($query) {
                    return $query->select('id', 'logo', 'name', 'desc');
                },
                'hasOneShare',
                'hasOneGoodsDispatch',
                'hasOnePrivilege',
                'hasOneSale',
                'hasOneGoodsCoupon',
                'hasOneInvitePage',
                'hasOneGoodsLimitBuy',
                'hasOneGoodsVideo',
            ])
            ->find($id);
        $goodsModel->vip_level_status;
        if (!$goodsModel) {
            if (is_null($integrated)) {
                return $this->errorJson('商品不存在.');
            } else {
                return show_json(0, '商品不存在.');
            }
        }


        //限时购 todo 后期优化 应该是前端优化
        $current_time = time();
        if (!is_null($goodsModel->hasOneGoodsLimitBuy)) {
            if ($goodsModel->hasOneGoodsLimitBuy->end_time < $current_time && $goodsModel->hasOneGoodsLimitBuy->status == 1) {
                $goodsModel->status = 0;
                $goodsModel->save();
            }
        }

        if (!$goodsModel->status) {
            if (is_null($integrated)) {
                return $this->errorJson('商品已下架.');
            } else {
                return show_json(0, '商品已下架.');
            }
        }


        $goodsModel->is_added = \ Setting::get('shop.member.added') ?: 1;


        //验证浏览权限
        $this->validatePrivilege($goodsModel, $member);

        //商品品牌处理
        if ($goodsModel->hasOneBrand) {
            $goodsModel->hasOneBrand->desc = html_entity_decode($goodsModel->hasOneBrand->desc);
            $goodsModel->hasOneBrand->logo = yz_tomedia($goodsModel->hasOneBrand->logo);
        }


        //商品规格图片处理
        if ($goodsModel->hasManyOptions && $goodsModel->hasManyOptions->toArray()) {
            foreach ($goodsModel->hasManyOptions as &$item) {
                $item->thumb = replace_yunshop(yz_tomedia($item->thumb));
            }
        }
        $goodsModel->content = html_entity_decode($goodsModel->content);
        if ($goodsModel->has_option) {
            $goodsModel->min_price = $goodsModel->hasManyOptions->min("product_price");
            $goodsModel->max_price = $goodsModel->hasManyOptions->max("product_price");
            $goodsModel->stock = $goodsModel->hasManyOptions->sum('stock');
        }
        foreach ($goodsModel->hasManySpecs as &$spec) {
            $spec['specitem'] = GoodsSpecItem::select('id', 'title', 'specid', 'thumb')->where('specid', $spec['id'])->orderBy('display_order', 'asc')->get();
            foreach ($spec['specitem'] as &$specitem) {
                $specitem['thumb'] = yz_tomedia($specitem['thumb']);
            }
        }

        $goodsModel->setHidden(
            [
                'deleted_at',
                'created_at',
                'updated_at',
                'cost_price',
                'real_sales',
                'is_deleted',
                'reduce_stock_method',
            ]);

        //商品图片处理
        if ($goodsModel->thumb) {
            $goodsModel->thumb = yz_tomedia($goodsModel->thumb);
        }
        if ($goodsModel->thumb_url) {
            $thumb_url = unserialize($goodsModel->thumb_url);
            foreach ($thumb_url as &$item) {
                $item = yz_tomedia($item);
            }
            $goodsModel->thumb_url = $thumb_url;
        }
        if (isset($goodsModel->sales_one_name_url[0])) {
            $goodsModel->sales_one_name_url = yz_tomedia($goodsModel->sales_one_name_url);
        }
        if (isset($goodsModel->sales_two_name_color[0]) && $goodsModel->sales_two_name_color[0] == '#') {
        }else{
            $goodsModel->sales_two_name_color = '#000000';
        }
        if (isset($goodsModel->sales_two_name_url[0])) {
            $goodsModel->sales_two_name_url = yz_tomedia($goodsModel->sales_two_name_url);
        }

        //商品视频处理
        if (!is_null($goodsModel->hasOneGoodsVideo) && $goodsModel->hasOneGoodsVideo->goods_video) {
            $goodsModel->goods_video = yz_tomedia($goodsModel->hasOneGoodsVideo->goods_video);
            $goodsModel->video_image = $goodsModel->hasOneGoodsVideo->video_image ? yz_tomedia($goodsModel->hasOneGoodsVideo->video_image) : yz_tomedia($goodsModel->thumb);
        } else {
            $goodsModel->goods_video = '';
            $goodsModel->video_image = '';
        }

        //商品营销 todo 优化新的
        $goodsModel->goods_sale = $this->getGoodsSaleV2($goodsModel, $member);
        $goodsModel->love_shoppin_gift = $this->loveShoppingGift($goodsModel);


        //商品会员优惠
        $goodsModel->member_discount = $this->getDiscount($goodsModel, $member);

        //商品是否开启领优惠卷
        //fixby-zhd-商品详情免登陆20201101
        if (!$member_id) {
            $goodsModel->availability = 0;
        } else {
            $goodsModel->availability = $this->couponsMemberLj($member);
        }

        //判断用户已购买总数 2020/8/03  zhd add line 1
        //fixby-zhd-商品详情免登陆20201101
        if ($member_id) {
            $goodsModel->member_history_num = Member::current()->orderGoods()->where('goods_id', $id)->sum('total');
        } else {
            $goodsModel->member_history_num = 0;
        }

        // 商品详情挂件
        if (\app\common\modules\shop\ShopConfig::current()->get('goods_detail')) {
            foreach (\app\common\modules\shop\ShopConfig::current()->get('goods_detail') as $key_name => $row) {
                $row_res = $row['class']::{$row['function']}($id, true);
                if ($row_res) {
                    $goodsModel->$key_name = $row_res;
                    //供应商在售商品总数
                    $class = new $row['class']();
                    if (method_exists($class, 'getGoodsIdsBySid')) {
                        $supplier_goods_id = SupplierGoods::getGoodsIdsBySid($goodsModel->supplier->id);
                        $supplier_goods_count = Goods::select('id')
                            ->whereIn('id', $supplier_goods_id)
                            ->where('status', 1)
                            ->count();
                        $goodsModel->supplier_goods_count = $supplier_goods_count;
                    }
                }
            }
        }


        if ($goodsModel->hasOneShare) {
            $goodsModel->hasOneShare->share_thumb = yz_tomedia($goodsModel->hasOneShare->share_thumb);
        }
        /*
        //设置商品相关插件信息
        $this->setGoodsPluginsRelations($goodsModel);
        */
        //该商品下的推广
        $goodsModel->show_push = $goodsModel->hasOneSale ? SaleGoods::getPushGoods($goodsModel->hasOneSale) : [];
        //销量等于虚拟销量加真实销量
//        $goodsModel->show_sales += $goodsModel->virtual_sales;

        $goodsModel->get_comment = $this->getComment($id);

        $goodsModel->favorable_rate = $this->favorableRate($id);
        return $goodsModel;
    }

    public function getGoods($request, $integrated = null)
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            if (is_null($integrated)) {
                return $this->errorJson('请传入正确参数.');
            } else {
                return show_json(0, '请传入正确参数.');
            }

        }

        $goodsModel = $this->_getGoods($id);
        //设置商品相关插件信息
        $this->setGoodsPluginsRelations($goodsModel);
        //供应商logo转格式
        if (!empty($goodsModel->supplier)) {
            $goodsModel->supplier->logo = yz_tomedia($goodsModel->supplier->logo);
        }
        //默认供应商店铺名称
        if ($goodsModel->supplier->store_name == 'null') {
            $goodsModel->supplier->store_name = $goodsModel->supplier->user_name;
        }

        //判断该商品是否是视频插件商品
        $videoDemand = new VideoDemandCourseGoods();
        $goodsModel->is_course = $videoDemand->isCourse($id);

        //商城租赁
        //TODO 租赁插件是否开启 $lease_switch
        $lease_switch = LeaseToyGoods::whetherEnabled();
        $this->goods_lease_set($goodsModel, $lease_switch);

        //判断是否酒店商品
        $goodsModel->is_hotel = $goodsModel->plugin_id == 33 ? 1 : 0;
        $goodsModel->is_store = $goodsModel->plugin_id == 32 ? 1 : 0;


        if (app('plugins')->isEnabled('label')) {
            $goodsModel->label = '';
            $pic = Setting::get('plugin.label');
            if ($pic['is_label']) {
                $goodsModel->label = $pic;
            }
        }

//        $goodsModel->get_comment = $this->getComment($id);
//
//        $goodsModel->favorable_rate = $this->favorableRate($id);

        //商品服务费
        $this->goodsServiceFree($goodsModel);


        if (is_null($integrated)) {
            return $this->successJson('成功', $goodsModel);
        } else {
            return show_json(1, $goodsModel);
        }

    }

    public function getGoodsPage()
    {
        $is_new_goods = 0;
        if (app('plugins')->isEnabled('designer')) {
            //商品模版
            $view_set = ViewSet::uniacid()->where('type', 'goods')->first();
            if (!empty($view_set) && $view_set->names == '02') {
                $is_new_goods = 1;
            }
        }
        //直播插件
        $this->apiData['is_room'] = (integer)(app('plugins')->isEnabled('room') && Setting::get('plugin.room_set_basic')['is_open_room']);

        //客服设置
        foreach ((new ServiceController())->index() as $k => $v) {
            $this->apiData[$k] = $v;
        }
        if (!is_null(\app\common\modules\shop\ShopConfig::current()->get('customer_service'))) {
            $class = array_get(\app\common\modules\shop\ShopConfig::current()->get('customer_service'), 'class');
            $function = array_get(\app\common\modules\shop\ShopConfig::current()->get('customer_service'), 'function');
            $ret = $class::$function(request()->id, request()->type);
            if ($ret) {
                if (is_array($ret)) {
                    foreach ($ret as $rk => $rv) {
                        $this->apiData[$rk] = $rv;
                    }
                }
            }
        }
        if ($is_new_goods == 1) {
            $newGoodsController = new NewGoodsController();
            $request = request();
            $this->dataIntegrated($newGoodsController->getGoods($request, true), 'get_goods');
            $this->dataIntegrated($newGoodsController->getGoodsType($request, true), 'goods_type');

            $storeId = $this->apiData['get_goods']->store_goods->store_id;

            if ($storeId) {
                if (class_exists('\Yunshop\StoreCashier\frontend\store\NewGoodsController')) {
                    if ($this->apiData['goods_type']['store_id']) {
                        $this->dataIntegrated(\Yunshop\StoreCashier\frontend\store\NewGoodsController::getStoreService($request, true, $this->apiData['goods_type']['store_id']), 'store_service');
                    }
                    $this->dataIntegrated(\Yunshop\StoreCashier\frontend\store\NewGoodsController::getInfobyStoreId($request, true, $storeId), 'get_store_Info');
                    if (MemberService::isLogged()) {
                        $this->dataIntegrated(\Yunshop\StoreCashier\frontend\shoppingCart\MemberCartController::index($request, true, $storeId), 'member_cart');
                    }
                } else {
                    return $this->errorJson('门店插件未开启');
                }
            }

            if ($this->apiData['get_goods']->is_hotel) {
                if (class_exists('\Yunshop\Hotel\frontend\hotel\NewGoodsController')) {
                    $this->dataIntegrated(\Yunshop\Hotel\frontend\hotel\NewGoodsController::getGoodsDetailByGoodsId($request, true), 'get_hotel_info');
                } else {
                    return $this->errorJson('酒店插件未开启');
                }
            }

            if (Setting::get('shop.member')['display_page'] == 1 && Setting::get('shop.member')['level_type'] == 2) {
                $this->apiData['display_page'] = 1;
            } else {
                $this->apiData['display_page'] = 0;
            }

            $this->dataIntegrated($this->pluginEnabled(), 'pluginEnabled');

            return $this->successJson('', $this->apiData);
        } else {
            $request = request();
            $this->dataIntegrated($this->getGoods($request, true), 'get_goods');
            $this->dataIntegrated($this->getGoodsType($request, true), 'goods_type');

            $storeId = $this->apiData['get_goods']->store_goods->store_id;
            if ($storeId) {
                if (class_exists('\Yunshop\StoreCashier\frontend\store\GetStoreInfoController')) {
                    if ($this->apiData['goods_type']['store_id']) {
                        $this->dataIntegrated(\Yunshop\StoreCashier\frontend\store\StoreController::getStoreService($request, true, $this->apiData['goods_type']['store_id']), 'store_service');
                    }
                    $this->dataIntegrated(\Yunshop\StoreCashier\frontend\store\GetStoreInfoController::getInfobyStoreId($request, true, $storeId), 'get_store_Info');
                    $this->dataIntegrated(\Yunshop\StoreCashier\frontend\shoppingCart\MemberCartController::index($request, true, $storeId), 'member_cart');
                } else {
                    return $this->errorJson('门店插件未开启');
                }
            }
            if ($this->apiData['get_goods']->is_hotel) {
                if (class_exists('\Yunshop\Hotel\frontend\hotel\GoodsController')) {
                    $this->dataIntegrated(\Yunshop\Hotel\frontend\hotel\GoodsController::getGoodsDetailByGoodsId($request, true), 'get_hotel_info');
                } else {
                    return $this->errorJson('酒店插件未开启');
                }
            }

            if (app('plugins')->isEnabled('guide-page')) {
                if (class_exists('\Yunshop\GuidePage\frontend\GuidePageController')) {
                    $this->dataIntegrated(\Yunshop\GuidePage\frontend\GuidePageController::index(), 'get_guidepage_info');
                } else {
                    return $this->errorJson('引导页插件未开启');
                }
            }


            if (Setting::get('shop.member')['display_page'] == 1 && Setting::get('shop.member')['level_type'] == 2) {
                $this->apiData['display_page'] = 1;
            } else {
                $this->apiData['display_page'] = 0;
            }

            $this->dataIntegrated(\app\frontend\modules\member\controllers\MemberHistoryController::store($request, true), 'store');
            $this->dataIntegrated(\app\frontend\modules\member\controllers\MemberFavoriteController::isFavorite($request, true), 'is_favorite');

            $this->dataIntegrated($this->pluginEnabled(), 'pluginEnabled');

            return $this->successJson('', $this->apiData);
        }
    }


    /**
     * 判断插件当前是否启用
     * @return array
     */
    protected function pluginEnabled()
    {
        $data['package_deliver_enabled'] = app('plugins')->isEnabled('package-deliver') ? 1 : 0;
        $data['help_center_enabled'] = app('plugins')->isEnabled('help-center') ? 1 : 0;

        return show_json(1, $data);
    }

    /**
     * todo 此方法需要优化，把插件的内容写到插件里面，通过配置文件读取
     * todo 插件关闭前端商城就不应该显示关闭插件的商品
     * @param $request
     * @param null $integrated
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getGoodsType($request, $integrated = null)

    {
        $goods_type = 'goods';//通用
        $id = request()->id;
        if (!$id) {
            if (is_null($integrated)) {
                return $this->errorJson('请传入正确参数.');
            } else {
                return show_json(0, '请传入正确参数.');
            }

        }

        $goodsModel = Goods::uniacid()->find($id);

        $data['title'] = $goodsModel->title;
        // 商品详情挂件
        if (\app\common\modules\shop\ShopConfig::current()->get('goods_detail')) {
            foreach (\app\common\modules\shop\ShopConfig::current()->get('goods_detail') as $key_name => $row) {
                $row_res = $row['class']::{$row['function']}($id, true);
                if ($row_res) {
                    $goodsModel->$key_name = $row_res;
                }
            }
        }
        //判断该商品是否是视频插件商品
        $isCourse = (new VideoDemandCourseGoods())->isCourse($id);
        if ($isCourse) {
            $goods_type = 'course';
        }
        //判断是否酒店商品
        if ($goodsModel->plugin_id == 33) {
            $goods_type = 'hotelGoods';
        }

        if ($goodsModel->plugin_id == 66) {
            $goods_type = 'voiceGoods';
        }

        //门店商品
        if ($goodsModel->plugin_id == 32 && $goodsModel->store_goods) {
            $goods_type = 'store_goods';
            $store_id = $goodsModel->store_goods->store_id;
            $data['store_id'] = $store_id;
        }


        //供应商商品
        if ($goodsModel->plugin_id == 92 && $goodsModel->supplier) {
            $goods_type = 'supplierGoods';
        }

        //分期购车插件
        if ($goodsModel->plugin_id == 47) {
            $goods_type = 'staging_buy_car_goods';
        }

        //预约插件
        if ($goodsModel->plugin_id == 101) {
            $goods_type = 'appointment_goods';
        }

        //预约插件
        if ($goodsModel->plugin_id == 101) {
            $goods_type = 'appointment_goods';
        }
        //芸签电子合同插件
        if ($goodsModel->plugin_id == 103) {
            $goods_type = 'yun_sign_goods';
        }
        $data['goods_type'] = $goods_type;

        if (is_null($integrated)) {
            return $this->successJson('成功', $data);
        } else {
            return show_json(1, $data);
        }
    }

    /**
     * @param $goodsModel
     * @param $member
     * @throws \app\common\exceptions\AppException
     */
    public function validatePrivilege($goodsModel, $member)
    {
        Privilege::validatePrivilegeLevel($goodsModel, $member);
        Privilege::validatePrivilegeGroup($goodsModel, $member);
    }

    private function setGoodsPluginsRelations($goods)
    {
        $goodsRelations = app('GoodsManager')->tagged('GoodsRelations');
        collect($goodsRelations)->each(function ($goodsRelation) use ($goods) {
            $goodsRelation->setGoods($goods);
        });
    }

    public function searchGoods()
    {
        $requestSearch = \YunShop::request()->search;

        $order_field = \YunShop::request()->order_field;

        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        if (!in_array($order_field, ['price', 'show_sales', 'comment_num', 'created_at'])) {
            $order_field = 'display_order';
        }
        $order_by = (\YunShop::request()->order_by == 'asc') ? 'asc' : 'desc';

        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item) && $item !== 0 && $item !== "undefined";
            });

            $categorySearch = array_filter(\YunShop::request()->category, function ($item) {
                return !empty($item);
            });

            if ($categorySearch) {
                $requestSearch['category'] = $categorySearch;
            }
        }
        //fixBy-wk-20201030 增加根据商品id获取商品的筛选条件
        $goodIdsSearch = \YunShop::request()->goodIds;
        $where = [];
        if (!empty($goodIdsSearch)) {
            $field = 'id';
            $where = function ($query) use ($field, $goodIdsSearch) {
                $query->whereIn($field, json_decode($goodIdsSearch, true));
            };
        } else {
            //fixBy-wk-20201130 搜索接口过滤关联课程的商品 避免和根据商品id获取商品的筛选条件冲突，两者只能用一个
            $course_goods = DB::table('yz_appletslive_room')
                ->select('id', 'goods_id')
                ->where('type', 1)
                ->where('goods_id', '>', 0)
                ->get()->toArray();
            if (!empty($course_goods)) {
                $goods_ids = array_unique(array_column($course_goods, 'goods_id'));

                $field = 'id';
                $where = function ($query) use ($field, $goods_ids) {
                    $query->whereNotIn($field, $goods_ids);
                };
            }
        }


        $build = $goods_model->Search($requestSearch)->selectRaw("thumb,market_price,price,cost_price,title,short_title,short_thumb, " . DB::getTablePrefix() . "yz_goods.id as goods_id, sales_one_name_url")
            ->where("status", 1)
            ->where($where)
            ->whereInPluginIds();

        //todo 为什么要取出id, 这样mysql where in的好长
        /*$id_arr = collect($list->get())->map(function ($rows) {
            return $rows['id'];
        });*/

        $list = $build->orderBy($order_field, $order_by)
            ->paginate(20)
            ->toArray();

        if ($list['total'] > 0) {
            $data = collect($list['data'])->map(function ($rows) {
                return collect($rows)->map(function ($item, $key) {

                    if ($key == 'thumb' || $key == 'short_thumb' || $key == 'sales_one_name_url') {
                        return yz_tomedia($item);
                    } else {
                        return $item;
                    }
                });
            })->toArray();

            //租赁商品
            //TODO 租赁插件是否开启 $lease_switch
            $lease_switch = LeaseToyGoods::whetherEnabled();
            foreach ($data as &$item) {
                $this->goods_lease_set($item, $lease_switch);
            }

            $list['data'] = $data;
        }

        if (empty($list)) {
            return $this->errorJson('没有找到商品.');
        }

        foreach ($build->get() as $k => $v) {
            /*$rGoods = $goods_model->select()
                ->where('id', $v['goods_id'])
                ->first();*/
            //fixBy-wk-20201005 增加虚拟销量 和 销量字段 real_sales show_sales virtual_sales
            $goodsInfo = DB::table('yz_goods')->select('virtual_sales', 'real_sales', 'show_sales')->where("status", 1)->where('id', $v['goods_id'])->first();
            $list['data'][$k]['virtual_sales'] = $goodsInfo['virtual_sales'];
            $list['data'][$k]['real_sales'] = $goodsInfo['real_sales'];
            $list['data'][$k]['show_sales'] = $goodsInfo['show_sales'];

            $list['data'][$k]['vip_price'] = $v->vip_price;
            $list['data'][$k]['vip_next_price'] = $v->vip_next_price;
            $list['data'][$k]['vip_level_status'] = $v->vip_level_status;
            $list['data'][$k]['price_level'] = $v->price_level;
            $list['data'][$k]['is_open_micro'] = $v->is_open_micro;
        }

        return $this->successJson('成功', $list);
    }

    public function getGoodsCategoryList()
    {
        $category_id = intval(\YunShop::request()->category_id);

        if (empty($category_id)) {
            return $this->errorJson('请输入正确的商品分类.');
        }

        $order_field = \YunShop::request()->order_field;
        if (!in_array($order_field, ['price', 'show_sales', 'comment_num'])) {
            $order_field = 'display_order';
        }

        $order_by = (\YunShop::request()->order_by == 'asc') ? 'asc' : 'desc';

        $categorys = Category::uniacid()->select("name", "thumb", "id")->where(['id' => $category_id])->first();

        if ($categorys) {
            $categorys->thumb = yz_tomedia($categorys->thumb);
        }

        $goodsList = Goods::uniacid()->select('yz_goods.id', 'yz_goods.id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->join('yz_goods_category', 'yz_goods_category.goods_id', '=', 'yz_goods.id')
            ->where("category_id", $category_id)
            ->where('status', '1')
            ->orderBy($order_field, $order_by)
            ->paginate(20)->toArray();


        if (empty($goodsList)) {
            return $this->errorJson('此分类下没有商品.');
        }
        $goodsList['data'] = set_medias($goodsList['data'], 'thumb');

        $categorys->goods = $goodsList;

        return $this->successJson('成功', $categorys);
    }

    public function getGoodsBrandList()
    {
        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        $brand_id = intval(\YunShop::request()->brand_id);
        $order_field = \YunShop::request()->order_field;
        if (!in_array($order_field, ['price', 'show_sales', 'comment_num'])) {
            $order_field = 'display_order';
        }

        $order_by = (\YunShop::request()->order_by == 'asc') ? 'asc' : 'desc';


        if (empty($brand_id)) {
            return $this->errorJson('请输入正确的品牌id.');
        }

        $brand = Brand::uniacid()->select("name", "logo", "id")->where(['id' => $brand_id])->first();

        if (!$brand) {
            return $this->errorJson('没有此品牌.');
        }

        $brand->logo = yz_tomedia($brand->logo);

        $goodsList = $goods_model->uniacid()->select('id', 'id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->where('status', '1')
            ->where('brand_id', $brand_id)
            ->whereInPluginIds()
            ->orderBy($order_field, $order_by)
            ->paginate(20)->toArray();
        $goodsList->vip_level_status;

        if (empty($goodsList)) {
            return $this->errorJson('此品牌下没有商品.');
        }

        $goodsList['data'] = set_medias($goodsList['data'], 'thumb');

        $brand->goods = $goodsList;

        return $this->successJson('成功', $brand);
    }

    public function getRecommendGoods()
    {
        $list = Goods::uniacid()
            ->select('id', 'id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->where('is_recommand', '1')
            ->whereStatus('1')
            ->orderBy('id', 'desc')
            ->get();

        if (!$list->isEmpty()) {
            $list = set_medias($list->toArray(), 'thumb');
        }

        return $this->successJson('获取推荐商品成功', $list);
    }

    /**
     * 会员折扣后的价格
     * @param Goods $goodsModel
     * @param  [type] $discountModel [description]
     * @return array [type]                [description]
     */
    public function getDiscount($goodsModel, $memberModel)
    {
        if ($goodsModel->vip_price === null) {
            return [];
        }
        $discount_switch = Setting::get('shop.member.discount');
        $level_type = Setting::get('shop.member.level_type');
        $display_page = Setting::get('shop.member.display_page');
        if ($memberModel->level) {
            $data = [
                'level_name' => $memberModel->level->level_name,
                'discount_value' => $goodsModel->vip_price,
                'discount' => $discount_switch,
                'next_level_price' => $goodsModel->next_level_price,
                'next_level_name' => $goodsModel->next_level_name,
                'level_type' => $level_type,
                'display_page' => $display_page
            ];
        } else {
            $level = Setting::get('shop.member.level_name');
            $level_name = $level ?: '普通会员';

            $data = [
                'level_name' => $level_name,
                'discount_value' => $goodsModel->vip_price,
                'discount' => $discount_switch,
                'next_level_price' => $goodsModel->next_level_price,
                'next_level_name' => $goodsModel->next_level_name ?: MemberLevel::value('level_name'),
                'level_type' => $level_type,
                'display_page' => $display_page
            ];
        }

        return $data;
    }

    public function getGoodsSaleV2($goodsModel, $member)
    {
        $sale = [];
        //商城积分设置
        $set = \Setting::get('point.set');

        //获取商城设置: 判断 积分、余额 是否有自定义名称
        $shopSet = \Setting::get('shop.shop');


        if ($goodsModel->hasOneSale->ed_num || $goodsModel->hasOneSale->ed_money) {
            $data['name'] = '包邮';
            $data['key'] = 'ed_num';
            $data['type'] = 'array';
            if ($goodsModel->hasOneSale->ed_num) {
                $data['value'][] = '本商品满' . $goodsModel->hasOneSale->ed_num . '件包邮';
            }

            if ($goodsModel->hasOneSale->ed_money) {
                $data['value'][] = '本商品满￥' . $goodsModel->hasOneSale->ed_money . '包邮';

            }
            array_push($sale, $data);
            $data = [];
        }

        if ($goodsModel->hasOneSale->all_point_deduct && $goodsModel->hasOneSale->has_all_point_deduct) {//商品设置
            $data['name'] = $shopSet['credit1'] ? $shopSet['credit1'] . '全额抵扣' : '积分全额抵扣';
            $data['key'] = 'all_point_deduct';
            $data['type'] = 'string';
            $data['value'] = '可使用' . $goodsModel->hasOneSale->all_point_deduct . '个' . ($shopSet['credit1'] ? $shopSet['credit1'] . '全额抵扣购买' : '积分全额抵扣购买');
            array_push($sale, $data);
            $data = [];
        }


        if ((bccomp($goodsModel->hasOneSale->ed_full, 0.00, 2) == 1) && (bccomp($goodsModel->hasOneSale->ed_reduction, 0.00, 2) == 1)) {
            $data['name'] = '满减';
            $data['key'] = 'ed_full';
            $data['type'] = 'string';
            $data['value'] = '本商品满￥' . $goodsModel->hasOneSale->ed_full . '立减￥' . $goodsModel->hasOneSale->ed_reduction;
            array_push($sale, $data);
            $data = [];
        }

        if ($goodsModel->hasOneSale->award_balance) {
            $data['name'] = $shopSet['credit'] ?: '余额';
            $data['key'] = 'award_balance';
            $data['type'] = 'string';
            $data['value'] = '购买赠送' . $goodsModel->hasOneSale->award_balance . $data['name'];
            array_push($sale, $data);
            $data = [];
        }


        $point = [];
        if (app('plugins')->isEnabled('store-cashier')) {//门店抵扣设置
            $store_goods = StoreGoods::where('goods_id', $goodsModel->id)->first();
            $point = StoreSetting::getStoreSettingByStoreId($store_goods->store_id)->where('key', 'point')->first();
        }

        $data['name'] = $shopSet['credit1'] ?: '积分';
        $data['key'] = 'point';
        $data['type'] = 'array';

        if ((strlen($goodsModel->hasOneSale->point) === 0) || $goodsModel->hasOneSale->point != 0) {
            if ($goodsModel->hasOneSale->point) {
                $points = $goodsModel->hasOneSale->point;
            } elseif (!empty($point['value']['set']['give_point']) && $point['value']['set']['give_point'] != 0) {//门店抵扣设置
                $points = $point['value']['set']['give_point'];
            } else {
                $points = $set['give_point'] ? $set['give_point'] : 0;
            }
            if (!empty($points)) {
                $data['value'][] = '购买赠送' . $points . $data['name'];
            }
        }
        //设置不等于0,支持积分抵扣
        //积分抵扣优先级 商品独立设置 ---> 门店设置 ---> 积分统一设置
        if ($set['point_deduct'] && (strlen($goodsModel->hasOneSale->max_point_deduct) === 0 || $goodsModel->hasOneSale->max_point_deduct != 0)) {
            if ($goodsModel->hasOneSale->max_point_deduct) {
                $max_point_deduct = $goodsModel->hasOneSale->max_point_deduct . '元';
            } elseif (!empty($point['value']['set']['money_max']) && $point['value']['set']['money_max'] != 0) {
                $max_point_deduct = $point['value']['set']['money_max'] . '%';
            } else {
                $max_point_deduct = $set['money_max'] ? $set['money_max'] . '%' : 0;
            }

            if (!empty(mb_substr($max_point_deduct, 0, -1))) {
                $data['value'][] = '最高抵扣' . $max_point_deduct;
            }
        }

        if ($set['point_deduct'] && (strlen($goodsModel->hasOneSale->min_point_deduct) === 0 || $goodsModel->hasOneSale->min_point_deduct != 0)) {
            if ($goodsModel->hasOneSale->min_point_deduct) {
                $min_point_deduct = $goodsModel->hasOneSale->min_point_deduct . '元';
            } else {
                $min_point_deduct = $set['money_min'] ? $set['money_min'] . '%' : 0;
            }
            if (!empty(mb_substr($min_point_deduct, 0, -1))) {
                $data['value'][] = '最少抵扣' . $min_point_deduct;
            }
        }


        if (!empty($data['value'])) {
            array_push($sale, $data);
        }
        $data = [];

        if ($goodsModel->hasOneGoodsCoupon->is_give) {
            $data['name'] = '购买返券';
            $data['key'] = 'coupon';
            $data['type'] = 'string';
            $data['value'] = $goodsModel->hasOneGoodsCoupon->send_type ? '商品订单完成返优惠券' : '每月一号返优惠券';
            array_push($sale, $data);
            $data = [];
        }

        //爱心值
        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {

            $love_goods = $this->getLoveSet($goodsModel, $goodsModel->id);

            $data['name'] = $love_goods['name'];
            $data['key'] = 'love';
            $data['type'] = 'array';
            if ($love_goods['deduction']) {
                $data['value'][] = '最高抵扣' . $love_goods['deduction_proportion'] . $data['name'];
            }

            if ($love_goods['award'] && \Setting::get('love.goods_detail_show_love') != 2) {
                $data['value'][] = '购买赠送' . $love_goods['award_proportion'] . $data['name'];
            }

            if (!empty($data['value'])) {
                array_push($sale, $data);
            }
            $data = [];
        }

        //佣金 fixBy-wk-20201005-注释掉   佣金暂时不显示
        $exist_commission = app('plugins')->isEnabled('commission');
        if ($exist_commission) {
            $is_agent = $this->isValidateCommission($member);
            if ($is_agent) {
                $commission_data = (new GoodsDetailService($goodsModel))->getGoodsDetailData();
                if ($commission_data['commission_show'] == 1) {
                    $data['name'] = '分享可赚';
                    $data['key'] = 'commission';
                    $data['type'] = 'array';

                    if (!empty($commission_data['first_commission']) && ($commission_data['commission_show_level'] > 0)) {
                        $data['value'][] = '一级分享可赚' . $commission_data['first_commission'] . '元';
                    }
                    if (!empty($commission_data['second_commission']) && ($commission_data['commission_show_level'] > 1)) {
                        $data['value'][] = '二级分享可赚' . $commission_data['second_commission'] . '元';
                    }
                    if (!empty($commission_data['third_commission']) && ($commission_data['commission_show_level'] > 2)) {
                        $data['value'][] = '三级分享可赚' . $commission_data['third_commission'] . '元';
                    }
                    array_push($sale, $data);
                    $data = [];
                }
            }
        }

        //经销商提成
        $exist_team_dividend = app('plugins')->isEnabled('team-dividend');
        if ($exist_team_dividend) {
            //验证是否是经销商及等级
            $is_agent = $this->isValidateTeamDividend($member);
            if ($is_agent) {

                $lang = \Setting::get('shop.lang');
                $team_dividend_name = array_get($lang, 'zh_cn.team_dividend.title', '经销商') ?: '经销商';
                //返回经销商等级奖励比例  商品等级奖励规则
                $team_dividend_data = (new TeamDividendGoodsDetailService($goodsModel))->getGoodsDetailData();
                if ($team_dividend_data['team_dividend_show'] == 1) {
                    $data['name'] = $team_dividend_name . '提成';
                    $data['key'] = 'team-dividend';
                    $data['type'] = 'array';
                    $data['value'][] = $team_dividend_name . '提成' . $team_dividend_data['team_dividend_royalty'];
                    array_unshift($sale, $data);
                    $data = [];
                }
            }

        }

        $exist_pending_order = app('plugins')->isEnabled('pending-order');
        if ($exist_pending_order) {
            $pending_order_goods = \Yunshop\PendingOrder\services\PendingOrderGoodsService::getGoodsWholesaleSend($goodsModel->id);
            $pending_order['name'] = '批发劵';
            $pending_order['key'] = 'pending-order';
            $pending_order['type'] = 'array';
            if ($pending_order_goods['send_condition']['code']) {
                $pending_order['value'][] = $pending_order_goods['send_condition']['msg'];
                array_push($sale, $pending_order);
            }
        }


        return [
            'sale_count' => count($sale),
//            'first_strip_key' => $sale ? $sale[rand(0, (count($sale) - 1))] : [],
            'first_strip_key' => $sale[0] ? $sale[0] : [],
            'sale' => $sale,
        ];
    }

    public function isValidateCommission($member)
    {
        return Agents::getAgentByMemberId($member->member_id)->first();
    }

    public function isValidateTeamDividend($member)
    {
        return TeamDividendAgencyModel::getAgencyByMemberId($member->member_id)->first();
    }

    /**
     * 商品的营销
     * @param  [type] $goodsModel [description]
     * @return [type]             [description]
     */
    public function getGoodsSale($goodsModel)
    {
        $set = \Setting::get('point.set');

        $shopSet = \Setting::get('shop.shop');

        if (!empty($shopSet['credit1'])) {
            $point_name = $shopSet['credit1'];
        } else {
            $point_name = '积分';
        }

        $data = [
            'first_strip_key' => 0,
            'point_name' => $point_name, //积分名称
            'love_name' => '爱心值',
            'ed_num' => 0,      //满件包邮
            'ed_money' => 0,    //满额包邮
            'ed_full' => 0,      //单品满额
            'ed_reduction' => 0, //单品立减
            'award_balance' => 0, //赠送余额
            'point' => 0,        //赠送积分
            'max_point_deduct' => 0, //积分最大抵扣
            'min_point_deduct' => 0, //积分最小抵扣
            'coupon' => 0,         //商品优惠券赠送
            'deduction_proportion' => 0, //爱心值最高抵扣
            'award_proportion' => 0, //奖励爱心值
            'sale_count' => 0,      //活动总数
        ];


        if (ceil($goodsModel->hasOneSale->ed_full) && ceil($goodsModel->hasOneSale->ed_reduction)) {
            $data['ed_full'] = $goodsModel->hasOneSale->ed_full;
            $data['ed_reduction'] = $goodsModel->hasOneSale->ed_reduction;

            $data['first_strip_key'] = 'ed_full';
            $data['sale_count'] += 1;

        }

        if ($goodsModel->hasOneSale->award_balance) {
            $data['award_balance'] = $goodsModel->hasOneSale->award_balance;

            $data['first_strip_key'] = 'award_balance';
            $data['sale_count'] += 1;

        }

        if ($goodsModel->hasOneSale->point !== '0') {

            $data['point'] = $set['give_point'] ? $set['give_point'] : 0;

            if ($goodsModel->hasOneSale->point) {
                $data['point'] = $goodsModel->hasOneSale->point;
            }

            if (!empty($data['point'])) {
                $data['first_strip_key'] = 'point';
                $data['sale_count'] += 1;
            }

        }

        if ($set['point_deduct'] && $goodsModel->hasOneSale->max_point_deduct !== '0') {

            $data['max_point_deduct'] = $set['money_max'] ? $set['money_max'] . '%' : 0;

            if ($goodsModel->hasOneSale->max_point_deduct) {

                $data['max_point_deduct'] = $goodsModel->hasOneSale->max_point_deduct;
            }
            if (!empty($data['max_point_deduct'])) {
                $data['first_strip_key'] = 'max_point_deduct';
                $data['sale_count'] += 1;
            }
        }
        if ($set['point_deduct'] && $goodsModel->hasOneSale->min_point_deduct !== '0') {

            $data['min_point_deduct'] = $set['money_min'] ? $set['money_min'] . '%' : 0;

            if ($goodsModel->hasOneSale->min_point_deduct) {

                $data['min_point_deduct'] = $goodsModel->hasOneSale->min_point_deduct;
            }
            if (!empty($data['min_point_deduct'])) {
                $data['first_strip_key'] = 'min_point_deduct';
                $data['sale_count'] += 1;
            }
        }
        if ($goodsModel->hasOneGoodsCoupon->is_give) {

            $data['coupon'] = $goodsModel->hasOneGoodsCoupon->send_type ? '商品订单完成返优惠券' : '每月一号返优惠券';

            $data['first_strip_key'] = 'coupon';
            $data['sale_count'] += 1;
        }

        if ($goodsModel->hasOneSale->ed_num) {
            $data['ed_num'] = $goodsModel->hasOneSale->ed_num;

            $data['first_strip_key'] = 'ed_num';
            $data['sale_count'] += 1;
        }

        if ($goodsModel->hasOneSale->ed_money) {
            $data['ed_money'] = $goodsModel->hasOneSale->ed_money;

            $data['first_strip_key'] = 'ed_money';
            $data['sale_count'] += 1;

        }

        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {
            $love_goods = $this->getLoveSet($goodsModel, $goodsModel->id);
            $data['love_name'] = $love_goods['name'];
            if ($love_goods['deduction']) {
                $data['deduction_proportion'] = $love_goods['deduction_proportion'];
                $data['first_strip_key'] = 'deduction_proportion';
                $data['sale_count'] += 1;
            }

            if ($love_goods['award']) {
                $data['award_proportion'] = $love_goods['award_proportion'];
                $data['first_strip_key'] = 'award_proportion';
                $data['sale_count'] += 1;
            }

        }
        $exist_commission = app('plugins')->isEnabled('commission');
        if ($exist_commission) {
            $commission_data = (new GoodsDetailService($goodsModel))->getGoodsDetailData();
            if ($commission_data['commission_show'] == 1) {
                $data['sale_count'] += 1;
                $data['first_strip_key'] = 'commission_show';
            }
            $data = array_merge($data, $commission_data);
        }
        return $data;
    }

    /**
     * 获取商品爱心值设置
     */
    public function getLoveSet($goods, $goods_id)
    {
        $data = [
            'name' => \Setting::get('love.name') ?: '爱心值',
            'deduction' => 0, //是否开启爱心值抵扣 0否，1是
            'deduction_proportion' => 0, //爱心值最高抵扣
            'award' => 0, //是否开启爱心值奖励 0否，1是
            'award_proportion' => 0, //奖励爱心值
        ];

        $love_set = \Setting::get('love');

        $res = app('plugins')->isEnabled('store-cashier');
        if ($res) {//门店抵扣设置
            $store_goods = StoreGoods::where('goods_id', $goods_id)->first();
            $love = StoreSetting::getStoreSettingByStoreId($store_goods->store_id)->where('key', 'love')->first();
            $set = \Setting::get('plugin.store_widgets', 'deduction_proportion');
//            dd($set['love']['deduction_proportion'],$love->value['deduction_proportion']);
        }

        $item = GoodsLove::ofGoodsId($goods->id)->first();
//         dd($item->deduction);
        $deduction = 0;
//            $deduction_proportion = (bccomp($item->deduction_proportion, 0.00, 2) == 1) ? $item->deduction_proportion : \Setting::get('love.deduction_proportion');
        $deduction_proportion = \Setting::get('love.deduction_proportion');


        if ($item->deduction) {//商品独立设置
            if ($love_set['deduction']) {
                $deduction_proportion = $love_set['deduction_proportion'];
                $deduction = $love_set['deduction'];
            }
//            if (!empty($set['love']['deduction'])){//平台设置
//                $deduction_proportion = $set['love']['deduction_proportion'];
//                $deduction = $set['love']['deduction'];
//            }
            // $price = $goods->price * ($deduction_proportion / 100);love[deduction_proportion_low]
            if (!empty($love) && $love->value['deduction_proportion'] && $love->value['deduction_proportion'] != 0) {//门店设置
                $deduction_proportion = $love->value['deduction_proportion'];
                $deduction = $love->value['deduction'];
            }

            if ($item->deduction_proportion && $item->deduction_proportion != 0) {
                $deduction_proportion = $item->deduction_proportion;
                $deduction = $item->deduction;
            }
            $data['deduction'] = $deduction;//$item->deduction;
            $data['deduction_proportion'] = $deduction_proportion . '%';

        }

//            $award_proportion = (bccomp($item->award_proportion, 0.00, 2) == 1) ? $item->award_proportion : \Setting::get('love.award_proportion');
        if ($item->award) {
            $award = $item->award;
            //爱心值插件设置
            $award_proportion = \Setting::get('love.award_proportion');

            //平台设置
//            if (!empty($set)){
//                $award_proportion = $set['love']['award_proportion'];
//                $award = $set['love']['award'];
//            }

            // $award_price = $goods->price * ($award_proportion / 100);
            //门店设置
            if (!empty($love) && $love->value['award_proportion'] && $love->value['award_proportion'] != 0) {
                $award_proportion = $love->value['award_proportion'];
                $award = $love->value['award'];
            }

            //商品独立设置
            if ($item->award_proportion && $item->award_proportion != 0) {
                $award_proportion = $item->award_proportion;//bccomp($item->award_proportion, 0.00, 2);
                $award = $item->award;
            }

            $data['award'] = $love_set['award'] ? $award : 0;//$item->award;
            $data['award_proportion'] = $award_proportion . '%';
        }
//        dd(\Setting::get('love.award_proportion'),$set['love']['award_proportion'],$love->value['award_proportion'],$item->award_proportion,bccomp(66, 0.00, 2));

        return $data;
    }

    /**
     * 是否开启领优惠卷
     * @param $member
     * @return \Illuminate\Http\JsonResponse|int
     */
    public function couponsMemberLj($member)
    {
        if (empty($member)) {
            throw new AppException('没有找到该用户');
        }
        $memberLevel = $member->level_id;

        $now = strtotime('now');
        $coupons = Coupon::getCouponsForMember($member->member_id, $memberLevel, null, $now)
            ->orderBy('display_order', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();
        if ($coupons->isEmpty()) {
            return 0;
        }

        foreach ($coupons as $v) {
            if (($v->total == MemberCouponController::NO_LIMIT) || ($v->has_many_member_coupon_count < $v->total)) {
                return 1;
            }
        }

        return 0;
    }

    private function goods_lease_set(&$goodsModel, $lease_switch)
    {
        if ($lease_switch) {
            //TODO 商品租赁设置 $id
            if (is_array($goodsModel)) {
                $goodsModel['lease_toy'] = LeaseToyGoods::getDate($goodsModel['id']);

            } else {
                $goodsModel->lease_toy = LeaseToyGoods::getDate($goodsModel->id);
            }

        } else {
            if (is_array($goodsModel)) {

                $goodsModel['lease_toy'] = [
                    'is_lease' => $lease_switch,
                    'is_rights' => 0,
                    'immed_goods_id' => 0,
                ];
            } else {
                $goodsModel->lease_toy = [
                    'is_lease' => $lease_switch,
                    'is_rights' => 0,
                    'immed_goods_id' => 0,
                ];
            }
        }
    }

    public function loveShoppingGift($goodsModel)
    {

        //爱心值
        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {
            $love_goods = $this->getLoveSet($goodsModel, $goodsModel->id);

            if ($love_goods['award'] && \Setting::get('love.goods_detail_show_love') == 2) {
                return '购买赠送' . $love_goods['award_proportion'] . $love_goods['name'];
            }
        }

        return '';
    }

    private function goodsServiceFree(&$goodsModel)
    {
        if (app('plugins')->isEnabled('service-fee')) {
            $serviceFee = Setting::get('plugins.service-fee');
            if ($serviceFee['service']['open'] == 1) {
                $serviceFees = ServiceFeeModel::where('goods_id', $goodsModel->id)->first();
                if ($serviceFees->is_open) {
                    $fee = ['name' => $serviceFee['service']['name'], 'money' => $serviceFees->fee];
                    $goodsModel->fee = $fee;
                }
            }
        }
        return;
    }


    public function showPush()
    {
        $id = intval(\YunShop::request()->id);
        $goods = Goods::with('hasOneSale')->find($id);
        $show_push = SaleGoods::getPushGoods($goods->hasOneSale);//SaleGoods::getPushGoods($goodsModel->hasOneSale)
        $data['show_push'] = $show_push ?: [];
        $data['content'] = html_entity_decode($goods->content) ?: '';

        return $this->successJson('返回成功', $data);
    }

    public function getComment($goodsId)
    {
//        $goodsId = \YunShop::request()->goods_id;
        $pageSize = 5;
        $list = Comment::getCommentsByGoods($goodsId)->paginate($pageSize);//

        if ($list) {
            foreach ($list as &$item) {
                $item->reply_count = $item->hasManyReply->count('id');
                $item->head_img_url = $item->head_img_url ? replace_yunshop(yz_tomedia($item->head_img_url)) : yz_tomedia(\Setting::get('shop.shop.logo'));
            }
            //对评论图片进行处理，反序列化并组装完整图片url
            $list = $list->toArray();
            foreach ($list['data'] as &$item) {
                self::unSerializeImage($item);
            }
            return $list;
        }
        return $list;
    }

    /*
    * 获取商品好评率
    */
    public function favorableRate($id)
    {
//        $total = OrderGoods::with('hasOneOrder')->where('goods_id',$id)->sum('id');//总条数

        $total = OrderGoods::with(['hasOneOrder', function ($q) {
            $q->where('status', 3);
        }])->where('goods_id', $id)->count('id');//总条数

        if ($total <= 0) {
            return '100%';
        }
        $level_comment = \app\common\models\Comment::where(['goods_id' => $id])->sum('level');//已评论的分数
        $comment = \app\common\models\Comment::where(['goods_id' => $id])->count('id');//总评论数
        $mark = bcmul($total, 5, 2);//总评分  = 总条数 * 5
        $no_comment = bcmul(bcsub($total, $comment, 2), 5, 2);//未评分 = 总条数 - 已评论条数
        $have_comment = bcmul(bcdiv(bcadd($level_comment, $no_comment, 2), $mark, 2), 100, 2);//最终好评率
        //最终好评率 = （（已评论分数 + 未评分） / 总评分）/100
        return $have_comment . '%';
    }

    // 反序列化图片
    public static function unSerializeImage(&$arrComment)
    {
        $arrComment['images'] = unserialize($arrComment['images']);
        foreach ($arrComment['images'] as &$image) {
            $image = yz_tomedia($image);
        }
        if ($arrComment['append']) {
            foreach ($arrComment['append'] as &$comment) {
                $comment['images'] = unserialize($comment['images']);
                foreach ($comment['images'] as &$image) {
                    $image = yz_tomedia($image);
                }
            }
        }
        if ($arrComment['has_many_reply']) {
            foreach ($arrComment['has_many_reply'] as &$comment) {
                $comment['images'] = unserialize($comment['images']);
                foreach ($comment['images'] as &$image) {
                    $image = yz_tomedia($image);
                }
            }
        }
    }

    //直播间
    public function getRoom()
    {
        $goods_id = intval(request()->goods_id);
        $rooms = Room::select('yz_room.*', 'yz_room_record_file.id as back_id')
            ->where(function ($querys) {
                $querys->whereIn('status', [2, 3])
                    ->orwhere(function ($query) {
                        $query->where('status', 4)
                            ->where('yz_room_record_file.id', '>', 0)
                            ->where('yz_room_record_file.is_show', 1);
                    });
            })
            ->leftJoin('yz_room_record_file', function ($join) {
                $join->on('yz_room_record_file.room_id', '=', 'yz_room.id');
            })
            ->with('hasOneMember')
            ->wherehas('hasManyGoods', function ($query) use ($goods_id) {
                $query->where('goods_id', $goods_id);
            })
            ->orderByRaw("FIELD(status, " . implode(", ", [3, 2, 4]) . ")")
            ->orderBy('yz_room.recommend', 'asc')
            ->orderBy('yz_room_record_file.recommend', 'asc')
            ->orderBy('yz_room.id', 'desc')
            ->paginate(10);
        $room = [];
        foreach ($rooms as $key => $val) {
            $room[$key]['avatar'] = $val->hasOneMember['avatar_image'];
            $room[$key]['nickname'] = $val->hasOneMember['nickname'];
            $room[$key]['id'] = $val->id;
            $room[$key]['status'] = $val->status;
            $room[$key]['title'] = $val->title;
            $room[$key]['cover'] = yz_tomedia($val->cover);
            $room[$key]['banner'] = yz_tomedia($val->banner);
            $room[$key]['live_time'] = $val->live_time;
            $room[$key]['view_num'] = $val->view_count + $val->virtual;
            if (empty($val->goods)) {
                $room[$key]['goods_num'] = 0;
            } else {
                $room[$key]['goods_num'] = count(explode(',', $val->goods));
            }
            if ($val->status == 2) {
                $room[$key]['play_type'] = 3;
            } elseif ($val->status == 3) {
                $room[$key]['play_type'] = 1;
            } else {
                $room[$key]['play_type'] = 2;
                $room[$key]['back_id'] = $val->back_id;
            }
        }
        $json = $rooms->toArray();
        $json['data'] = $room;
        return $this->successJson('成功', $json);
    }

    public function getDiscountGoods()
    {
        $user_id = intval(\YunShop::request()->user_id);
        $list = Goods::uniacid()
            ->select('id', 'id as goods_id', 'title', 'thumb', 'price', 'market_price')
            ->where('is_discount', '1')
            ->whereStatus('1')
            ->orderBy('display_order', 'desc')
            ->get();

        if (!$list->isEmpty()) {
            $list = set_medias($list->toArray(), 'thumb');
        }
        $user_data = Db::table('yz_member')->where(['member_id' => $user_id])->first();
        $data['list'] = $list;
        $data['level'] = $user_data['level_id'];
        return $this->successJson('获取促销商品成功', $data);
    }

    //增加商品详情热度展示 fixby-wk-goodsHotOrders 2020-09-29  2020-10-10  优化显示全部完成订单的20条 20201104 优化为缓存 30分钟后过期
    public function getGoodsHotOrders()
    {
        //Cache 的第二個參數是分鐘 多少分钟会过期
        $list = Cache::remember('good_hot_orders'.\YunShop::app()->uniacid, 30, function () {
            return DB::table('yz_order as o')
                ->join('yz_order_address as p', 'o.id', '=', 'p.order_id')
                ->join('diagnostic_service_user as u', 'o.uid', '=', 'u.ajy_uid')
                ->select('u.avatarurl', 'p.address')
                ->where('o.status', 3)
                ->where('o.uniacid',\YunShop::app()->uniacid)
                ->groupBY('o.uid')
                ->orderBy('o.create_time', 'DESC')
                ->take(20)
                ->get()->toArray();
        });

        return $this->successJson('获取成功', $list);
    }

    //增加获取满额优惠 设置接口 fixby-wk-getEnoughReduce 2020-11-14
    public function getEnoughReduce()
    {

        $enoug_reduce_list = Setting::get('enoughReduce');
        return $this->successJson('获取成功', $enoug_reduce_list);

    }

    //未登录获取商品佣金接口 fixby-wk-getGoodsCommission 2020-11-16
    public function getGoodsCommission()
    {
        $id = intval(\YunShop::request()->id);

        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        //fixby-zhd-商品详情免登陆20201101
        $member_id = \YunShop::app()->getMemberId();

        if ($member_id) {
            try {
                $member = Member::current()->yzMember;
            } catch (MemberNotLoginException  $e) {
                if (\YunShop::request()->type == 1 || \YunShop::request()->type == 2) {
                    return;
                }

                throw new MemberNotLoginException($e->getMessage());
            }
        }

        $goodsModel = $goods_model->uniacid()
            ->with([
                'hasManyParams' => function ($query) {
                    return $query->select('goods_id', 'title', 'value')->orderby('displayorder', 'asc');
                },
                'hasManySpecs' => function ($query) {
                    return $query->select('id', 'goods_id', 'title', 'description');
                },
                'hasManyOptions' => function ($query) {
                    return $query->select('id', 'goods_id', 'title', 'thumb', 'product_price', 'market_price', 'stock', 'specs', 'weight');
                },
                'hasManyDiscount' => function ($query) use ($member) {
                    return $query->where('level_id', $member->level_id);
                },
                'hasOneBrand' => function ($query) {
                    return $query->select('id', 'logo', 'name', 'desc');
                },
                'hasOneShare',
                'hasOneGoodsDispatch',
                'hasOnePrivilege',
                'hasOneSale',
                'hasOneGoodsCoupon',
                'hasOneInvitePage',
                'hasOneGoodsLimitBuy',
                'hasOneGoodsVideo',
            ])
            ->find($id);
        $goodsModel->vip_level_status;
        $exist_commission = app('plugins')->isEnabled('commission');

        if ($exist_commission) {

            if ($member) {
                $is_agent = $this->isValidateCommission($member);
            } else {
                $is_agent = true;
            }

            if ($is_agent) {

                $commission_data = (new GoodsDetailService($goodsModel))->getGoodsDetailData();

                if ($commission_data['commission_show'] == 1) {
                    $data['name'] = '佣金';
                    $data['key'] = 'commission';
                    $data['type'] = 'array';

                    if (!empty($commission_data['first_commission']) && ($commission_data['commission_show_level'] > 0)) {
                        $data['value'][] = '一级佣金' . $commission_data['first_commission'] . '元';
                    }
                    if (!empty($commission_data['second_commission']) && ($commission_data['commission_show_level'] > 1)) {
                        $data['value'][] = '二级佣金' . $commission_data['second_commission'] . '元';
                    }
                    if (!empty($commission_data['third_commission']) && ($commission_data['commission_show_level'] > 2)) {
                        $data['value'][] = '三级佣金' . $commission_data['third_commission'] . '元';
                    }

                }

            }
        }
        return $this->successJson('获取成功', $data);
    }

//购买时验证商品参数接口 fixby-wk-validateGoodsPrivilege 2020-11-16
    public function validateGoodsPrivilege()
    {

        $this->validate([
            'goods_id' => 'required|integer',
            'option_id' => 'integer',
            'total' => 'integer|min:1',
        ]);

        $goods_params = [
            'goods_id' => request()->input('goods_id'),
            'total' => request()->input('total'),
            'option_id' => request()->input('option_id'),
        ];

        $result = new MemberCartCollection();

        $result->push(MemberCartService::newMemberCart($goods_params));
        $res = $result->validate();
        if (!$res) {
            return $this->successJson('验证成功', $res);
        }

    }

    //商品埋点上报 fixby-zhd-2021-1-3
    public function goodsTracking()
    {
        $to_type_id = intval(\YunShop::request()->to_type_id);
        $resource_id = intval(\YunShop::request()->resource_id);
        $goods_id = intval(\Yunshop::request()->goods_id);
        $action = intval(\YunShop::request()->action);
        $val = \YunShop::request()->val;
        $parent_page = \YunShop::request()->parent_page;
        $app_version = \YunShop::request()->app_version;
        $app_type = \YunShop::request()->app_type;

        if (!$to_type_id || !$resource_id || !$goods_id) {

            return $this->errorJson('请传入商品来源参数.');
        }
        if (!$action) {

            return $this->errorJson('请传入操作动作id');
        }

        $goods_ids = explode(',', $goods_id);

        foreach ($goods_ids as $v) {
            $data = array(
                'uniacid' => \YunShop::app()->uniacid,
                'user_id' => \YunShop::app()->getMemberId(),
                'to_type_id' => $to_type_id, //商品推荐来源类型1:穴位 2：病例 （买点已设置，暂无页面）3：文章 4：社区话题 5：体质测试 6：灸师推荐  7:课时推荐  8：直播商品 
                // 9：首页商城' 10：活动商品（限时秒杀/新人礼/穴位查询···） 11:分享（分享好友、朋友圈等）,12:搜索 13：购物车
                'resource_id' => $resource_id, //根据类型id对应穴位、病例、文章、话题、体质、灸师id，课时id， 直播间id，商城id（1底部导航 2：功能区导航），活动海报/轮播/广告位 id，分享者user_id',搜索（1全局搜索 2：商城搜索）
                'goods_id' => $v,
                'action' => $action, //动作类型 1：查看 2、收藏 3、加购 4：下单 5：支付
                'val' => $val, //根据action记录相应参数：加购记录加购商品数 下单记录订单编号
                'parent_page' => html_entity_decode($parent_page),
                'app_version' => $app_version,
                'app_type' => $app_type,
                'create_time' => TIMESTAMP
            );

            $res = DB::table('diagnostic_service_goods_tracking')->insert($data);
            if (!$res) {
                return $this->errorJson('数据插入失败');
            }

            $qyWechatSetting = \Setting::get('plugin.enterprise-wechat');
            if ($qyWechatSetting && $qyWechatSetting['weiban_corpid'] && $qyWechatSetting['weiban_secret']) {
                $this->sendOrderTrack($data);
            }

        }

        return $this->successJson('ok');
    }

    //订单导入
    public function sendOrderTrack($data)
    {
        $member_id = $data['user_id'];
        $goods_id = $data['goods_id'];
        $action = $data['action']; //动作类型 1：查看 2、收藏 3、加购 4：下单 5：支付
        $val = $data['val']; //根据action记录相应参数：加购记录加购商品数 下单记录订单编号
        $user = DB::table('diagnostic_service_user')->select('ajy_uid', 'unionid')->where('ajy_uid', $member_id)->first();
        $goods = DB::table('yz_goods')->select('id', 'title', 'price')->where(['id' => $goods_id])->first();

        $orderData = array(
            "shop_id" => "10820686",     //shop_id	str	否	店铺id，为店铺的唯一编号，若存在店铺数据，需携带此参数用于辨识区分店铺
            "shop_name" => "养居益商城",      //shop_name	str	否	店铺名称
            "item_id" => $goods_id,   //item_id	str	是	商品id
            "item_name" => $goods['title'], //item_name	str	是	商品名称
            "item_price" => $goods['price'],//item_price	str	是	商品价格
            "amount" => 1,//amount	int	是	购买数量
            "payment_amount" => $goods['price'],//payment_amount	int	是	购买总金额
            "order_id" => date('Y-m-d H:i:s') . '-' . $member_id . '-' . $goods_id,//order_id	str	是	订单号
            "order_status" => "浏览",//order_status	str	是	订单状态
            "create_time" => time(),//create_time	int	是	订单创建时间
            "paid_time" => time(),//paid_time	int	是	订单支付时间
            "unionid" => $user['unionid'],//unionid	str	是	客户的unionid
            "order_type" => "用户足迹",//order_type	str	是	订单类型，限制不超过12个字节（英文1字节，汉字2字节）此参数对应侧边栏的订单名称的显示
            //"discount_amount"=>"0.00",//discount_amount	int	否	优惠金额
            //"payment_channel" => "微信",//payment_channel	str	否	支付渠道
            //shop_fields	ShopField[]否	店铺信息自定义字段列表，非店铺基本字段。字段说明见 ShopField数据模型
            /*"shop_fields" => array(
                [
                    "field_name" => "养居益商城",
                    "value" => "营业中",
                ],
                [
                    "field_name" => "店铺评分",
                    "value" => "4.5",
                ]

            ),*/
            //item_fields	ItemField[]	否	商品信息自定义字段列表，非商品的基本字段。字段说明见 ItemField数据模型
            /*"item_fields" => array(
                [
                    "field_name" => "商品描述",
                    "value" => "商品足迹",
                ],
                [
                    "field_name" => "商品类别",
                    "value" => "实体商品",
                ]
            ),*/
            //order_fields	OrderField[]	否	订单信息自定义字段，非订单基本字段列表。字段说明见 OrderField数据模型
            /*"order_fields" => array(
                [
                    "field_name" => "买家手机号",
                    "value" => "13607697385",
                ],

            )*/
        );

        if ($action == 1) {
            $orderData['order_status'] = '查看';
        } elseif ($action == 2) {
            $orderData['order_status'] = '收藏';
        } elseif ($action == 3) {
            $orderData['order_status'] = '加购';
            $orderData['amount'] = intval($val); //加购件数
        } elseif ($action == 4) {
            $orderData['order_status'] = '下单';
        } elseif ($action == 5) {
            $orderData['order_status'] = '支付';
        }

        $res = QyWeiBanService::importOrder($orderData);
        return $res;
    }
}