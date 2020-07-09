<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/27
 * Time: 下午5:11
 */

namespace Yunshop\Printer\common\listeners;

use app\backend\modules\order\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Printer\common\services\PrintingService;
use app\common\events\order\AfterOrderCreatedEvent;

class OrderCreatedListener
{
    public $event;
    public $order;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderCreatedEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderCreatedEvent $event)
    {
        $this->event = $event;
        $this->order = Order::find($event->getOrderModel()->id);
        $plugin_goods = \app\common\modules\shop\ShopConfig::current()->get('plugin_goods');
        \Log::info('plugin:',  $plugin_goods);
        $ret = false;
        \Log::info('oder11111:', $this->order);
        foreach ($plugin_goods as $plugin) {
            $class = array_get($plugin,'class');
            $function = array_get($plugin,'function');
            if(class_exists($class) && method_exists($class,$function) && is_callable([$class,$function])){
                $ret = $class::$function($this->order);
                if ($ret) {
                    break;
                }
            }
        }
        if (!$ret) {
            if ($this->order->is_plugin == 0 && $this->order->plugin_id == 0) {
                \app\common\modules\shop\ShopConfig::current()->set('printer_owner', [
                    'owner' => 1,
                    'owner_id' => 0
                ]);
                \Log::info('商城下单打印，ORDER_ID:', $this->order->id);
                new PrintingService($this->order, 1, '商城下单打印');
            }
        }
    }
}