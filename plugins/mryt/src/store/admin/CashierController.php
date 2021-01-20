<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/4
 * Time: 下午5:35
 */

namespace Yunshop\Mryt\store\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\member\MemberChildren;
use app\common\models\order\OrderCoupon;
use app\common\models\order\OrderDeduction;
use app\common\models\PayType;
use app\common\services\ExportService;
use Yunshop\FullReturn\common\models\Queue;
use Yunshop\Mryt\admin\model\MemberShopInfo;
use Yunshop\Mryt\store\common\controller\CommonController;
use Yunshop\Mryt\store\models\CashierOrder;
use Yunshop\Mryt\store\models\CashierShopOrder;
use Yunshop\Mryt\store\models\Order;
use Yunshop\Mryt\store\models\GiveCoupon;
use Yunshop\Mryt\store\models\GiveReward;
use Yunshop\Mryt\store\models\Store;
use Yunshop\Mryt\store\models\StoreCategory;
use Yunshop\Mryt\store\models\StoreOrder;
use Yunshop\Mryt\store\models\StoreSetting;
use app\backend\modules\member\models\Member;

class CashierController extends CommonController
{
    const INDEX_VIEW = 'Yunshop\Mryt::store.statistics.cashier';
    const DETAIL_VIEW = 'Yunshop\Mryt::store.statistics.cashier_detail';
    const INDEX_URL = 'plugin.mryt.store.admin.cashier.index';
    const DETAIL_URL = 'plugin.mryt.store.admin.cashier.detail';
    const EXPORT_URL = 'plugin.mryt.store.admin.cashier.export';

    public function index()
    {
        $search = request()->search;
        $time = request()->time;
        if (!$time['time']) {
            $time['time']['start']    = date("Y-m-d H:i:s", time());
            $time['time']['end']      = date("Y-m-d H:i:s", time());
            $time['is_time']          = 0;
        }
        $list = Store::getList($search)->whereIn('uid', $this->child_ids)->paginate(Store::PAGE_SIZE);
        $list = $this->getMap($list, $time);
        $category_list  = StoreCategory::getList()->get();
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view(self::INDEX_VIEW, [
            'list'          => $list,
            'pager'         => $pager,
            'category_list' => $category_list,
            'search'        => $search,
            'time'          => $time,
//            'statistics'    => $this->getShopStatistics()
        ])->render();
    }

    public function detail()
    {
        $store_id = intval(request()->store_id);
        if (empty($store_id)) {
            return $this->message(trans('Yunshop\Mryt::pack.common_param_error'), Url::absoluteWeb(self::INDEX_URL), 'error');
        }
        $cashier_model = Store::getStoreById($store_id)->first();
        if (!$cashier_model) {
            return $this->message(trans('Yunshop\Mryt::pack.common_result_error'), Url::absoluteWeb(self::INDEX_URL), 'error');
        }
        $cashier_model = $this->getItemMap($cashier_model);
        $love_name = '云币';
        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {
            $love_set = array_pluck(\Setting::getAllByGroup('Love')->toArray(), 'value', 'key');
            if ($love_set['name']) {
                $love_name = $love_set['name'];
            }
        }
        return view(self::DETAIL_VIEW, [
            'cashier_model' => $cashier_model,
            'love_name'     => $love_name,
            'exist_love'    => $exist_love
        ])->render();
    }

    public function export()
    {
        $search = request()->search;
        $builder = Store::getList($search);
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($builder, $export_page);
        $file_name = date('Ymdhis', time()) . '收银台统计导出';
        $export_data[0] = ['门店ID', '门店名称', '门店分类', '收银台累计订单金额', '门店累计订单金额', '收银台累计应收款金额', '门店累计应收款金额', '收银台已结算金额', '门店已结算金额', '收银台未结算金额', '门店未结算金额'];
        $model_collect = $this->getMap($export_model->builder_model);
        foreach ($model_collect as $key => $item) {
            $export_data[$key + 1] = [
                $item->id,
                $item->store_name,
                $item->hasOneCategory->name,
                $item->order_price,
                $item->store_order_price,
                $item->receivable_price,
                $item->store_receivable_price,
                $item->finish_withdraw,
                $item->store_finish_withdraw,
                $item->not_withdraw,
                $item->store_not_withdraw
            ];
        }
        $export_model->export($file_name, $export_data, \Request::query('route'));
    }

    private function getMap($list, $time = false)
    {
        $list->map(function($store) use ($time){
            $this->getItemMap($store, $time);
        });
        return $list;
    }

