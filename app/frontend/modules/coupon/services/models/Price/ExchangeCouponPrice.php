<?php
/**
 * Created by PhpStorm.
 * User: CGOD
 * Date: 2020/1/7
 * Time: 16:00
 */

namespace app\frontend\modules\coupon\services\models\Price;


class ExchangeCouponPrice extends CouponPrice
{
    protected function _getAmount()
    {
        return $this->getExchangeOrderGoodsCollectionPayment();
    }
}