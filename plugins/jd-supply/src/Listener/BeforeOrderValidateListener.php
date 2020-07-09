<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/18
 * Time: 10:53
 */

namespace Yunshop\JdSupply\Listener;


use app\common\events\order\BeforeOrderPayEvent;
use app\common\events\order\BeforeOrderPayValidateEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\JdSupply\models\JdSupplyOrder;
use Yunshop\JdSupply\services\JdOrderValidate;


class BeforeOrderValidateListener
{
    public $order;
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(BeforeOrderPayValidateEvent::class, self::class . '@handle');
    }

    public function handle(BeforeOrderPayValidateEvent $event)
    {
        $this->order = $event->getOrderModel();
        if ($this->order->plugin_id == JdSupplyOrder::PLUGIN_ID) {
            $this->order->jd_order_goods = $this->order->orderGoods;
            JdOrderValidate::orderValidate($this->order);
            unset($this->order->jd_order_goods);
        }
    }
}