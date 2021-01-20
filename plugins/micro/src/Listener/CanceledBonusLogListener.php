<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/10
 * Time: 下午4:25
 */

namespace Yunshop\Micro\Listener;

use app\common\events\order\AfterOrderCanceledEvent;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\models\Order;
use Yunshop\Micro\common\models\MicroShopBonusLog;

class CanceledBonusLogListener
{
    public $event;
    public $order;
    public $log_ids = null;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderCanceledEvent::class, self::class . '@onUpdateBonusLog');
    }

    public function onUpdateBonusLog(AfterOrderCanceledEvent $event)
    {
        $this->event = $event;
        $this->order = Order::find($event->getOrderModel()->id);
        $this->handle();
    }

    public function handle()
    {
        $log_data = [
            'apply_status'  => -1,
            'refund_time'    => time()
        ];
        $this->getLogIds();
        if (is_null($this->log_ids)) {
            return;
        }
        $result = MicroShopBonusLog::uniacid()->whereIn('id',$this->log_ids)->update($log_data);
        if ($result) {
            // todo 分红失效通知
        }
    }

    public function getLogIds()
    {
        $logs = MicroShopBonusLog::uniacid()->where('order_id', $this->order->id)->get();
        if (!$logs->isEmpty()) {
            $this->log_ids = $logs->pluck('id');
        }
    }
}