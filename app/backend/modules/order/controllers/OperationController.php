<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午2:30
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\common\models\Order;
use app\common\models\PayType;
use app\frontend\modules\order\services\OrderService;
use app\common\models\order\Remark;
use app\common\exceptions\AppException;

class OperationController extends BaseController
{
    protected $param;
    /**
     * @var Order
     */
    protected $order;
    public $transactionActions = ['*'];

    public function preAction()
    {
        parent::preAction();

        $this->param = request()->input();

        if (!isset($this->param['order_id'])) {
            return $this->message('order_id不能为空!', '', 'error');

        }
        $this->order = Order::find($this->param['order_id']);
        if (!isset($this->order)) {
            return $this->message('未找到该订单!', '', 'error');

        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function pay()
    {
        $this->order->backendPay();
        return $this->successJson();

    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function cancelPay()
    {
        OrderService::orderCancelPay($this->param);

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function send()
    {
        OrderService::orderSend($this->param);
        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function fClose()
    {
        $this->order->refund();
        return $this->message('强制退款成功');

    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function cancelSend()
    {
        OrderService::orderCancelSend($this->param);

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function receive()
    {
        OrderService::orderReceive($this->param);

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function close()
    {
        OrderService::orderClose($this->param);

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function manualRefund()
    {
        if ($this->order->isPending()) {
            throw new AppException("订单已锁定,无法继续操作");
        }
        $result = $this->order->refund();
        if (isset($result['url'])) {
            return redirect($result['url'])->send();
        }

        return $this->message('操作成功');
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function delete()
    {
        OrderService::orderDelete($this->param);

        return $this->message('操作成功');
    }

    public function remarks()
    {
        $order = Order::find(request()->input('order_id'));
        if (!$order) {
            throw new AppException("未找到该订单" . request()->input('order_id'));
        }

        if (request()->has('remark')) {
            $remark = $order->hasOneOrderRemark;
            if (!$remark) {
                $remark = new Remark([
                    'order_id' => request()->input('order_id'),
                    'remark' => request()->input('remark')
                ]);

                if (!$remark->save()) {
                    return $this->errorJson();
                }
            } else {
                $reUp = Remark::where('order_id', request()->input('order_id'))
                    ->where('remark', $remark->remark)
                    ->update(['remark' => request()->input('remark')]);

                if (!$reUp) {
                    return $this->errorJson();
                }
            }
        }
        //(new \app\common\services\operation\OrderLog($remark, 'special'));
        echo json_encode(["data" => '', "result" => 1]);
    }

    /**
     * fixby-lgg-invoice 2020-08-01 10:10
     * @throws AppException
     */
    public function invoice()
    {
        $order = Order::find(request()->input('order_id'));

        if (!$order) {
            throw new AppException("未找到该订单" . request()->input('order_id'));
        }

        $invoice = trim(request()->input('invoice'));
        if (empty($invoice)) {
            throw new AppException("请上传发票图片");
        }

        $order->invoice = $invoice;
        $order->invoice_status = 2;
        $order->save();
        echo json_encode(["data" => '', "result" => 1]);
    }

    /**
     * fixby-lgg-发票驳回 2020-08-01 10:10
     * @return mixed
     * @throws AppException
     */
    public function invoiceRefuse()
    {
        $order = Order::find(request()->input('order_id'));

        if (!$order) {
            throw new AppException("未找到该订单" . request()->input('order_id'));
        }

        $invoice_error = trim(request()->input('invoice_error'));
        if (empty($invoice_error)) {
            throw new AppException("请填写驳回原因");
        }

        $order->invoice_status = 3;
        $order->invoice_error = $invoice_error;
        $order->save();
        return $this->message('操作成功');
    }

    /**
     * fixby-zhd-jushuitanERP订单推送 2020-09-21 18:05
     * @throws AppException
     */
    public function jushuitanSend()
    {
        $order = Order::with('address', 'hasManyOrderGoods', 'hasOneOrderPay')->find(request()->input('order_id'));

        if (!$order) {
            throw new AppException("未找到该订单" . request()->input('order_id'));
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
                'properties_value' => $val['goods_option_title']  //string商品属性；长度<=100 （非必传）
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
            'items' => $items,  //商品明细 （必传项）
        ]);


        $result = OrderService::post($params, 'jushuitan.orders.upload');
        \Log::info('----'.$order->order_sn.'订单上传结果：', $result);
        if (!empty($result) && $result['code'] == 0) {
            //$data['jushuitan_status'] = '1';
            $order->jushuitan_status = 1;
            $order->save();
            //DB::table('yz_order')->where(['id' => $order_data['id']])->update($data);
            //echo '订单上传成功' . $order_data['id'];
            return $this->message('订单上传成功');
        } else {

            //echo $result['msg'];
            return $this->message('订单上传失败:'.$result['msg'],'','error');
        }

    }

}