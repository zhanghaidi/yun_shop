<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/9/5
 * Time: 13:50
 */

namespace Yunshop\Commission\models;


class OrderGoods extends \app\common\models\OrderGoods
{
    public function hasOneOrder()
    {
        return $this->hasOne(\app\common\models\Order::class,'id', 'order_id');
    }
}