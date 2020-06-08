<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/2/17
 * Time: 15:36
 */

namespace app\frontend\modules\payment\orderPayments;


class YopProWechatPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::request()->type == 1 && \YunShop::plugin()->get('yop-pro');
    }
}