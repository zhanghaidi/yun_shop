<?php
/****************************************************************
 * Author:  BaoJia LI
 * Date:    2017/6/30 下午3:25
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Listeners;

use app\common\events\order\AfterOrderReceivedEvent;
use app\common\events\order\CreatedOrderEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Love\Common\Services\LoveGiveService;
use Yunshop\Love\Common\Services\SetService;

class OrderReceiveListener
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderReceivedEvent::class,static::class.'@onLoveGive');
    }


    public function onLoveGive($event)
    {
        /**
         * @var AfterOrderReceivedEvent $event
         */
        $loveGiveService = new LoveGiveService();

        $orderModel = $event->getOrderModel();
        if ($this->orderStatus()) {
            $loveGiveService->loveGive($orderModel);
        }
        //订单完成激素激活爱心值
        $loveGiveService->quickenActivation($orderModel);
    }

    /**
     * 爱心值奖励时间设置：订单支付(1) or 订单完成(0)
     *
     * @return bool
     */
    private function orderStatus()
    {
        return !!!SetService::getLoveSet('order_status');
    }
}
