<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/11
 * Time: 下午5:32
 */

namespace Yunshop\Mryt\store\models;


use app\common\models\BaseModel;

class GiveCoupon extends BaseModel
{
    public $table = 'yz_store_cashier_give_coupon';
    public $timestamps = true;
    protected $guarded = [''];

    public static function getRemardCoupons()
    {
        return self::select();
    }
}