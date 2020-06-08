<?php


namespace app\frontend\modules\payment\orderPayments;


class AlipayJsapiPayment extends WebPayment
{
    public function canUse()
    {
        //小程序，app不支持
        if (\YunShop::request()->type == 2 || \YunShop::request()->type == 7) {
            return false;
        }
        if (!app('plugins')->isEnabled('face-payment')) {
            return false;
        }
        $face_setting = \Setting::get('plugin.face-payment');
        if (!$face_setting['switch'] || !$face_setting['method']['weixin'] || $face_setting['button']['wechat']) {
            return false;
        }
        return parent::canUse();
    }
}