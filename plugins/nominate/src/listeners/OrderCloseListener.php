<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/1
 * Time: 13:39
 */

namespace Yunshop\Nominate\listeners;

use app\common\events\order\AfterOrderCloseEvent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\models\Order;
use Yunshop\Nominate\models\TeamPrize;

class OrderCloseListener
{
    use DispatchesJobs;

    public $order;
    public $log_ids = null;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderCloseEvent::class, self::class. '@changeAwardStatus');
    }

    public function changeAwardStatus(AfterOrderCloseEvent $event)
    {
        $model = $event->getOrderModel();
        $this->order = Order::find($model->id);//订单信息
        $this->handle();
    }

    public function handle()
    {
        $set = \Setting::get('plugin.nominate');
        \Log::debug('--------推荐奖励设置--------', $set);
        if (!$set['is_open']) {
            return;
        }
        $log_data = [
            'status'  => 2,//状态：已失效
        ];
        $this->getAwardLog();
        \Log::debug('--------推荐奖励记录id--------', $this->log_ids);
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