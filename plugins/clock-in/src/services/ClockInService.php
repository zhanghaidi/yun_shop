<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/24
 * Time: 上午10:27
 */

namespace Yunshop\ClockIn\services;


use app\common\facades\Setting;
use app\frontend\modules\payment\orderPayments\BasePayment;

class ClockInService
{
    public $pluginName = '早起打卡';
    public $payMethod = ['1' => '微信支付', '2' => '支付宝支付', '3' => '余额支付', '28' => '微信支付-汇聚', '29' => '支付宝支付-汇聚'];
    private $setting;

    public function __construct()
    {

    }

    private function getSetting()
    {
        if (!isset($this->setting)) {
            $this->setting = Setting::get('plugin.clock_in');
        }
        return $this->setting;

    }

    /**
     * @param null $key
     * @return array|string
     */
    public function get($key = null)
    {
        switch ($key) {
            case 'plugin_name':
                $value = $this->getPluginName($key);
                break;
            case 'pay_method':
                $value = $this->getPayMethod();
                break;
        }
        return $value;
    }

    /**
     * @return array
     */
    public function getPayMethod()
    {
        return $this->payMethod;
    }

    /**
     * @param $key
     * @return string
     */
    public function getPluginName($key)
    {

        if (isset($key)) {
            $this->pluginName = $this->getSetting()['plugin_name'] ? $this->getSetting()['plugin_name'] : $this->pluginName;
        }
        return $this->pluginName;
    }

    /**
     * @param null $key
     * @return string
     */
    public static function getPayStatusName($key = null)
    {
        switch ($key) {
            case '0':
                return '未支付';
                break;
            case '1':
                return '已支付';
                break;
        }
    }

    /**
     * @param null $key
     * @return string
     */
    public static function getClockInStatusName($key = null)
    {
        switch ($key) {
            case '0':
                return '未打卡';
                break;
            case '1':
                return '打卡成功';
                break;
        }
    }


    public function randAmount($total_bean, $total_packet)
    {

        $min = 0.01;
        $max = $total_bean - $min;
        $list = [];

        if ($total_bean / $total_packet < $min || $total_bean < $min) {
            return ['status' => 0, 'message' => '瓜分金额小于' . $min . '元'];
        }

        $maxLength = $total_packet - 1;
        while (count($list) < $maxLength) {
            $rand = $this->randomFloat($min, $max);
            if (empty($list[$rand])) {
                $list[$rand] = $rand;
            }
        }
        $list[0] = 0; //第一个
        $list[$total_bean] = $total_bean; //最后一个

        sort($list); //不再保留索引
        $beans = [];
        for ($j = 1; $j <= $total_packet; $j++) {
            $beans[] = sprintf("%.2f", $list[$j] - $list[$j - 1]);
        }

        return ['status' => 1, 'beans' => $beans];
    }


    public function randomFloat($min = 0, $max = 10)
    {
        $num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        return sprintf("%.2f", $num);

    }

    public function getpayMethodName($pay_ethod)
    {
        $paymentTypes = app('PaymentManager')->make('OrderPaymentTypeManager')->getOrderPaymentTypes();
        $payMethods = $paymentTypes->map(function (BasePayment $paymentType) {
            return [
                'name' => $paymentType->getName(),
                'value' => $paymentType->getId(),
                'need_password' => $paymentType->needPassword(),
            ];
        });

        foreach ($payMethods as $item) {
            if ($item['value'] == $pay_ethod) {
                $name = $item['value'];
            }
        }
        return $name;

    }


}