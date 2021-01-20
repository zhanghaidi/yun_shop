<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/19
 * Time: 下午1:39
 */

namespace Yunshop\Micro\Listener;

use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\events\order\AfterOrderPaidEvent;
use Yunshop\Micro\common\models\MicroShopBonusLog;
use Illuminate\Support\Facades\DB;

class EditBonusLogByPayListener
{
    //public $field = ['pay_type', 'pay_sn', 'pay_time', 'order_status'];
    //DB::raw(implode(',', $this->field))
    public $event;
    public $order;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderPaidEvent::class, self::class . '@onEdit');
    }

    public function onEdit(AfterOrderPaidEvent $event)
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
        $pay_data = [
            'pay_type'      => $this->order->pay_type_name,
            'pay_sn'        => $this->order->hasOneOrderPay->pay_sn,
            'pay_time'      => $this->order->pay_time->timestamp,
            'order_status'  => $this->order->status
        ];
        $logs->each(function($log)use($pay_data){
            $log->fill($pay_data);
            $log->save();
        });
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