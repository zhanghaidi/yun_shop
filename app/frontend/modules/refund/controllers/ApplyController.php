<?php

namespace app\frontend\modules\refund\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\refund\RefundApply;
use app\frontend\models\Order;
use app\frontend\modules\refund\services\RefundService;
use app\frontend\modules\refund\services\RefundMessageService;
use app\frontend\modules\order\services\OrderService; //前端订单service
use Request;
use app\frontend\modules\order\services\MiniMessageService;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/12
 * Time: 下午4:24
 */
class ApplyController extends ApiController
{
    public function index($request)
    {
        $order = \YunShop::request()->order_id;
        $type = \YunShop::request()->type;

        $this->validate([
            'order_id' => 'required|integer'
        ]);

//        if (!empty($type)) {
//            if ($type == 2 && $order) {
//                $order = Order::find($order);
//            } else {
//                $order = Order::find($request->query('order_id'));
//            }
//        } else {
//            $order = Order::find($request->query('order_id'));
//        }
         $order = Order::find($request->input('order_id'));
        if (!isset($order)) {
            throw new AppException('订单不存在');
        }

        $reasons = [
            '不想要了',
            '卖家缺货',
            '拍错了/订单信息错误',
            '其他',
        ];
        $refundTypes = [];
        if ($order->status >= \app\common\models\Order::WAIT_SEND) {
            $refundTypes[] = [
                'name' => '退款(仅退款不退货)',
                'value' => 0
            ];
        }
        if ($order->status >= \app\common\models\Order::WAIT_RECEIVE) {

            $refundTypes[] = [
                'name' => '退款退货',
                'value' => 1
            ];
        }
        if ($order->status >= \app\common\models\Order::COMPLETE) {
            $refundTypes[] = [
                'name' => '换货',
                'value' => 2
            ];
        }

        $data = compact('order', 'refundTypes', 'reasons');
//        dd($data);
        return $this->successJson('成功', $data);
    }


    public function store($request)
    {

        $this->validate([
            'reason' => 'required|string',
            'content' => 'sometimes|string',
            'images' => 'sometimes|filled|json',
            'refund_type' => 'required|integer',
            'order_id' => 'required|integer'
        ], $request, [
            'reason.required' => '退款原因未选择',
            'refund_type.required' => '退款方式未选择',
            'images.json' => 'images非json格式'
        ]);

        $order = Order::find($request->input('order_id'));
        if (!isset($order)) {
            throw new AppException('订单不存在');
        }
        if ($order->uid != \YunShop::app()->getMemberId()) {
            throw new AppException('无效申请,该订单属于其他用户');
        }
        if ($order < Order::WAIT_SEND) {
            throw new AppException('订单未付款,无法退款');
        }

        if (Order::find($request->input('order_id'))->hasOneRefundApply) {
            throw new AppException('申请已提交,处理中');
        }

        $refundApply = new RefundApply($request->only(['reason', 'content', 'refund_type', 'order_id']));
        $refundApply->images = $request->input('images', []);
        $refundApply->content = $request->input('content', '');
        $refundApply->refund_sn = RefundService::createOrderRN();
        $refundApply->create_time = time();
        $refundApply->price = $order->price;

        if (!$refundApply->save()) {
            throw new AppException('请求信息保存失败');
        }
        $order->refund_id = $refundApply->id;
        if (!$order->save()) {
            throw new AppException('订单退款状态改变失败');
        }


        $jst_status = $this->jushuitanSend($request->input('order_id'));
        if(!$jst_status){
            throw new AppException('ERP上报状态失败');
        }
        //通知买家
        RefundMessageService::applyRefundNotice($refundApply);
        RefundMessageService::applyRefundNoticeBuyer($refundApply);
        return $this->successJson('成功', $refundApply->toArray());
    }

    /**
     * fixby-zhd-jushuitanERP订单推送 2020-09-21 18:05
     * @throws AppException
     */
    public function jushuitanSend($order_id)
    {
        $order = Order::with('address', 'hasManyOrderGoods', 'hasOneOrderPay')->find($order_id);

        if (!$order) {
            return false;
        }

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
                'outer_oi_id' => $order->hasOneOrderPay->pay_sn, //string商家系统订单商品明细主键,为了拆单合单时溯源，最长不超过 50,保持唯一 （必传项）
                'properties_value' => $val['goods_option_title'],  //string商品属性；长度<=100 （非必传）
                'refund_status' => 'waiting', //string 非必传 PS：值存在，会自动将订单转异常,success 状态的，发货将不发该商品，不支持单商品发部分; 退款状态:可选 退款中=waiting; 退款成功=success,closed=退款关闭:
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
            'question_desc' => '用户退款', //订单异常描述
            'items' => $items,  //商品明细 （必传项）
        ]);

        $result = OrderService::post($params, 'jushuitan.orders.upload');
        \Log::info('----'.$order->order_sn.'订单退款上传结果：', $result);
        if (!empty($result) && $result['code'] == 0) {
            return true;
        } else {
            return false;

        }
    }
}