<?php


namespace app\frontend\modules\payment\paymentSettings\shop;


class HkScanPaySetting extends BaseSetting
{
    public function canUse()
    {
        if(\Setting::get('plugin.hk_pay_set.is_open_pay') != 1 ){
            return false;
        }
        return true;
    }

    public function exist()
    {
        return true;
    }
}