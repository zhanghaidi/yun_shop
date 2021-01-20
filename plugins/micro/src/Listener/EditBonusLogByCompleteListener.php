<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/19
 * Time: 下午4:13
 */

namespace Yunshop\Micro\Listener;

use app\common\events\order\AfterOrderReceivedEvent;
use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Micro\common\models\MicroShopBonusLog;
use Illuminate\Support\Facades\DB;

class EditBonusLogByCompleteListener
{
    //public $field = ['order_status', 'complete_time'];
    public $event;
    public $order;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderReceivedEvent::class, self::class . '@onEdit');
    }

    public function onEdit(AfterOrderReceivedEvent $event)
    {
        $this->event = $event;
        $this->order = Order::find($event->getOrderModel()->id);
        $this->update();
    }

    public function update()
    {
        $logs = $this->getLogs();
        if (!$logs) {
            return;
        }

        $complete_data = [
            'order_status'  => $this->order->status,
            'complete_time' => $this->order->finish_time->timestamp
        ];
        $logs->each(function($log)use($complete_data){
            $log->fill($complete_data);
            $log->save();
        });;
    }

    public function getLogs()
    {
        $logs = MicroShopBonusLog::uniacid()->select()
            ->byOrderId($this->order->id)
            ->get();
        if ($logs->isEmpty()) {
            return false;
        }
        return $logs;
    }
}