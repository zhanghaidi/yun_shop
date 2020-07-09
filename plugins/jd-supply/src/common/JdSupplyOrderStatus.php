<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/18
 * Time: 10:58
 */

namespace Yunshop\JdSupply\common;


use app\common\models\Order;
use Yunshop\JdSupply\models\JdSupplyOrder;

class JdSupplyOrderStatus
{
    const LOCKING = 1;
    const UNLOCK  = 0;

    
    //锁住订单
    public static function lockingOrder(Order $order)
    {
        $order->is_pending = self::LOCKING;
        if ($order->save()) {
           return JdSupplyOrder::updateStatus($order->id, 1);
        }
        
        return false;
    }


    /**
     * 修改状态为等待第三方发货通知
     * @param Order $order
     * @return bool
     */
    public static function waitSend($order_id)
    {
        return JdSupplyOrder::updateStatus($order_id, 2);
    }


    //解锁订单
    public static function unlockOrder(Order $order)
    {
        $order->is_pending = self::UNLOCK;
        if ($order->save()) {
            return JdSupplyOrder::updateStatus($order->id, 3);
        }

        return false;
    }
}