<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/11
 * Time: 下午4:13
 */

namespace Yunshop\MinApp\Common\Listeners;

use app\common\events\order\AfterOrderReceivedEvent;
use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\MinApp\Common\Services\UpdateHwqService;

class OrderReceiveListener
{
    public $event;
    public $order;

    /**
     * 订单完成异步事件
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderReceivedEvent::class, self::class . '@handle');
    }

    /**
     * @name 监听执行
     * @author
     * @param AfterOrderReceivedEvent $event
     */
    public function handle(AfterOrderReceivedEvent $event)
    {
            if(\Setting::get('plugin.min_app.hwq')){
                $this->event = $event;
                $this->order = Order::find($event->getOrderModel()->id);
                \Log::info('好物圈下单完成，ORDER_ID:', $this->order->id);
                new UpdateHwqService($this->order);
            }
    }

}