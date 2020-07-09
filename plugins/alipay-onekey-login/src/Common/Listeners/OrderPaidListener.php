<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/12/4
 * Time: 11:41 AM
 */

namespace Yunshop\Love\Common\Listeners;


use app\common\events\order\AfterOrderPaidEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Love\Common\Services\LoveGiveService;
use Yunshop\Love\Common\Services\SetService;

class OrderPaidListener
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderPaidEvent::class,static::class.'@onLoveGive');
    }


    public function onLoveGive($event)
    {
        /**
         * @var AfterOrderPaidEvent $event
         */
        if ($this->orderStatus()) {
            (new LoveGiveService())->loveGive($event->getOrderModel());
        }
    }

    /**
     * 爱心值奖励时间设置：订单支付(1) or 订单完成(0)
     *
     * @return bool
     */
    private function orderStatus()
    {
        return !!SetService::getLoveSet('order_status');
    }

}
