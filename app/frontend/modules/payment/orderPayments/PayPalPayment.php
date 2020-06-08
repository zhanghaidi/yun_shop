<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/4/26
 * Time: 10:19
 */

namespace app\frontend\modules\payment\orderPayments;


class PayPalPayment extends WebPayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('pay-pal');
    }
}