<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/3
 * Time: 下午5:33
 */

namespace Yunshop\Mryt\store\admin;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\order\OrderCoupon;
use app\common\models\order\OrderDeduction;
use app\common\models\PayType;
use app\common\services\ExportService;
use app\Jobs\SendMessage;
use Illuminate\Support\Facades\DB;
use Yunshop\Commission\models\Agents;
use Yunshop\Merchant\common\models\Merchant;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\GiveCoupon;
use Yunshop\StoreCashier\common\models\GiveReward;
use Yunshop\StoreCashier\common\models\ShopOrder;
use Yunshop\StoreCashier\common\models\Store;
use Yunshop\StoreCashier\common\models\StoreCategory;
use Yunshop\StoreCashier\common\models\StoreOrder;

class OrderController extends BaseController
{
    const LIST_VIEW = 'Yunshop\Mryt::store.cashier.order.list';
    const DETAIL_VIEW = 'Yunshop\Mryt::store.cashier.order.detail';
    const DETAIL_URL = 'plugin.mryt.store.admin.order.detail';
    const INDEX_URL = 'plugin.mryt.store.admin.order.index';
    const EXPORT_URL = 'plugin.mryt.store.admin.order.export';

    public static $export_param = '';

    public function index()
    {
        $search = request()->search;
        self::$export_param = $search['store']['store_id'] ? $search['store']['store_id'] : '';
        $store = Store::getStoreByCashierId($search['store']['cashier_id'])->first();

        $build = ShopOrder::orders($search);
        if (is_numeric($search['status'])) {
            $build->where('status', $search['status']);
        }
        $build->whereHas('hasOneCashierOrder', function ($cashier_order) use ($search) {
                $cashier_order->with([
                        'hasOneStore' => function ($store) {
                            $store->select('id', 'thumb');
                        }
                    ])
                    ->byOrderSearch($search['store']['cashier_id']);
            });

        $total_price = $build->sum('price');
        $list = $build->orderBy('id', 'desc')->paginate(StoreOrder::PAGE_SIZE);
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view(self::LIST_VIEW, [
            'store' => $store,
            'list'  => $list,
            'pager' => $pager,
            'total_price' => $total_price,
            'search' => $search,
            'is_open_love' => app('plugins')->isEnabled('love'),
            //'identity' => $this->getMemberIdentity($shopOrderBuilder),
            'export_url' => self::EXPORT_URL
        ])->render();
    }

    public function detail()
    {
        $order_id = intval(request()->order_id);
        if (!$order_id) {
            return $this->message(trans('Yunshop\StoreCashier::pack.common_param_error'), Url::absoluteWeb(self::INDEX_URL), 'error');
        }
        $order_model = ShopOrder::orders([])->whereId($order_id)->hasOneCashierOrder([])->first();
        $cashier_order_model = CashierOrder::getCashierOrderByOrderId($order_id)->first();
        if (!$order_model) {
            return $this->message(trans('Yunshop\StoreCashier::pack.common_result_error'), Url::absoluteWeb(self::INDEX_URL), 'error');
        }

        return view(self::DETAIL_VIEW, [
            'store_order' => $order_model,
            'order' => $order_model->toArray(),
            'cashier_order' => $cashier_order_model
        ])->render();
    }

    public static function handle($list, $export_data)
    {
        foreach ($list as $key => $item) {
            if (substr($item->belongsToMember->nickname, 0, strlen('=')) === '=') {
                $item->belongsToMember->nickname = '，' . $item->belongsToMember->nickname;
            }
            $export_data[$key + 1] = [
                $item->hasOneCashierOrder->hasOneStore->store_name,
                $item->order_sn,
                $item->price,
                $item->hasOneCashierOrder->fee_percentage,
                $item->hasOneCashierOrder->amount,
                $item->belongsToMember->nickname,
                $item->pay_type_name,
                $item->status_name,
                $item->create_time,
                !empty(strtotime($item->pay_time))?$item->pay_time:'',
                !empty(strtotime($item->finish_time))?$item->finish_time:'',
            ];
        }
        return $export_data;
    }

