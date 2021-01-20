<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/1
 * Time: 11:41
 */

namespace Yunshop\Nominate\listeners;

use app\common\events\order\AfterOrderCanceledEvent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\models\Order;
use Yunshop\Nominate\models\TeamPrize;

class OrderCanceledListener
{
    use DispatchesJobs;

    public $order;
    public $log_ids = null;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderCanceledEvent::class, self::class. '@changeAwardStatus');
    }

    public function changeAwardStatus(AfterOrderCanceledEvent $event)
    {
        $model = $event->getOrderModel();
        $this->order = Order::find($model->id);//订单信息
        $this->handle();
    }

    public function handle()
    {
        $set = \Setting::get('plugin.nominate');
        if (!$set['is_open']) {
            return;
        }
        $log_data = [
            'status'  => 2,//状态：已失效
        ];
        $this->getAwardLog();
        if (is_null($this->log_ids)) {
            return;
        }
        $result = TeamPrize::uniacid()->whereIn('id', $this->log_ids)->update($log_data);
        if ($result) {
            //todo 奖励失效通知
        }
    }

    public function getAwardLog()
    {
        $logs = TeamPrize::uniacid()->where('order_id', $this->order->id)->get();
        if (!$logs->isEmpty()) {
            $this->log_ids = $logs->pluck('id');
        }
    }
}