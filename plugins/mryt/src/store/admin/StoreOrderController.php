<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/3
 * Time: 下午5:33
 */

namespace Yunshop\Mryt\store\admin;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\PaginationHelper;
use app\common\models\order\OrderCoupon;
use app\common\models\order\OrderDeduction;
use app\common\services\ExportService;
use Yunshop\Mryt\store\models\GiveReward;
use Yunshop\Mryt\store\models\GiveCoupon;
use Yunshop\Mryt\store\models\Order;
use Yunshop\Mryt\store\models\StoreOrder;

class StoreOrderController extends BaseController
{
    const LIST_VIEW = 'Yunshop\Mryt::store.order.list';
    const DETAIL_VIEW = 'Yunshop\Mryt::store.order.detail';
    const LIST_URL = 'plugin.mryt.store.admin..store-order.index';

    const PAGE_SIZE = 20;
    protected $orderModel;
    public function __construct()
    {
        parent::__construct();
        $shopOrderSearch = request()->shop_order_search;
        $storeOrderSearch = request()->store_order_search ? request()->store_order_search : [];
        $storeOrderSearch['store']['clerk_id'] = request()->clerk_id;
        $this->orderModel = Order::getStoreOrderList($shopOrderSearch, $storeOrderSearch);
    }

    public function index()
    {
        $this->export($this->orderModel);
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    public function waitPay()
    {
        $this->orderModel->waitPay();
        $this->export($this->orderModel->waitPay());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    public function waitSend()
    {

        $this->orderModel->waitSend();
        $this->export($this->orderModel->waitSend());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    public function waitReceive()
    {
        $this->orderModel->waitReceive();
        $this->export($this->orderModel->waitReceive());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    public function completed()
    {

        $this->orderModel->completed();
        $this->export($this->orderModel->completed());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    public function cancelled()
    {
        $this->orderModel->cancelled();
        $this->export($this->orderModel->cancelled());
        return view(self::LIST_VIEW, $this->getData())->render();
    }

    protected function getData()
    {
        $shopOrderSearch = request()->shop_order_search;
        if ($shopOrderSearch) {
            $shopOrderSearch = array_filter($shopOrderSearch, function ($item) {
                return !empty($item);
            });
        }
        $storeOrderSearch = request()->store_order_search;

        $list['total_price'] = $this->orderModel->sum('price');
        $list += $this->orderModel->orderBy('id', 'desc')->paginate(self::PAGE_SIZE)->appends(['button_models'])->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        $data = [
            'list' => $list,
            'total_price' => $list['total_price'],
            'pager' => $pager,
            'shopOrderSearch' => $shopOrderSearch,
            'storeOrderSearch' => $storeOrderSearch,
            'var' => \YunShop::app()->get(),
            'url' => \Request::query('route'),
            'include_ops' => 'order.ops',
            'detail_url' => 'order.detail'
        ];
//        dd($data);
        return $data;
    }

    public function export($orders)
    {
        if (request()->export == 1) {
            $export_page = request()->export_page ? request()->export_page : 1;
            $orders = $orders->with(['discounts']);
            $export_model = new ExportService($orders, $export_page);
            if ($export_model->builder_model->isEmpty()) {
                throw new ShopException('没有可导出订单');
            }
            if (!$export_model->builder_model->isEmpty()) {
                $file_name = date('Ymdhis', time()) . '订单导出';//返现记录导出
                $export_data[0] = $this->getColumns();
                foreach ($export_model->builder_model->toArray() as $key => $item) {
                    $address = explode(' ', $item['address']['address']);

                    $export_data[$key + 1] = [
                        $item['order_sn'],
                        $item['has_one_order_pay']['pay_sn'],
                        $this->getNickname($item['belongs_to_member']['uid']),
                        $this->getNickname($item['belongs_to_member']['nickname']),
                        $item['address']['realname'],
                        $item['address']['mobile'],
                        !empty($address[0])?$address[0]:'',
                        !empty($address[1])?$address[1]:'',
                        !empty($address[2])?$address[2]:'',
                        $item['address']['address'],
                        $this->getGoods($item, 'goods_title'),
                        $this->getGoods($item, 'goods_sn'),
                        $this->getGoods($item, 'total'),
                        $item['pay_type_name'],
                        $this->getExportDiscount($item, 'deduction'),
                        $this->getExportDiscount($item, 'coupon'),
                        $this->getExportDiscount($item, 'enoughReduce'),
                        $this->getExportDiscount($item, 'singleEnoughReduce'),
                        $item['goods_price'],
                        $item['dispatch_price'],
                        $item['price'],
                        $item['status_name'],
                        $item['create_time'],
                        !empty(strtotime($item['pay_time']))?$item['pay_time']:'',
                        !empty(strtotime($item['send_time']))?$item['send_time']:'',
                        !empty(strtotime($item['finish_time']))?$item['finish_time']:'',
                        $item['express']['express_company_name'],
                        $item['express']['express_sn'],
                        $item['has_one_order_remark']['remark'],
                    ];
                }
                $export_model->export($file_name, $export_data, 'order.list.index');
            }
        }
    }

    private function getColumns()
    {
        return ["订单编号", "支付单号", "粉丝ID", "粉丝昵称", "会员姓名", "联系电话", '省', '市', '区', "收货地址", "商品名称", "商品编码", "商品数量", "支付方式", '抵扣金额', '优惠券优惠', '全场满减优惠', '单品满减优惠', "商品小计", "运费", "应收款", "状态", "下单时间", "付款时间", "发货时间", "完成时间", "快递公司", "快递单号", "订单备注"];
    }

    protected function getExportDiscount($order, $key)
    {
        $export_discount = [
            'deduction' => 0,    //抵扣金额
            'coupon'    => 0,    //优惠券优惠
            'enoughReduce' => 0,  //全场满减优惠
            'singleEnoughReduce' => 0,    //单品满减优惠
        ];

        foreach ($order['discounts'] as $discount) {

            if ($discount['discount_code'] == $key) {
                $export_discount[$key] = $discount['amount'];
            }
        }

        return $export_discount[$key];

    }


    private function getGoods($order, $key)
    {
        $goods_title = '';
        $goods_sn = '';
        $total = '';
        foreach ($order['has_many_order_goods'] as $goods) {
            $res_title = $goods['title'];
            $res_title = str_replace('-', '，', $res_title);
            $res_title = str_replace('+', '，', $res_title);
            $res_title = str_replace('/', '，', $res_title);
            $res_title = str_replace('*', '，', $res_title);
            $res_title = str_replace('=', '，', $res_title);

            $goods_title .= $res_title . '，';
            $goods_sn .= $goods['goods_sn'].'/';
            $total .= $goods['total'].'/';
        }
        $res = [
            'goods_title' => $goods_title,
            'goods_sn' => $goods_sn,
            'total' => $total
        ];
        return $res[$key];
    }

    private function getNickname($nickname)
    {
        if (substr($nickname, 0, strlen('=')) === '=') {
            $nickname = '，' . $nickname;
        }
        return $nickname;
    }

    public function detail()
    {
        $orderId = request()->id;
        $order = Order::getOrderDetailById($orderId);
        if (!$order) {
            throw new ShopException('未找到该订单');
        }
        if(!empty($order->express)){
            $express = $order->express->getExpress($order->express->express_code, $order->express->express_sn);
            $dispatch['express_sn'] = $order->express->express_sn;
            $dispatch['company_name'] = $order->express->express_company_name;
            $dispatch['data'] = $express['data'];
            $dispatch['thumb'] = $order->hasManyOrderGoods[0]->thumb;
            $dispatch['tel'] = '95533';
            $dispatch['status_name'] = $express['status_name'];
        }

        return view(self::DETAIL_VIEW, [
            'order'         => $order ? $order->toArray() : [],
            'dispatch'      => $dispatch,
            'var'           => \YunShop::app()->get(),
            'ops'           => 'order.ops'
        ])->render();
    }

    public function getStatistics()
    {
        $order_ids = Order::getStoreOrderList([], [])->pluck('id');
        // todo 已提现金额
        $has_settlement = StoreOrder::select()->where('has_withdraw', 1)->whereIn('order_id', $order_ids)->sum('amount');
        // todo 未提现金额
        $no_settlement = StoreOrder::select()->where('has_withdraw', 0)->whereIn('order_id', $order_ids)->sum('amount');
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
}