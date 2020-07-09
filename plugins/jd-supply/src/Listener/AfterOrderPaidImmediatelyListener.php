<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/18
 * Time: 10:53
 */

namespace Yunshop\JdSupply\Listener;


use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\JdSupply\common\JdSupplyOrderStatus;
use Yunshop\JdSupply\models\JdSupplyOrder;
use Yunshop\JdSupply\models\Order;
use Yunshop\JdSupply\services\JdOrderService;


class AfterOrderPaidImmediatelyListener
{
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderPaidImmediatelyEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderPaidImmediatelyEvent $event)
    {

        \YunShop::app()->getMemberId();

        $order =  Order::find($event->getOrderModel()->id);



        if ($order->plugin_id != JdSupplyOrder::PLUGIN_ID) {
            return;
        }

        if(is_null(JdSupplyOrder::isJdOrder($order->id))) {
            return;
        }

        //未提交订单到第三方不能发货
        JdSupplyOrderStatus::lockingOrder($order);
        //自动提交订单
        JdOrderService::autoCommitOrder($order->id);
    }
}