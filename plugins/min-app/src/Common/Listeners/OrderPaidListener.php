<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/27
 * Time: 下午5:13
 */

namespace Yunshop\MinApp\Common\Listeners;

use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\events\order\AfterOrderPaidEvent;
use Yunshop\MinApp\Common\Services\HwqService;
use Yunshop\MinApp\Common\Services\UpdateHwqService;

class OrderPaidListener
{
    public $event;
    public $order;

    /**
     * 订单支付完成异步事件
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderPaidEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderPaidEvent $event)
    {
            if(\Setting::get('plugin.min_app.hwq')){
                $this->event = $event;
                $this->order = Order::find($event->getOrderModel()->id);
                \Log::info('好物圈下单支付，ORDER_ID:', $this->order->id);
                if($this->order->status == 1 or ($this->order->dispatch_type_id ==0 and $this->order->status == 3)){
                    new HwqService($this->order);
                }else{
                    new UpdateHwqService($this->order);
                }
            }
    }
}