    public function export()
    {

        if (request()->export == 1) {
            $search = request()->search;

            $builder = ShopOrder::orders($search)
                ->whereHas('hasOneCashierOrder', function ($cashier_order) use ($search) {
                    $cashier_order->with([
                        'hasOneStore' => function ($store) {
                            $store->select('id', 'thumb');
                        }
                    ])
                        ->byOrderSearch($search['store']['cashier_id']);
                });
//            $builder = ShopOrder::getCashierOrderList($search, $search['store']);

            dispatch(new SendMessage('Yunshop\StoreCashier\common\models\ShopOrder', 'getCashierOrderList', $search));

            $export_page = request()->export_page ? request()->export_page : 1;
            $export_model = new ExportService($builder, $export_page);
            $file_name = date('Ymdhis', time()) . '收银台订单导出';
            $export_data[0] = ['门店名称', '订单编号', '支付单号', '订单金额', '商城提点金额', '提现金额', '会员ID', '购买会员', '支付方式', '订单状态', '下单时间', '付款时间', '完成时间'];

            if ($export_model->builder_model->isEmpty()) {
                return $this->message('导出数据为空', Url::absoluteWeb(self::INDEX_URL), 'error');
            }

            foreach ($export_model->builder_model as $key => $item) {
                if (substr($item->belongsToMember->nickname, 0, strlen('=')) === '=') {
                    $item->belongsToMember->nickname = '，' . $item->belongsToMember->nickname;
                }
                $export_data[$key + 1] = [
                    $item->hasOneCashierOrder->hasOneStore->store_name,
                    $item->order_sn,
                    $item->hasOneOrderPay->pay_sn,
                    $item->price,
                    $item->hasOneCashierOrder->fee_percentage,
                    $item->hasOneCashierOrder->amount,
                    $item->belongsToMember->uid,
                    $item->belongsToMember->nickname,
                    $item->pay_type_name,
                    $item->status_name,
                    $item->create_time,
                    !empty(strtotime($item->pay_time))?$item->pay_time:'',
                    !empty(strtotime($item->finish_time))?$item->finish_time:'',
                ];
            }
            $export_model->export($file_name, $export_data, self::INDEX_URL);
        }
    }

    public function getStatistics()
    {
        $search = request()->search;
        $order_ids = ShopOrder::getCashierOrderList($search, $search['store'])->pluck('id');
        // todo 已提现金额
        $has_settlement = CashierOrder::getListByHasWithdraw(CashierOrder::HAS_WITHDRAW)->whereIn('order_id', $order_ids)->byPayTypeId(PayType::CASH_PAY)->sum('amount');
        // todo 未提现金额
        $no_settlement = CashierOrder::getListByHasWithdraw(CashierOrder::NOT_HAS_WITHDRAW)->whereIn('order_id', $order_ids)->byPayTypeId(PayType::CASH_PAY)->sum('amount');
        // todo 会员积分奖励数量
        $remard_buyer_point = GiveReward::getStatisticByRewardTypeAndByBelongTo(GiveReward::REWARD_POINT, GiveReward::BUYER)->whereIn('order_id', $order_ids)->sum('amount');
        // todo 会员云币奖励数量
        $remard_buyer_love = GiveReward::getStatisticByRewardTypeAndByBelongTo(GiveReward::REWARD_LOVE, GiveReward::BUYER)->whereIn('order_id', $order_ids)->sum('amount');
        // todo 会员优惠券奖励数量
        $remard_buyer_coupon = GiveCoupon::getRemardCoupons()->whereIn('order_id', $order_ids)->count();
        // todo 商家积分奖励数量
        $remard_store_point = GiveReward::getStatisticByRewardTypeAndByBelongTo(GiveReward::REWARD_POINT, GiveReward::STORE)->whereIn('order_id', $order_ids)->sum('amount');
        // todo 商家云币奖励数量
        $remard_store_love = GiveReward::getStatisticByRewardTypeAndByBelongTo(GiveReward::REWARD_LOVE, GiveReward::STORE)->whereIn('order_id', $order_ids)->sum('amount');
        // todo 积分抵扣
        $deduct_point = OrderDeduction::select()->whereIn('order_id', $order_ids)->where('name', 'like', '%积分%')->sum('amount');
        // todo 云币抵扣
        $deduct_love = OrderDeduction::select()->whereIn('order_id', $order_ids)->where('name', 'like', '%爱心%')->sum('amount');
        // todo 优惠券抵扣
        $deduct_coupon = OrderCoupon::select()->whereIn('order_id', $order_ids)->sum('amount');
        echo $this->successJson('成功', compact(
            'has_settlement',
            'no_settlement',
            'remard_buyer_point',
            'remard_buyer_love',
            'remard_buyer_coupon',
            'remard_store_point',
            'remard_store_love',
            'deduct_point',
            'deduct_love',
            'deduct_coupon'
        ));
        exit;
    }

