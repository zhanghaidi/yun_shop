<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/1
 * Time: 下午5:20
 */

namespace Yunshop\Mryt\store\admin;


use app\backend\modules\goods\models\Discount;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Address;
use app\common\models\Street;
use app\common\models\user\UniAccountUser;
use app\common\services\ExportService;
use Yunshop\AreaDividend\models\AreaDividendGoods;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\Commission;
use Yunshop\Love\Common\Models\GoodsLove;
use Yunshop\Merchant\common\models\MerchantGoods;
use Yunshop\Merchant\common\models\MerchantLevel;
use Yunshop\Merchant\common\models\StaffLevel;
use Yunshop\Mryt\models\weiqing\UsersPermission;
use Yunshop\Mryt\models\weiqing\WeiQingUsers;
use Yunshop\Mryt\store\common\controller\CommonController;
use Yunshop\Mryt\store\models\Store;
use Yunshop\Mryt\store\models\StoreCategory;
use Yunshop\SingleReturn\models\SingleReturnGoodsModel;
use Illuminate\Support\Facades\DB;

class StoreController extends CommonController
{
    const EDIT_VIEW         = 'Yunshop\Mryt::store.store_detail';
    const ADD_VIEW                  = 'Yunshop\Mryt::store.cashierset';
    const INDEX_VIEW                = 'Yunshop\Mryt::store.store_list';
    const INDEX_URL                 = 'plugin.mryt.store.admin.store.index';
    const EXPORT_URL                = 'plugin.mryt.store.admin.store.export';
    const EDIT_URL                  = 'plugin.mryt.store.admin.store.edit';
    const BLACK_URL                  = 'plugin.mryt.store.admin.store.black';
    const HIDE_URL                  = 'plugin.mryt.store.admin.store.hide';
    const ADD_URL                   = 'plugin.mryt.store.admin.store.add';
    const STORE_SET_URL             = 'plugin.mryt.store.admin.store.set';
    const DOWNLOAD_URL              = 'plugin.mryt.store.admin.store.downloadQr';
    const STORE_BASE_VIEW           = 'Yunshop\Mryt::store.store.store_base';
    const STORE_SHOW_VIEW           = 'Yunshop\Mryt::store.store.specify_show';
    const CASHIER_SET_VIEW          = 'Yunshop\Mryt::store.cashier.set';
    const CASHIER_SETTLEMENT_VIEW   = 'Yunshop\Mryt::store.cashier.settlement';
    const CASHIER_SALE_VIEW         = 'Yunshop\Mryt::store.cashier.sale';
    const CASHIER_PROFIT_VIEW       = 'Yunshop\Mryt::store.cashier.profit';
    const CASHIER_RETURN_VIEW       = 'Yunshop\Mryt::store.cashier.return';
    const CASHIER_ASSET_VIEW       = 'Yunshop\Mryt::store.asset.return';

    private $storeId = 0;
    private $boss_uid;
    private $store_uid = 0;

    public function index()
    {
        $category_list  = StoreCategory::getList()->get();
        $search = request()->search;
        $list = Store::getList($search)->whereIn('uid', $this->child_ids)->orderBy('id', 'desc')->paginate(10);
        $list = $this->getMap($list);
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view(self::INDEX_VIEW, [
            'list'  => $list,
            'pager' => $pager,
            'category_list' => $category_list,
            'search'        => $search,
        ]);
    }

    public function edit()
    {
        $store_id = request()->store_id;
        $storeModel = Store::getStoreById($store_id)->first();
        $storeModel->salers = unserialize($storeModel->salers);
        $storeData = request()->store;
        if ($storeData) {
            $verify_res = Store::verifyStore((Store::getStoreData($storeModel->hasOneCashier, $storeData)), $storeModel);
            $this->saveStore($verify_res);
            echo $this->successJson('修改成功');exit;
        }
        return view('Yunshop\Mryt::store.store_detail', [
            'store' => $storeModel,
            'store_id' => $store_id
        ]);
    }

    private function saveStore($verify_res)
    {
        if ($verify_res['validator']->fails()) {
            echo $this->errorJson($verify_res['validator']->messages());
            exit;
        } else {
            $verify_res['store_model']->save();
        }
    }

