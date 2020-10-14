<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/21
 * Time: 下午6:16
 */

namespace app\backend\modules\refund\controllers;

use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\models\Order;
use app\common\modules\refund\services\RefundService;
use app\backend\modules\refund\services\RefundMessageService;
use app\backend\modules\refund\models\RefundApply;
use Illuminate\Support\Facades\DB;
use app\frontend\modules\order\services\OrderService;



class PayController extends BaseController
{
    private $refundApply;   
    public $transactionActions = [];

    public function preAction()
    {
        parent::preAction();
        $request = \Request::capture();
        $this->validate([
            'refund_id' => 'required',
        ]);
        $this->refundApply = RefundApply::find($request->input('refund_id'));
        if (!isset($this->refundApply)) {
            throw new AdminException('退款记录不存在');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $request = request()->input();

        $this->validate([
            'refund_id' => 'required'
        ]);


        /**
         * fixby-ly-jushuitan后台退款操作-2020-07-21
         * date:2020 07 21
         * 小程序消息推送添加
         * 用户申请退货，发送聚水潭
         */
        $refund_order = Db::table('yz_order_refund')->where(['id' => $request['refund_id']])->first();
        if(!empty($refund_order)){
            $order = Order::with('address', 'hasManyOrderGoods', 'hasOneOrderPay')->find($refund_order['order_id']);

            if($order['status']==1 && $order['jushuitan_status']==1){

                $address = explode(" ", $order->address->address);
                $goods = $order->hasManyOrderGoods->toArray();
                $items = [];
                foreach ($goods as $k => $val) {
                    $items[] = [
                        //'shop_sku_id' => 'SKU A1',
                        'sku_id' => $val['goods_sn'],   //ERP内商品编码 长度<=40 （必传项）
                        'shop_sku_id' => $val['goods_sn'],      //店铺商品编码 长度<=128 （必传项）
                        //'i_id' => '',  //ERP内款号/货号 长度<=40
                        'amount' => floatval($val['goods_price']), //decimal应付金额，保留两位小数，单位（元）；备注：可能存在人工改价 （必传项）
                        'base_price' => floatval($val['goods_price']), //decimal基本价（拍下价格），保留两位小数，单位（元） （必传项）
                        'qty' => intval($val['total']), //int数量 （必传项）
                        'name' => $val['title'], //string商品名称 长度<=100 （必传项）
                        'outer_oi_id' => strval($val['id']), //string商家系统订单商品明细主键,为了拆单合单时溯源，最长不超过 50,保持唯一 （必传项）
                        'properties_value' => $val['goods_option_title'],  //string商品属性；长度<=100 （非必传）
                        'refund_status' => 'success', //string 非必传 PS：值存在，会自动将订单转异常,success 状态的，发货将不发该商品，不支持单商品发部分; 退款状态:可选 退款中=waiting; 退款成功=success,closed=退款关闭:
                        'refund_qty' => intval($val['total'])  //退货数量
                    ];
                }
                $params = array([
                    'pay' => [
                        'outer_pay_id' => $order->hasOneOrderPay->pay_sn,//string 外部支付单号，最大50 （必传项）$order->order_sn,
                        'pay_date' => $order->pay_time->toDateTimeString(), //string支付日期 （必传项）
                        'amount' => floatval($order->price), //decimal支付金额 （必传项）
                        'payment' => $order->hasOneOrderPay->pay_type_name, //string支付方式，最大20 （必传项）
                        'seller_account' => $order->address->mobile, //string卖家支付账号，最大 50 （必传项）
                        'buyer_account' => $order->shop_name //string买家支付账号，最大 200 （必传项）
                    ],
                    'shop_id' => 10820686, //int店铺编号 （必传项）
                    'so_id' => $order->order_sn,  //string订单编号 （必传项）
                    'order_date' => $order->pay_time->toDateTimeString(),//stringCarbon::$order->create_time, //订单日期 （必传项）
                    'shop_status' => 'WAIT_SELLER_SEND_GOODS',  //string（必传项）订单：等待买家付款=WAIT_BUYER_PAY，等待卖家发货=WAIT_SELLER_SEND_GOODS,等待买家确认收货=WAIT_BUYER_CONFIRM_GOODS, 交易成功=TRADE_FINISHED, 付款后交易关闭=TRADE_CLOSED,付款前交易关闭=TRADE_CLOSED_BY_TAOBAO；发货前可更新
                    'shop_buyer_id' => $order->address->mobile, //string买家帐号 长度 <= 50 （必传项）
                    'receiver_state' => $address[0], //string收货省份 长度 <= 50；发货前可更新 （必传项）
                    'receiver_city' => $address[1], //string收货市 长度<=50；发货前可更新 （必传项）
                    'receiver_district' => $address[2], //string收货区/街道 长度<=50；发货前可更新 （必传项）
                    'receiver_address' => $address[3], //string收货地址 长度<=200；发货前可更新 （必传项）
                    'receiver_name' => $order->address->realname, //string收件人 长度<=50；发货前可更新 （必传项）
                    'receiver_phone' => $order->address->mobile, //string联系电话 长度<=50；发货前可更新 （必传项）
                    'receiver_mobile' => $order->address->mobile, //string 手机号
                    'pay_amount' => floatval($order->price),  //decimal应付金额，保留两位小数，单位元） （必传项）
                    'freight' => floatval($order->dispatch_price),    //decimal运费 （必传项）
                    'shop_modified' => date('Y-m-d H:i:s', time()), //string订单修改日期 （必传项）
                    'buyer_message' => $order->note, //string买家留言 长度<=400；可更新 （非必传）
                    'remark' => $order->hasOneOrderRemark->remark, //string 卖家备注 长度<=150；可更新
                    'question_desc' => '用户退款', //订单异常描述
                    'items' => $items,  //商品明细 （必传项）
                ]);

                $result = OrderService::post('退款处理上报',$params, 'jushuitan.orders.upload');

                if (empty($result) || $result['code'] != 0) {
                    throw new ShopException('退款失败！');
                }
            }
            OrderService::orderMess($order['order_sn'],$order,2);
        }

        /**
         * @var $this ->refundApply RefundApply
         */
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $result = (new RefundService)->pay($request['refund_id']);
            if (!$result) {
                throw new ShopException('操作失败');
            }

            return $result;
        });

        if (is_string($result)) {
            redirect($result)->send();
        }

        if (is_array($result) && isset($result['action']) && isset($result['input'])) {
           echo $this->formPost($result);exit();
        }

        RefundMessageService::passMessage($this->refundApply);//通知买家
        return $this->message('操作成功');

    }


    /**
     * 表单POST请求
     * @param $trxCode
     * @param $data
     */
    public function formPost($data)
    {

        $echo = "<form style='display:none;' id='form1' name='form1' method='post' action='" . $data['action']."'>";
        foreach ($data['input'] as $k => $v) {
            $echo .= "<input name='{$k}' type='text' value='{$v}' />";
        }
        $echo .= "</form>";
        $echo .= "<script type='text/javascript'>function load_submit(){document.form1.submit()}load_submit();</script>";

        echo $echo;
    }

}