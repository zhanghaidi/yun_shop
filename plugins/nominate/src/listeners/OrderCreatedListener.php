<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/18
 * Time: 10:40 AM
 */

namespace Yunshop\Nominate\listeners;


use app\common\events\order\AfterOrderCreatedImmediatelyEvent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\models\Order;
use Yunshop\Nominate\jobs\AwardJob;

class OrderCreatedListener
{
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderCreatedImmediatelyEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderCreatedImmediatelyEvent $event)
    {
        //file_put_contents(storage_path('logs/zxz.txt'), print_r(date('Ymd His').'推荐奖励开始'.PHP_EOL,1), FILE_APPEND);

        $set = \Setting::get('plugin.nominate');
        if (!$set['is_open']) {

            //file_put_contents(storage_path('logs/zxz.txt'), print_r(date('Ymd His').'推荐奖励开始-没开启'.PHP_EOL,1), FILE_APPEND);

            return;
        }

        $model = $event->getOrderModel();

        $order = Order::find($model->id);//订单信息

        $this->dispatch(new AwardJob($order));
    }
}