    private function returnView($store_model = null, $write = true)
    {
        $levels = MemberLevel::getMemberLevelList();
        $category_list  = StoreCategory::getList()->get();
        $data = [
            'category_list' => $category_list,
            'levels'        => $levels,
            'exist_plugins' => $this->getPlugins($store_model->cashier_id),
            'asset_list' => app('plugins')->isEnabled('asset') ? (new AssetService())->publishAssetList()  : '' ,
        ];

        if ($store_model) {
            $data['store'] = $store_model;
            $discountValue = array();
            if ($store_model->cashier_id && Discount::getList($store_model->cashier_id)) {
                $discounts = Discount::getList($store_model->cashier_id);
                foreach ($discounts as $key => $discount) {
                    $discountValue[$discount['level_id']] =   $discount['discount_value'];
                }
            }
            $data['discountValue'] = $discountValue;
        }
        $data['route'] = request()->route;
        $data['store_id'] = intval(request()->store_id);
        $data['write'] = $write;
        $data['love_name'] = '爱心值';
        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {
            $love_set = array_pluck(\Setting::getAllByGroup('Love')->toArray(), 'value', 'key');
            if ($love_set['name']) {
                $data['love_name'] = $love_set['name'];
            }
        }
        if($store_model==null){
            $widgets = \Setting::get('plugin.store_widgets');
            $setting = \Setting::get('plugin.store_setting');
            $category_list  = StoreCategory::getList()->get();

            return view(self::ADD_VIEW, [
                'data'    =>     $data,
                'exist_plugins' => $this->getPlugins(),
                'category_list' => $category_list,
                'widgets' =>     $widgets,
                'setting' =>     $setting,
                'write'   =>     $write
            ]);


        }else{
            return view(self::EDIT_VIEW, $data);
        }

    }

    private function getMap($list)
    {
        $list->map(function ($row){
            $province = Address::select()->where('id', $row->province_id)->first();
            $city = Address::select()->where('id', $row->city_id)->first();
            $district = Address::select()->where('id', $row->district_id)->first();
            $street = Street::select()->where('id', $row->street_id)->first();
            $address = $province['areaname'] . $city['areaname'] . $district['areaname'] . $street['areaname'];
            $row->province = $address;
            $uid = $row->uid;
            if ($row->boss_uid) {
                $uid = $row->boss_uid;
            }
            $row->download_url = Store::getQrCodeUrl($row->id,$uid);
        });
        return $list;
    }

    public function export()
    {
        $search = request()->search;
        $builder = Store::getList($search);
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '门店导出';
        $export_data[0] = ['ID', '门店名称', '门店地址', '门店电话', '门店店长', '分类'];
        foreach ($export_model->builder_model as $key => $item) {
            $province = Address::find($item->province_id);
            $city = Address::find($item->city_id);
            $district = Address::find($item->district_id);
            $street = Street::find($item->street_id);
            $address = $province['areaname'] . $city['areaname'] . $district['areaname'] . $street['areaname'] . '-' . $item->address;
            $export_data[$key + 1] = [
                $item->id,
                $item->store_name,
                $address,
                $item->mobile,
                $item->hasOneMember->nickname,
                $item->hasOneCategory->name
            ];
        }
        $export_model->export($file_name, $export_data, \Request::query('route'));
    }

