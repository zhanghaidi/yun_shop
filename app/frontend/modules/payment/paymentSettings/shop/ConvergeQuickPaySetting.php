<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/4/8
 * Time: 14:25
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class ConvergeQuickPaySetting extends BaseSetting
{
    public function canUse()
    {
        return \Setting::get('plugin.convergePay_set.quick_pay.is_open');
    }

    public function exist()
    {
        $quickPay =  \Setting::get('plugin.convergePay_set.quick_pay');
        return !empty($quickPay['private_key']) &&  !empty($quickPay['platform_public_key']);

    }
}