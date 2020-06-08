<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/4/8
 * Time: 14:24
 */

namespace app\frontend\modules\payment\orderPayments;


class ConvergeQuickPayPayment extends BasePayment
{
    public function canUse()
    {
        return parent::canUse() && \YunShop::plugin()->get('converge_pay');
    }
}