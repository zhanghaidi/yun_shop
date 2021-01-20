<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/18
 * Time: 11:36 AM
 */

namespace Yunshop\Nominate\listeners;


use app\common\events\order\AfterOrderReceivedEvent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\models\Order;
use Yunshop\Nominate\jobs\AwardJob;

class OrderReceiveListener
{
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderReceivedEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderReceivedEvent $event)
    {
        $set = \Setting::get('plugin.nominate');
        if (!$set['is_open']) {
            return;
        }

        $model = $event->getOrderModel();

        $order = Order::find($model->id);//订单信息

        $this->dispatch(new AwardJob($order));
    }
}