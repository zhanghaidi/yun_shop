<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-01-06
 * Time: 11:25
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace app\frontend\modules\goods\controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\models\GoodsSpecItem;
use app\common\models\OrderGoods;
use app\common\services\goods\LeaseToyGoods;
use app\common\services\goods\SaleGoods;
use app\common\services\goods\VideoDemandCourseGoods;
use app\frontend\modules\goods\models\Comment;
use app\frontend\modules\goods\models\Goods;
use Yunshop\Love\Common\Models\GoodsLove;
use Yunshop\StoreCashier\common\models\StoreSetting;
use Yunshop\StoreCashier\store\models\StoreGoods;
use Yunshop\Supplier\common\models\SupplierGoods;
use Yunshop\ServiceFee\models\ServiceFeeModel;
use app\frontend\models\Member;
use Yunshop\TeamDividend\models\TeamDividendAgencyModel;
use Yunshop\Commission\models\Agents;
use Yunshop\TeamDividend\Common\Services\TeamDividendGoodsDetailService;
use Yunshop\Commission\Common\Services\GoodsDetailService;
use app\common\models\goods\Privilege;

class NewGoodsController extends BaseController
{
    public function getGoodsPage()
    {
        $request = request();
        $this->dataIntegrated($this->getGoods($request, true),'get_goods');
        $this->dataIntegrated($this->getGoodsType($request, true),'goods_type');

        $storeId = $this->apiData['get_goods']->store_goods->store_id;

        if($storeId){
            if(class_exists('\Yunshop\StoreCashier\frontend\store\NewGoodsController')){
                if($this->apiData['goods_type']['store_id'])
                {
                    $this->dataIntegrated(\Yunshop\StoreCashier\frontend\store\NewGoodsController::getStoreService($request, true,$this->apiData['goods_type']['store_id']),'store_service');
                }
                $this->dataIntegrated(\Yunshop\StoreCashier\frontend\store\NewGoodsController::getInfobyStoreId($request, true,$storeId),'get_store_Info');
            }else{
                return $this->errorJson('门店插件未开启');
            }
        }

        if($this->apiData['get_goods']->is_hotel){
            if(class_exists('\Yunshop\Hotel\frontend\hotel\NewGoodsController')){
                $this->dataIntegrated(\Yunshop\Hotel\frontend\hotel\NewGoodsController::getGoodsDetailByGoodsId($request,true),'get_hotel_info');
            }else{
                return $this->errorJson('酒店插件未开启');
            }
        }

        if(Setting::get('shop.member')['display_page'] == 1 && Setting::get('shop.member')['level_type'] == 2){
            $this->apiData['display_page'] = 1;
        }else{
            $this->apiData['display_page'] = 0;
        }

        $this->dataIntegrated($this->pluginEnabled(),'pluginEnabled');
        return $this->successJson('', $this->apiData);
    }

    public function getGoods($request, $integrated = null)
    {
        $id = intval(\YunShop::request()->id);
        if (!$id) {
            if(is_null($integrated)){
                return $this->errorJson('请传入正确参数.');
            }else{
                return show_json(0,'请传入正确参数.');
            }

        }

        //获取商品模型
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
        $goodsModel->is_store = $goodsModel->plugin_id == 32 ? 1 :0;

        if (app('plugins')->isEnabled('label')){
            $goodsModel->label = '';
            $pic = Setting::get('plugin.label');
            if ($pic['is_label']){
                $goodsModel->label = $pic;
            }
        }

        //商品服务费
        $this->goodsServiceFree($goodsModel);

        if(is_null($integrated)){
            return $this->successJson('成功', $goodsModel);
        }else{
            return show_json(1, $goodsModel);
        }
    }

