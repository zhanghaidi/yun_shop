<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/12/17
 * Time: 19:42
 */

namespace Yunshop\Mryt\listeners;

use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Mryt\services\AwardService;

class OrderCreatedListener
{
    /**
     *
     * @param  Dispatcher $events
     * @return mixed
     */
    public function subscribe(Dispatcher $events)
    {

        $events->listen(\app\common\events\order\AfterOrderCreatedEvent::class, function ($event) {
            $orderModel = $event->getOrderModel();
            $order = Order::find($orderModel->id);
            $order->no_refund = $this->isNotRefund($order);
            $order->save();
            if ($order->plugin_id == 32 || $order->plugin_id == 31) {
                $res = new AwardService('', '', $order);
                $res->storeAward();
            }
        });
    }

    public function isNotRefund($order)
    {
        if ($order->hasManyOrderGoods) {
            foreach ($order->hasManyOrderGoods as $goods) {
                if ($goods->hasOneGoods->no_refund) {
                    return 1;
                }
            }
        }
        return 0;
    }

}