<?php

namespace Yunshop\ClockIn\api;

use app\common\components\ApiController;
use app\common\events\payment\ChargeComplatedEvent;
use app\common\facades\Setting;
use app\common\services\PayFactory;
use Yunshop\ClockIn\models\ClockPayLogModel;
use Yunshop\ClockIn\services\ClockInService;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/16
 * Time: 下午2:29
 */
class ClockInPayController extends ApiController
{
    public $_set;
    public $_clockInService;
    public $_pluginName;

    public function preAction()
    {
        parent::preAction();

        $this->_clockInService = new ClockInService();
        $this->_pluginName = $this->_clockInService->get('plugin_name');
        $this->_payMethod = $this->_clockInService->get('pay_method');
        $this->_set = Setting::get('plugin.clock_in');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayData()
    {
        //plugin.clock-in.api.clock-in-pay.get-pay-data

        $data = [
            'amount' => $this->_set['amount'],
        ];
        if ($data) {
            return $this->successJson('Ok', $data);
        }
        return $this->errorJson('未检测到数据', $data);
    }

    /**
     *
     */
    public function runClockPay()
    {
        //plugin.clock-in.api.clock-in-pay.run-clock-pay
        $amount = \YunShop::request()->amount;
        $payMethod = \YunShop::request()->pay_method;
        $member_id = \YunShop::app()->getMemberId();

        if ($amount <= 0) {
            return $this->errorJson('支付金额不正确！', $amount);
        }
        \Log::debug('----支付验证---1');
        if ($this->getValidatePay($member_id)->where('pay_status', 1)->first()) {
            return $this->errorJson('今日已支付！');
        }
        \Log::debug('----支付验证----2', [$member_id]);
        $clockLog = $this->getValidatePay($member_id)->where('pay_status', 0)->first();
        \Log::debug('----支付验证----3', $clockLog);
        if ($clockLog) {
            \Log::debug('----支付验证----4');
            $order_sn = $clockLog->order_sn;
            ClockPayLogModel::uniacid()
                ->where('order_sn', $order_sn)
                ->update([
                    'pay_method' => $payMethod,
                ]);
        } else {
            \Log::debug('----支付验证----5');
            $order_sn = $this->addPayLog($member_id, $amount, $payMethod);
        }
        \Log::debug('---订单号---', [$order_sn]);

        $data =  array(
            'uid' => \Yunshop::app()->getMemberId(),
            'order_sn' => $order_sn
        );

        if ($payMethod == 9){
            return $this->successJson('支付成功', $data);
        }

        if ($order_sn) {
            $payRequest = $this->getClockPay($amount, $payMethod, $order_sn);
        }
        \Log::debug('----支付返回----', $payRequest);
        if ($payRequest) {
            if (is_array($payRequest)) {
                if ($payRequest['js'] && $payRequest['config']) {
                    $payRequest['js'] = json_decode($payRequest['js'], 1);
                }
            }

            if (is_bool($payRequest[0]) && $payMethod == 3) {
                event(new ChargeComplatedEvent([
                    'order_sn' => $order_sn,
                    'pay_sn' => ''
                ]));
            }

            return $this->successJson('支付成功', $payRequest);
        }
        \Log::debug('----支付失败---');
        return $this->errorJson('支付失败', $payRequest);
    }

    public function getValidatePay($member_id)
    {
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间


        $memberPayLog = ClockPayLogModel::uniacid()
            ->select('id', 'member_id', 'amount', 'pay_status', 'order_sn')
            ->whereBetween('created_at', [$today, $current])
            ->where('member_id', $member_id);

        return $memberPayLog;
    }

    public function addPayLog($memberId, $amount, $payMethod)
    {

        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $memberId,
            'amount' => $amount,
            'pay_method' => $payMethod,
            'pay_status' => 0,
            'clock_in_status' => 0,
            'clock_in_at' => null,
            'queue_id' => 0,
            'order_sn' => ClockPayLogModel::createOrderSn('CI', 'order_sn'),
            'created_at' => time(),
            'updated_at' => time(),
        ];
        if (ClockPayLogModel::insert($data)) {
            return $data['order_sn'];
        }
        return '';
    }

    public function getClockPay($amount, $payMethod, $orderNo)
    {
        /**
         * 订单支付/充值
         *
         * @param $subject 名称
         * @param $body 详情
         * @param $amount 金额
         * @param $order_no 订单号
         * @param $extra 附加数据
         * @return strin5
         */
        $data = [
            'subject' => '早起打卡-支付',
            'body' => '早起打卡:' .\YunShop::app()->uniacid,
            'amount' => $amount,
            'order_no' => $orderNo,
            'extra' => ['type' => 1],
            'member_id' => \Yunshop::app()->getMemberId()
        ];

        return PayFactory::pay($payMethod, $data);
    }


}