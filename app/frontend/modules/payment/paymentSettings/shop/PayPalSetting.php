<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/4/26
 * Time: 10:16
 */

namespace app\frontend\modules\payment\paymentSettings\shop;


class PayPalSetting  extends BaseSetting
{
    public function canUse()
    {
        $set = \Setting::get('plugin.pay_pal');

        return \YunShop::request()->type != 7 && !is_null($set) && $set['is_open'] != 1;
    }

    public function exist()
    {
        $set = \Setting::get('plugin.pay_pal');

        return !empty($set['client_id']) && !empty($set['client_secret']);
    }
}