<?php


namespace app\frontend\modules\coupon\models;


class OrderCoupon extends \app\common\models\Coupon
{

    /**
     * 临时解决办法，后期有空修改
     * @var array
     */
    protected $hidden = ['storeids'];
}