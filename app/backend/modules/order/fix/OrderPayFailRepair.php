<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/15
 * Time: 下午1:49
 */

namespace app\backend\modules\order\fix;

use app\backend\modules\orderPay\fix\DoublePaymentRepair;
use app\common\events\payment\ChargeComplatedEvent;
use app\common\models\Order;
use app\common\models\OrderPay;
use app\common\models\PayRequestDataLog;
use app\common\services\PayFactory;
use app\frontend\modules\order\services\OrderService;

class OrderPayFailRepair
{
    /**
     * @var Order
     */
    private $order;
    public $message = [];

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return bool
     * @throws \app\common\exceptions\AppException
     */
    public function handle()
    {

        if (!$this->check()) {
            $this->message[] = '不满足修复条件';

            return false;
        }

        /**
         * @var OrderPay $orderPay
         */
        $orderPay = $this->order->orderPays->where('status', 1)->sort(function ($orderPay){
            return $orderPay->update_at;
        })->first();
        $orderPay->pay();
        $this->message[] = $this->order->order_sn.'已修复';
        // todo 剩余的记录执行退款
        $this->order->orderPays->where('status', 1)->where('id', '!=', $orderPay->id)->each(
            function (OrderPay $orderPay) {
                $this->message[] = $orderPay->pay_sn . '已退款';
                (new DoublePaymentRepair($orderPay))->handle();
            }

        );


    }

    private function check()
    {
        // 待支付
        if ($this->order->status != Order::WAIT_PAY) {
            $this->message[] = '订单已支付';
            return false;
        }

        // 已付款
        if (count($this->order->orderPays->where('status', 1)) > 0) {
            return true;
        }
        $paySns = $this->order->orderPays->pluck('pay_sn');

        $payResult = PayRequestDataLog::whereIn('out_order_no',$paySns)->get();

        if($payResult->count()>0){
            $this->message[] = "共{$payResult->count()}条支付记录";
            $data = json_decode($payResult->first()->params,true);

            $orderPay = OrderPay::where('pay_sn', $data['out_trade_no'])->orderBy('id', 'desc')->first();

            if ($data['unit'] == 'fen') {
                $orderPay->amount = $orderPay->amount * 100;
            }

            if (bccomp($orderPay->amount, $data['total_fee'], 2) == 0) {

                \Log::debug('更新订单状态');
                OrderService::ordersPay(['order_pay_id' => $orderPay->id, 'pay_type_id' => $data['pay_type_id']]);

                event(new ChargeComplatedEvent([
                    'order_sn' => $data['out_trade_no'],
                    'pay_sn' => $data['trade_no'],
                    'order_pay_id' => $orderPay->id
                ]));
            }
            return true;

        }



        return false;
    }

}