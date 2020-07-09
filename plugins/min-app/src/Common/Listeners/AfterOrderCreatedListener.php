<?php

namespace Yunshop\MinApp\Common\Listeners;

use app\common\models\Order;
use app\common\events\order\AfterOrderSentEvent;
use Yunshop\MinApp\Common\Services\UpdateHwqService;
use Illuminate\Contracts\Events\Dispatcher;

class AfterOrderCreatedListener
{

//    public $event;
    public $order;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderSentEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderSentEvent $event)
    {
            if(\Setting::get('plugin.min_app.hwq')){
                $this->order = Order::find($event->getOrderModel()->id);
                \Log::info('好物圈发货，ORDER_ID:', $this->order->id);
                new UpdateHwqService($this->order);
            }
    }
}