    private function getPlugins($goods_id = '')
    {
        $exist_plugins = [];
        // todo 分销
        $exist_commission = app('plugins')->isEnabled('commission');
        if ($exist_commission) {
            $commission_levels = AgentLevel::getLevels()->get();
            $exist_plugins['commission'] = [
                'commission'    => true,
                'commission_levels' => $commission_levels
            ];
            if ($goods_id) {
                $item = Commission::getModel($goods_id, '');
                $item->rule = unserialize($item->rule);
                $exist_plugins['commission']['commission_goods'] = $item;
            }
        }

        // todo 订单返现
        $exist_single_return = app('plugins')->isEnabled('single-return');
        if ($exist_single_return) {
            $exist_plugins['single-return']['single_return_goods'] = '';
            if ($goods_id) {
                $item = SingleReturnGoodsModel::getModel($goods_id, '');
                $exist_plugins['single-return']['single_return_goods'] = $item;
            }
        }

        // todo 团队分红
        $exist_team_dividend = app('plugins')->isEnabled('team-dividend');
        if ($exist_team_dividend) {
            $exist_plugins['team-dividend']['team_dividend_goods'] = '';
            if ($goods_id) {
                $item = GoodsTeamDividend::getModel($goods_id,'');
                $exist_plugins['team-dividend']['team_dividend_goods'] = $item;
            }
        }

        // todo 区域分红
        $exist_area_dividend = app('plugins')->isEnabled('area-dividend');
        if ($exist_area_dividend) {
            $exist_plugins['area-dividend']['area_dividend_goods'] = '';
            if ($goods_id) {
                $item = AreaDividendGoods::getModel($goods_id,'');
                $exist_plugins['area-dividend']['area_dividend_goods'] = $item;
            }
        }

        // todo 招商
        $exist_merchant = app('plugins')->isEnabled('merchant');
        if ($exist_merchant) {
            $exist_plugins['merchant']['merchant_goods'] = '';
            $exist_plugins['merchant']['merchant_levels'] = [];
            if ($goods_id) {
                $item = MerchantGoods::getModelByGoodsId($goods_id)->first();
                if ($item) {
                    $item = $item->toArray();
                    $item['set'] = unserialize($item['set']);
                }
                $exist_plugins['merchant']['merchant_goods'] = $item;
            }
            // 招商中心等级
            $merchant_levels = MerchantLevel::getLevelList()->get();
            $exist_plugins['merchant']['merchant_levels'] = $merchant_levels;
            // 招商员等级
            $staff_level = StaffLevel::getLevelList()->get();
            $exist_plugins['merchant']['staff_levels'] = $staff_level;
        }

        // todo 爱心值
        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {
            $exist_plugins['love']['love_goods'] = '';
            if ($goods_id) {
                $item = GoodsLove::ofGoodsId($goods_id)->first();
                $exist_plugins['love']['love_goods'] = $item;
            }
        }

        // todo 数字资产
        $exist_asset = app('plugins')->isEnabled('asset');
        if ($exist_asset) {
            $exist_plugins['asset']['asset_goods'] = '';
            if ($goods_id) {
                $item = AssetCashierModel::where('cashier_id',$goods_id)->first();
                $exist_plugins['asset']['asset_goods'] = $item;
            }
        }
        return $exist_plugins;
    }

    public function black()
    {
        $store_id = intval(request()->store_id);
        if (!$store_id) {
            return $this->message('参数错误', Url::absoluteWeb(self::INDEX_URL), 'error');
        }
        $store_model = Store::getStoreById($store_id)->first();
        if (!$store_model) {
            return $this->message(trans('Yunshop\Mryt::pack.common_result_error'), Url::absoluteWeb(self::INDEX_URL), 'error');
        }
        if ($store_model->is_black == 1) {
            $store_model->is_black = 0;
        }else if ($store_model->is_black == 0) {
            $store_model->is_black = 1;
        }
        $store_model->save();
        return $this->message('操作成功', Url::absoluteWeb(self::INDEX_URL));
    }

    public function hide()
    {
        $store_id = intval(request()->store_id);
        if (!$store_id) {
            return $this->message('参数错误', Url::absoluteWeb(self::INDEX_URL), 'error');
        }
        $store_model = Store::getStoreById($store_id)->first();
        if (!$store_model) {
            return $this->message(trans('Yunshop\Mryt::pack.common_result_error'), Url::absoluteWeb(self::INDEX_URL), 'error');
        }
        if ($store_model->is_hide == 1) {
            $store_model->is_hide = 0;
        }else if ($store_model->is_hide == 0) {
            $store_model->is_hide = 1;
        }
        $store_model->save();
        return $this->message('操作成功', Url::absoluteWeb(self::INDEX_URL));
    }

    public function updateStore()
    {
        $list = Store::select('id', 'user_uid')->where('uid', '!=', 0)->get();
        $list->map(function ($store) {
            WeiQingUsers::updateType($store->user_uid);
            $acount_user = UniAccountUser::select()->whereUid($store->user_uid)->first();
            if ($acount_user) {
                $acount_user->role = 'clerk';
                $acount_user->save();
            }
        });
        dd('更改门店数据成功');
        exit;
    }

    public function query()
    {
        // plugin.mryt.store.admin.store.query
        $kwd = \YunShop::request()->keyword;
        if ($kwd) {
            $stores = Store::select('id', 'thumb', 'store_name', 'cashier_id')
                ->where('store_name', 'like', '%' . $kwd . '%')
                ->orWhereHas('hasOneMember', function ($member) use ($kwd) {
                    $member->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                        ->where('realname', 'like', '%' . $kwd . '%')
                        ->orWhere('mobile', 'like', '%' . $kwd . '%')
                        ->orWhere('nickname', 'like', '%' . $kwd . '%');
                })
                ->get();

            return view('Yunshop\Mryt::store.store.query', [
                'stores' => $stores
            ])->render();
        }
    }
}
