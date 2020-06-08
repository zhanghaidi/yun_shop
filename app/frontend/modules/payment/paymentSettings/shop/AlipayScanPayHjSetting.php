<?php


namespace app\frontend\modules\payment\paymentSettings\shop;


class AlipayScanPayHjSetting extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.convergePay_set.alipay.alipay_status');
//        return \YunShop::request()->type != 9 && $set;
        return false;
    }

    public function exist()
    {
//        return \Setting::get('plugin.convergePay_set') !== null;
        return false;
    }
}