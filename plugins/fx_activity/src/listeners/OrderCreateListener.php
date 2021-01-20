<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/28
 * Time: 8:56
 */
namespace Yunshop\FxActivity\listeners;

use app\common\events\order\AfterOrderCreatedEvent;
use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\Printer\common\services\PrintingService;

class OrderCreateListener
{
    use DispatchesJobs;

    public $event;
    public $order;
    private $goods_id;
    private $store_goods;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderCreatedEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderCreatedEvent $event)
    {
        $this->event = $event;
        $this->order = Order::find($event->getOrderModel()->id);
        $this->goods_id = $this->order->hasManyOrderGoods->first()->goods_id;
    }
}