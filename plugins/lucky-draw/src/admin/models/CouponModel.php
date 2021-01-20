<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-06-11
 * Time: 10:03
 */

namespace Yunshop\LuckyDraw\admin\models;


use app\common\models\Coupon;

class CouponModel extends Coupon
{
    public static function getCouponByKwd($kwd)
    {
        return self::uniacid()
            ->select('id', 'name')
            ->where('status', 1)
            ->Where('name', 'like', '%' . $kwd . '%')
            ->get();
    }
}