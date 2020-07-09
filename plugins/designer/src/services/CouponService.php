<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/7/3
 * Time: 14:46
 */

namespace Yunshop\Designer\services;

use app\backend\modules\coupon\models\Coupon;

class CouponService extends Coupon
{

    public static function getCouponByIds($ids)
    {
        return self::uniacid()->whereIn('id', $ids)->get()->toArray();
    }

}