    public function _getGoods($id, $integrated = null)
    {
        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;

        try {
            $member = Member::current()->yzMember;
        } catch (MemberNotLoginException  $e) {
            if (\YunShop::request()->type == 1) {
                return;
            }

            throw new MemberNotLoginException($e->getMessage());
        }

        $goodsModel = $goods_model->uniacid()
            ->with([
                'hasManyParams' => function ($query) {
                    return $query->select('goods_id', 'title', 'value')->orderby('displayorder','asc');
                },
                'hasManySpecs' => function ($query) {
                    return $query->select('id', 'goods_id', 'title', 'description');
                },
                'hasManyOptions' => function ($query) {
                    return $query->select('id', 'goods_id', 'title', 'thumb', 'product_price', 'market_price', 'stock', 'specs', 'weight');
                },
                'hasOneBrand' => function ($query) {
                    return $query->select('id', 'logo', 'name', 'desc');
                },
                'hasOneShare',
                'hasOneGoodsDispatch',
                'hasOneSale',
                'hasOneGoodsLimitBuy',
                'hasOneGoodsVideo',
            ])
            ->find($id);

        //todo 不知道干嘛用
//        $goodsModel->vip_level_status;

        if (!$goodsModel) {
            if(is_null($integrated)){
                return $this->errorJson('商品不存在.');
            }else{
                return show_json(0,'商品不存在.');
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

        //商品上下架验证
        if (!$goodsModel->status) {
            if(is_null($integrated)){
                return $this->errorJson('商品已下架.');
            }else{
                return show_json(0,'商品已下架.');
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

        //商品内容百度编辑器转换格式
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

        //商品视频处理
        if (!is_null($goodsModel->hasOneGoodsVideo) && $goodsModel->hasOneGoodsVideo->goods_video) {
            $goodsModel->goods_video = yz_tomedia($goodsModel->hasOneGoodsVideo->goods_video);
            $goodsModel->video_image = $goodsModel->hasOneGoodsVideo->video_image ? yz_tomedia($goodsModel->hasOneGoodsVideo->video_image) : yz_tomedia($goodsModel->thumb);
        } else {
            $goodsModel->goods_video = '';
            $goodsModel->video_image = '';
        }

        $goodsModel->goods_sale = $this->getGoodsSaleV2($goodsModel, $member);
        //商品爱心值赠送设置
        $goodsModel->love_shoppin_gift = $this->loveShoppingGift($goodsModel);

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

        //该商品下的推广
        $goodsModel->show_push = $goodsModel->hasOneSale ? SaleGoods::getPushGoods($goodsModel->hasOneSale) : [];

        //商品评论
        $goodsModel->get_comment = $this->getComment($id);

        //商品好评率
        $goodsModel->favorable_rate = $this->favorableRate($id);

        return $goodsModel;
    }

    public function getGoodsType($request, $integrated = null)
    {
        $goods_type = 'goods';//通用
        $id = request()->id;
        if (!$id) {
            if(is_null($integrated)){
                return $this->errorJson('请传入正确参数.');
            }else{
                return show_json(0,'请传入正确参数.');
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

        if ($goodsModel->plugin_id == 66)
        {
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

        $data['goods_type'] = $goods_type;

        if(is_null($integrated)){
            return $this->successJson('成功', $data);
        }else{
            return show_json(1,$data);
        }
    }

    public function loveShoppingGift($goodsModel)
    {
        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {
            $love_goods = $this->getLoveSet($goodsModel, $goodsModel->id);

            if ($love_goods['award'] && \Setting::get('love.goods_detail_show_love') == 2) {
                return  '购买赠送' . $love_goods['award_proportion'] . $love_goods['name'];
            }
        }

        return '';
    }

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
        if ($res){//门店抵扣设置
            $store_goods = StoreGoods::where('goods_id',$goods_id)->first();
            $love = StoreSetting::getStoreSettingByStoreId($store_goods->store_id)->where('key','love')->first();
        }

        $item = GoodsLove::ofGoodsId($goods->id)->first();
        $deduction = 0;
        $deduction_proportion = \Setting::get('love.deduction_proportion');


        if ($item->deduction) {//商品独立设置
            if ($love_set['deduction']){
                $deduction_proportion = $love_set['deduction_proportion'];
                $deduction = $love_set['deduction'];
            }

            if (!empty($love) && $love->value['deduction_proportion'] && $love->value['deduction_proportion'] != 0){//门店设置
                $deduction_proportion = $love->value['deduction_proportion'];
                $deduction = $love->value['deduction'];
            }

            if ($item->deduction_proportion && $item->deduction_proportion != 0){
                $deduction_proportion = $item->deduction_proportion;
                $deduction = $item->deduction;
            }

            $data['deduction'] = $deduction;
            $data['deduction_proportion'] = $deduction_proportion . '%';
        }

        if ($item->award) {
            $award = $item->award;
            //爱心值插件设置
            $award_proportion = \Setting::get('love.award_proportion');

            //门店设置
            if (!empty($love) && $love->value['award_proportion'] && $love->value['award_proportion'] != 0){
                $award_proportion = $love->value['award_proportion'];
                $award = $love->value['award'];
            }

            //商品独立设置
            if ($item->award_proportion && $item->award_proportion != 0){
                $award_proportion = $item->award_proportion;
                $award = $item->award;
            }

            $data['award'] = $award;
            $data['award_proportion'] = $award_proportion . '%';
        }

        return $data;
    }

    public function getComment($goodsId)
    {
        $pageSize = 5;
        $list = Comment::getCommentsByGoods($goodsId)->paginate($pageSize);

        if ($list) {
            foreach ($list as &$item) {
                $item->reply_count = $item->hasManyReply->count('id');
                $item->head_img_url = $item->head_img_url ? replace_yunshop(yz_tomedia($item->head_img_url)) : yz_tomedia(\Setting::get('shop.shop.logo'));
            }

            //对评论图片进行处理，反序列化并组装完整图片url
            $list = $list->toArray();
            foreach ($list['data'] as &$item) {
                // 反序列化图片
                self::unSerializeImage($item);
            }

            return  $list;
        }

        return  $list;
    }

    public function favorableRate($id)
    {
        $total = OrderGoods::with(['hasOneOrder',function($q){
            $q->where('status',3);
        }])->where('goods_id',$id)->count('id');//总条数

        if ($total <= 0){
            return '100%';
        }

        $level_comment = \app\common\models\Comment::where(['goods_id' => $id])->sum('level');//已评论的分数
        $comment = \app\common\models\Comment::where(['goods_id' => $id])->count('id');//总评论数
        $mark = bcmul($total,5,2);//总评分  = 总条数 * 5
        $no_comment = bcmul(bcsub($total,$comment,2) ,5,2);//未评分 = 总条数 - 已评论条数
        $have_comment = bcmul(bcdiv(bcadd($level_comment,$no_comment,2),$mark,2),100,2);//最终好评率

        //最终好评率 = （（已评论分数 + 未评分） / 总评分）/100
        return $have_comment.'%';
    }

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

    public function goodsServiceFree(&$goodsModel)
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

    public function goods_lease_set(&$goodsModel, $lease_switch)
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

    public function setGoodsPluginsRelations($goods)
    {
        $goodsRelations = app('GoodsManager')->tagged('GoodsRelations');
        collect($goodsRelations)->each(function ($goodsRelation) use ($goods) {
            $goodsRelation->setGoods($goods);
        });
    }

    public function pluginEnabled()
    {
        $data['package_deliver_enabled'] = app('plugins')->isEnabled('package-deliver')?1:0;
        $data['help_center_enabled'] = app('plugins')->isEnabled('help-center')?1:0;

        return show_json(1,$data);
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

        if($goodsModel->hasOneSale->all_point_deduct && $goodsModel->hasOneSale->has_all_point_deduct){//商品设置
            $data['name'] = $shopSet['credit1'] ? $shopSet['credit1'].'全额抵扣':'积分全额抵扣';
            $data['key'] = 'all_point_deduct';
            $data['type'] = 'string';
            $data['value'] = '可使用' . $goodsModel->hasOneSale->all_point_deduct .'个'.($shopSet['credit1'] ? $shopSet['credit1'] .'全额抵扣购买' : '积分全额抵扣购买');
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

//        $data['name'] = $shopSet['credit1'] ?: '积分';
//        $data['key'] = 'point';
//        $data['type'] = 'array';
//        if ($goodsModel->hasOneSale->point !== '0') {
//            $point = $set['give_point'] ? $set['give_point'] : 0;
//            if ($goodsModel->hasOneSale->point) {
//                $point = $goodsModel->hasOneSale->point;
//            }
//            if (!empty($point)) {
//                $data['value'][] = '购买赠送' . $point . $data['name'];
//            }
//
//        }
//        dd($goodsModel->hasOneSale);
        $res = app('plugins')->isEnabled('store-cashier');
        if ($res){//门店抵扣设置
            $store_goods = StoreGoods::where('goods_id',$goodsModel->id)->first();
            $point = StoreSetting::getStoreSettingByStoreId($store_goods->store_id)->where('key','point')->first();
//            $discount = StoreSetting::getStoreSettingByStoreId($store_goods->store_id)->where('key','discount')->first();
//            dd($point['value']['set']['money_max']);
        }

        $data['name'] = $shopSet['credit1'] ?: '积分';
        $data['key'] = 'point';
        $data['type'] = 'array';

        if ($set['give_point']){
            $points = $set['give_point'] ? $set['give_point'] : 0;
        }
        if (!empty($point['value']['set']['give_point']) && $point['value']['set']['give_point'] != 0) {//门店抵扣设置
            $points = $point['value']['set']['give_point'];
        }
        if ($goodsModel->hasOneSale->point !== '0') {
//            $points = $set['give_point'] ? $set['give_point'] : 0;
            if ($goodsModel->hasOneSale->point) {
                $points = $goodsModel->hasOneSale->point;
            }
            if (!empty($points)) {
                $data['value'][] = '购买赠送' . $points . $data['name'];
            }
        }


//        if ($set['point_deduct'] ) {//&& $goodsModel->hasOneSale->max_point_deduct !== '0'
//
//            $max_point_deduct = $set['money_max'] ? $set['money_max'] . '%' : 0;
//
//            if (!empty($point['value']['set']['money_max']) && $point['value']['set']['money_max'] != 0){//门店抵扣设置
//                $max_point_deduct = $point['value']['set']['money_max'];
////                $store_goods = StoreGoods::where('goods_id',$goodsModel->id)->first();
////                $store_setting = StoreSetting::getStoreSettingByStoreId($store_goods->store_id)->where('key','point')->first();
////                dd($store_setting['value']['set']['money_max']);
//            }
//
//            if ($goodsModel->hasOneSale->max_point_deduct && $goodsModel->hasOneSale->max_point_deduct != 0) {
//                $max_point_deduct = $goodsModel->hasOneSale->max_point_deduct;
//            }
//            if (!empty($max_point_deduct)) {
//                $data['value'][] = '最高抵扣' . $max_point_deduct . '元';
//            }
//        }
//
//
//        if ($set['point_deduct']){
//            $min_point_deduct = $set['money_min'] ? $set['money_min'] . '%' : 0;
//
////            if (!empty($discount['value']['discount_method']) && $discount['value']['discount_method'] != 0){//门店抵扣设置
////                $min_point_deduct = $discount['value']['discount_method'];
////            }
//
//            if ($goodsModel->hasOneSale->min_point_deduct && $goodsModel->hasOneSale->min_point_deduct != 0) {
//                $min_point_deduct = $goodsModel->hasOneSale->min_point_deduct;
//            }
//
//            if (!empty($min_point_deduct)) {
//                $data['value'][] = '最少抵扣' . $min_point_deduct . '元';
//            }
//        }


        //积分抵扣

//        if ($set['point_deduct'] && $goodsModel->hasOneSale->max_point_deduct != 0) {//&& $goodsModel->hasOneSale->max_point_deduct !== '0'
//            $max_point_deduct = $set['money_max'] ? $set['money_max'] . '%' : 0;
//        }
        if ($set['point_deduct'] && $set['money_max']) {//&& $goodsModel->hasOneSale->max_point_deduct !== '0'
            $max_point_deduct = $set['money_max'] ? $set['money_max'] : 0;
        }

        if (!empty($point['value']['set']['money_max']) && $point['value']['set']['money_max'] != 0){//门店抵扣设置
            $max_point_deduct = $point['value']['set']['money_max'];
        }

        if ($goodsModel->hasOneSale->max_point_deduct && $goodsModel->hasOneSale->max_point_deduct != 0) {
            $max_point_deduct = $goodsModel->hasOneSale->max_point_deduct;
        }
        if (!empty($max_point_deduct) && $max_point_deduct != 0) {
            $data['value'][] = '最高抵扣' . $max_point_deduct . '元';
        }


        if ($set['point_deduct'] && $goodsModel->hasOneSale->min_point_deduct != 0){
            $min_point_deduct = $set['money_min'] ? $set['money_min'] . '%' : 0;

            if ($goodsModel->hasOneSale->min_point_deduct) {
                $min_point_deduct = $goodsModel->hasOneSale->min_point_deduct;
            }

            if (!empty($min_point_deduct) && $min_point_deduct != 0) {
                $data['value'][] = '最少抵扣' . $min_point_deduct . '元';
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

            $love_goods = $this->getLoveSet($goodsModel,$goodsModel->id);

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

        //佣金
        $exist_commission = app('plugins')->isEnabled('commission');
        if ($exist_commission) {
            $is_agent = $this->isValidateCommission($member);
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
                    array_push($sale, $data);
                    $data = [];
                }
            }
        }

        //经销商提成
        $exist_team_dividend = app('plugins')->isEnabled('team-dividend');
        if($exist_team_dividend){
            //验证是否是经销商及等级
            $is_agent = $this->isValidateTeamDividend($member);
            if ($is_agent) {
                //返回经销商等级奖励比例  商品等级奖励规则
                $team_dividend_data = (new TeamDividendGoodsDetailService($goodsModel))->getGoodsDetailData();
                if ($team_dividend_data['team_dividend_show'] == 1) {
                    $data['name'] = '经销商提成';
                    $data['key'] = 'team-dividend';
                    $data['type'] = 'array';
                    $data['value'][] = '经销商提成' . $team_dividend_data['team_dividend_royalty'];
                    array_unshift($sale, $data);
                    $data = [];
                }
            }

        }

        $exist_pending_order = app('plugins')->isEnabled('pending-order');
        if ($exist_pending_order) {
            $pending_order_goods =  \Yunshop\PendingOrder\services\PendingOrderGoodsService::getGoodsWholesaleSend($goodsModel->id);
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
}