    public function getItemMap($store, $time = false)
    {
        // 累计应收款金额
        // 收银台订单模型
        $cashier_orders = CashierOrder::select()->where('cashier_id', $store->cashier_id)->get();
        // 收银台-累计已完成订单数量
        $store->order_sum = $cashier_orders->sum(function($cashier_order){
            if ($cashier_order->hasOneOrder->status == 3 ) {
                return 1;
            }
        });
        // 门店订单ids
        $s_search = [
            'store' => [
                'store_id' => $store->id
            ]
        ];
        $store->client_sum = MemberShopInfo::where('parent_id', $store->uid)->count();


        $store_orders = \Yunshop\Mryt\store\models\Order::getStoreOrderList([], $s_search)->get();
        $store_order_ids = \Yunshop\Mryt\store\models\Order::getStoreOrderList([], $s_search)->pluck('id');
        // 门店-累计已完成订单数量
        $store->store_order_sum = $store_orders->sum(function($store_order){
            if ($store_order->status == 3 ) {
                return 1;
            }
        });

        // 累计实收款金额
        // 收银台-累计实收款金额
        $store->order_price = $cashier_orders->sum(function($cashier_order){
            if ($cashier_order->hasOneOrder->status == 3 ) {
                return $cashier_order->hasOneOrder->price;
            }
        });
        // 门店-累计实收款金额
        $store->store_order_price = $store_orders->sum(function($store_order){
            if ($store_order->status == 3 ) {
                return $store_order->price;
            }
        });
        // 已结算
        // 收银台-已结算
        $store->finish_withdraw = $cashier_orders->sum(function($cashier_order){
            if ($cashier_order->has_withdraw == 1 && $cashier_order->pay_type_id != PayType::CASH_PAY) {
                return $cashier_order->amount;
            }
        });
        // 门店-已结算
        $store->store_finish_withdraw = StoreOrder::select()->whereIn('order_id', $store_order_ids)->where('has_withdraw', 1)->sum('amount');
        // 未结算

        return $store;
    }

    public function getStoreItemMap($store, $time = false)
    {
        // todo 累计应收款金额
        $common_builder = CashierShopOrder::whereHas('hasOneStoreOrder',function ($query) use ($store) {
            $query->whereStoreId($store->id);
        });
        if ($time['is_time'] == 1) {
            $common_builder->whereBetween('created_at', [strtotime($time['time']['start']), strtotime($time['time']['end'])]);
        }
        $store->receivable_price = $common_builder->where('status', '!=', '-1')->sum('price');

        // todo 累计实收款金额
        $store->order_price = $common_builder->whereStatus('3')->sum('price');
        // todo 已结算
        $store->finish_withdraw = CashierOrder::where('has_withdraw', '1')->where('pay_type_id', '!=', PayType::CASH_PAY)->whereHas('hasOneOrder', function ($order) use ($store, $time) {
            if ($time['is_time'] == 1) {
                $order->where('status', '!=', '-1')->whereBetween('created_at', [strtotime($time['time']['start']), strtotime($time['time']['end'])])->whereHas('hasOneStoreOrder', function($store_order)use($store){
                    $store_order->whereStoreId($store->id);
                });
            } else {
                $order->where('status', '!=', '-1')->whereHas('hasOneStoreOrder', function($store_order)use($store){
                    $store_order->whereStoreId($store->id);
                });
            }
        })->sum('amount');
        // todo 未结算
        $store->not_withdraw = CashierOrder::where('has_withdraw', '0')->where('pay_type_id', '!=', PayType::CASH_PAY)->whereHas('hasOneOrder', function ($order) use ($store, $time) {
            if ($time['is_time'] == 1) {
                $order->where('status', '3')->whereBetween('created_at', [strtotime($time['time']['start']), strtotime($time['time']['end'])])->whereHas('hasOneStoreOrder', function($store_order)use($store){
                    $store_order->whereStoreId($store->id);
                });
            } else {
                $order->where('status', '3')->whereHas('hasOneStoreOrder', function($store_order)use($store){
                    $store_order->whereStoreId($store->id);
                });
            }
        })->sum('amount');

        $cashierOrders = $store->hasManyCashierOrder;
        if ($time['is_time'] == 1) {
            $cashierOrders = $cashierOrders->reject(function($cashierOrder)use($time){
                return $cashierOrder->created_at->timestamp > strtotime($time['time']['end']) || $cashierOrder->created_at->timestamp < strtotime($time['time']['start']);
            });
        }

        if (request()->route == 'plugin.store-cashier.admin.cashier.index') {
            return $store;
        }

        // todo 会员积分奖励数量
        $store->remard_buyer_point = $cashierOrders->sum(function($cashier_order){
            if (!$cashier_order->hasManyGiveReward->isEmpty()) {
                return $cashier_order->hasManyGiveReward->sum(function (
                    $give_reward
                ) {
                    if ($give_reward->is_store == GiveReward::BUYER && $give_reward->reward_model == GiveReward::REWARD_POINT) {
                        return $give_reward->amount;
                    }
                });
            }
        });
        // todo 会员云币奖励数量
        $store->remard_buyer_love = $cashierOrders->sum(function($cashier_order){
            if (!$cashier_order->hasManyGiveReward->isEmpty()) {
                return $cashier_order->hasManyGiveReward->sum(function (
                    $give_reward
                ) {
                    if ($give_reward->is_store == GiveReward::BUYER && $give_reward->reward_model == GiveReward::REWARD_LOVE) {
                        return $give_reward->amount;
                    }
                });
            }
        });
        // todo 商家积分奖励数量
        $store->remard_store_point = $cashierOrders->sum(function($cashier_order){
            if (!$cashier_order->hasManyGiveReward->isEmpty()) {
                return $cashier_order->hasManyGiveReward->sum(function (
                    $give_reward
                ) {
                    if ($give_reward->is_store == GiveReward::STORE && $give_reward->reward_model == GiveReward::REWARD_POINT) {
                        return $give_reward->amount;
                    }
                });
            }
        });
        // todo 商家云币奖励数量
        $store->remard_store_love = $cashierOrders->sum(function($cashier_order){
            if (!$cashier_order->hasManyGiveReward->isEmpty()) {
                return $cashier_order->hasManyGiveReward->sum(function (
                    $give_reward
                ) {
                    if ($give_reward->is_store == GiveReward::STORE && $give_reward->reward_model == GiveReward::REWARD_LOVE) {
                        return $give_reward->amount;
                    }
                });
            }
        });
        // todo 会员优惠券奖励数量
        $store->remard_buyer_coupon = $cashierOrders->sum(function($cashier_order){
            if (!$cashier_order->hasManyGiveCoupon->isEmpty()) {
                return $cashier_order->hasManyGiveCoupon->sum(function (
                    $give_coupon
                ) {
                    return 1;
                });
            }
        });
        // todo 积分抵扣
        $store->deduct_point = $cashierOrders->sum(function($cashier_order){
            if (!$cashier_order->hasManyOrderDeduction->isEmpty()) {
                return $cashier_order->hasManyOrderDeduction->sum(function (
                    $order_deduction
                ) {
                    if (strstr($order_deduction->name, '积分') !== false) {
                        return $order_deduction->amount;
                    }
                });
            }
        });
        // todo 云币抵扣
        $store->deduct_love = $cashierOrders->sum(function($cashier_order){
            if (!$cashier_order->hasManyOrderDeduction->isEmpty()) {
                return $cashier_order->hasManyOrderDeduction->sum(function($order_deduction){
                    if (strpos($order_deduction->name, '爱心') !== false) {
                        return $order_deduction->amount;
                    }
                });
            }
        });
        // todo 优惠券抵扣
        $store->deduct_coupon = $cashierOrders->sum(function($cashier_order){
            if (!$cashier_order->hasManyCoupon->isEmpty()) {
                return $cashier_order->hasManyCoupon->sum('amount');
            }
        });

        return $store;
    }

