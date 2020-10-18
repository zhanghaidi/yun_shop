<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 11:12
 */

namespace app\frontend\modules\coupon\models;


class ShoppingShareCoupon extends \app\common\models\coupon\ShoppingShareCoupon
{
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('uniacid', function ($builder) {
            $builder->uniacid();
        });
    }

    public function hasOneOrder(){
        return $this->hasOne('App\common\models\Order','id','order_id');
    }
}