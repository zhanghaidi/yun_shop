<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/4/4
 * Time: 16:27
 */

namespace Yunshop\Commission\models;


class Order extends \app\common\models\Order
{
    public function hasOneCommission()
    {
        return $this->hasOne(CommissionOrder::class,'ordertable_id','id');
    }

}