<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/6/25
 * Time: 上午 09:47
 */

namespace app\frontend\modules\payment\listeners;

use app\common\events\payment\GetOrderPaymentTypeEvent;
use app\common\events\payment\RechargeComplatedEvent;


class WechatJsapiPayListener
{
    /**
     * 微信支付
     *
     * @param GetOrderPaymentTypeEvent $event
     * @return null
     */
    public function onGetPaymentTypes(GetOrderPaymentTypeEvent $event)
    {

        if(\YunShop::request()->type == 5 && \YunShop::request()->type == 7){
            return null;
        }
        if (!app('plugins')->isEnabled('face-payment')) {
            return null;
        }
        $face_setting = \Setting::get('plugin.face-payment');
        if (!$face_setting['switch'] || !$face_setting['method']['weixin'] || $face_setting['button']['wechat']) {
            return null;
        }

        if (\Setting::get('shop.wechat_set')) {
            $result = [
                'name' => '微信支付(服务商)',
                'value' => '48',
                'need_password' => '0'
            ];

            $event->addData($result);
        }
        return null;
    }

    public function subscribe($events)
    {
        $events->listen(
            GetOrderPaymentTypeEvent::class,
            self::class . '@onGetPaymentTypes'
        );
        $events->listen(
            RechargeComplatedEvent::class,
            self::class . '@onGetPaymentTypes'
        );
    }
}