    private function getShopStatistics()
    {
        $ids = Store::select(['id','cashier_id'])->whereIn('uid', $this->child_ids)->get()->toArray();
        $store_ids = array_column($ids, 'id');
        $store_orders = Order::uniacid()
            ->selectRaw('count(id) as count, sum(price) as price')
            ->whereHas('hasOneStoreOrder', function ($q) use($store_ids) {
                $q->whereIn('store_id', $store_ids);
            })
            ->where('status', 3)
            ->where('plugin_id', 32)
            ->first();

        $cashier_ids = array_column($ids, 'cashier_id');
        $cashier_orders = Order::uniacid()
            ->selectRaw('count(id) as count, sum(price) as price')
            ->whereHas('hasOneCashierOrder', function ($q) use($cashier_ids) {
                $q->whereIn('cashier_id', $cashier_ids);
            })
            ->where('status', 3)
            ->where('plugin_id', 31)
            ->first();
        $count = $store_orders->count + $cashier_orders->count;
        $price = $store_orders->price + $cashier_orders->price;
        $people = MemberShopInfo::uniacid()->whereIn('parent_id', $this->child_ids)->count();
        $cashier_withdraw = CashierOrder::select()->whereIn('cashier_id', $cashier_ids)->where('has_withdraw',1)->sum('amount');
        $store_withdraw = StoreOrder::select()->whereIn('store_id', $store_ids)->where('has_withdraw',1)->sum('amount');
        $income = $cashier_withdraw + $store_withdraw;
        return compact(
            'count',
            'price',
            'income',
            'people'
        );
    }

    /**
     * 推广下线
     *
     * @return mixed
     */
    public function client()
    {
        $request = \YunShop::request();

        $member_info = Member::getUserInfos($request->id)->first();

        if (empty($member_info)) {
            return $this->message('会员不存在','', 'error');
        }

        $list = Member::getAgentInfoByMemberId($request)
            ->paginate($this->pageSize)
            ->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\Mryt::store.client', [
            'member' => $member_info,
            'list'  => $list,
            'pager' => $pager,
            'total' => $list['total'],
            'request' => $request
        ])->render();
    }
}