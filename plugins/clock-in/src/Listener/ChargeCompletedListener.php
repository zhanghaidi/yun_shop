<?php

namespace Yunshop\ClockIn\Listener;

use app\common\events\payment\ChargeComplatedEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\ClockIn\models\ClockPayLogModel;
use Yunshop\ClockIn\services\CommissionService;
use Yunshop\ClockIn\services\TeamService;

class ChargeCompletedListener
{

    public function subscribe(Dispatcher $events)
    {
        $events->listen(ChargeComplatedEvent::class, function ($event) {


            $chargeData = $event->getChargeData();
            $order_sn = $chargeData['order_sn'];

            if (substr($order_sn, 0, 2) != 'CI') {
                return;
            }
            $data = [
                'pay_status' => 1,
            ];

            ClockPayLogModel::uniacid()
                ->where('order_sn', $order_sn)
                ->update($data);

            $clock = ClockPayLogModel::select()->where('order_sn', $order_sn)->first();
            if (!$clock) {
                return;
            }
            // 参与分销
            (new CommissionService($clock->member_id, $clock->amount, $order_sn))->handle();
            // 参与 经销商管理分红
            (new TeamService($clock->member_id, $clock->amount))->handle();
        });

    }
}