    private function getMemberIdentity($builder)
    {
        $identity = [];
        $uids = array_unique($builder->get()->pluck('uid')->toArray());
        if ($uids) {
            foreach ($uids as $uid) {
                $exist_merchant = app('plugins')->isEnabled('merchant');
                $exist_commission = app('plugins')->isEnabled('commission');
                if ($exist_merchant) {
                    $is_staff = Merchant::getMerchantStaffByMemberId($uid)->first();
                    $is_center = Merchant::getMerchantCenterByMemberId($uid)->first();
                    if ($is_staff) {
                        $identity[$uid]['merchant'] = '招商员';
                    }
                    if ($is_center) {
                        $identity[$uid]['merchant'] = '招商中心';
                    }
                }
                if ($exist_commission) {
                    $is_agent = Agents::getAgentByMemberId($uid)->first();
                    if ($is_agent) {
                        $identity[$uid]['commission'] = '分销商';
                    }
                }
            }
        }
        return $identity;
    }

    public function test()
    {
        $condtion = '';
        $search = request()->search;
        if (array_get($search, 'ambiguous.field', '') && array_get($search, 'ambiguous.string', '')) {
            if ($search['ambiguous']['field'] == 'order') {
                $condtion .= " AND o.order_sn LIKE %".$search['ambiguous']['string']."%";
            }
            if ($search['ambiguous']['field'] == 'member') {
                $condtion .= " AND ((m.uid = ".$search['ambiguous']['string'].") OR (m.realname LIKE %".$search['ambiguous']['string']."%) OR (m.nickname LIKE %".$search['ambiguous']['string']."%) OR (m.mobile LIKE %".$search['ambiguous']['string']."%))";
            }
        }
        if ($search['store']['store_name']) {
            $condtion .= " AND s.store_name LIKE %".$search['store']['store_name']."%";
        }
        if ($search['store']['member']) {
            $condtion .= " AND s.store_name LIKE %".$search['store']['store_name']."%";
        }


        $count = pdo_fetchcolumn("SELECT count(1) FROM " . tablename('yz_order') . ' o LEFT JOIN ' .
            tablename('yz_plugin_cashier_order') . ' co ON co.order_id = o.id LEFT JOIN ' .
            tablename('yz_store') . ' s ON s.cashier_id = co.cashier_id LEFT JOIN ' .
            tablename('mc_members') . ' m ON m.uid = o.uid ' .
            ' WHERE o.uniacid = ' . \YunShop::app()->uniacid . ' AND o.plugin_id = 31');
        $pagesize = ceil($count/5000);
        $header = array(
            'store_name' => '门店名称', 'order_sn' => '订单编号', 'price' => '订单金额', 'fee_percentage' => '商城提点金额', 'amount' => '提现金额', 'member' => '购买会员', 'pay_type_name' => '支付方式', 'status_name' => '订单状态', 'create_time' => '下单时间', 'pay_time' => '付款时间', 'finish_time' => '完成时间'
        );
        $keys = array_keys($header);
        $html = "\xEF\xBB\xBF";
        foreach ($header as $li) {
            $html .= $li . "\t ,";
        }
        $html .= "\n";
        for ($j = 1; $j <= $pagesize; $j++) {
            $list = pdo_fetchall(
                "SELECT s.store_name, o.order_sn, o.price, co.fee_percentage, co.amount, o.pay_type_id, o.status, o.create_time, o.pay_time, o.finish_time, m.nickname FROM " . tablename('yz_order') . ' o LEFT JOIN ' .
                tablename('yz_plugin_cashier_order') . ' co ON co.order_id = o.id LEFT JOIN ' .
                tablename('yz_store') . ' s ON s.cashier_id = co.cashier_id  LEFT JOIN ' .
                tablename('mc_members') . ' m ON m.uid = o.uid ' .
                ' WHERE o.uniacid = ' . \YunShop::app()->uniacid  . ' AND o.plugin_id = 31 ORDER BY o.id  limit ' . ($j - 1) * 5000 . ', 5000'
            );
            if (!empty($list)) {
                $size = ceil(count($list) / 500);
                for ($i = 0; $i < $size; $i++) {
                    $buffer = array_slice($list, $i * 500, 500);
                    foreach ($buffer as $row) {
                        $row['pay_type_name'] = '啊实打';
                        $row['status_name'] = '阿萨德';
                        $row['create_time'] = date('Y-m-d H:i:s',
                            $row['created_at']);
                        $row['pay_time'] = date('Y-m-d H:i:s',
                            $row['pay_time']);
                        $row['finish_time'] = date('Y-m-d H:i:s',
                            $row['finish_time']);
                        foreach ($keys as $key) {
                            $data[] = $row[$key];
                        }
                        $user[] = implode("\t ,", $data) . "\t ,";
                        unset($data);
                    }
                }
            }
        }
        $html .= implode("\n", $user);
        header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename=收银台订单.csv");
        echo $html;
        exit();
    }
}