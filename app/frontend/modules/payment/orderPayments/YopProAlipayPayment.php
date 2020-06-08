<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/2/17
 * Time: 15:38
 */

namespace app\frontend\modules\payment\orderPayments;


class YopProAlipayPayment extends WebPayment
{
    public function canUse()
    {
        //&& \YunShop::request()->type == 1;
        return parent::canUse() && \YunShop::plugin()->get('yop-pro');
    }
}