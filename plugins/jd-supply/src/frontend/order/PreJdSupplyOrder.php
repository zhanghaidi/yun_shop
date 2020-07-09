<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 10:43
 */

namespace Yunshop\JdSupply\frontend\order;


use app\common\models\Order;
use Yunshop\JdSupply\models\JdSupplyOrder;

class PreJdSupplyOrder extends JdSupplyOrder
{
//    private $order;

    public function afterCreating()
    {

    }

    public function init()
    {

    }

    public function initAttributes(Order $order)
    {
        $attributes = [
            'member_id' => $order->uid,
            'order_price' => $order->price,
            'order_sn' => $order->order_sn,
        ];
        $attributes = array_merge($this->getAttributes(), $attributes);
        $this->setRawAttributes($attributes);
    }
}