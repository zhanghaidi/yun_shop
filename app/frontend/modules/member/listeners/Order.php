<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/1
 * Time: 下午4:49
 */

namespace app\frontend\modules\member\listeners;

use app\common\events\order\AfterOrderCreatedImmediatelyEvent;

class Order
{
    public function handle(AfterOrderCreatedImmediatelyEvent $event)
    {
        $order = $event->getOrder();
        if (!isset($order->orderRequest->request['cart_ids'])) {
            return;
        }
        $ids = json_decode($order->orderRequest->request['cart_ids'],true);
        $ids = $ids ?: [];

        if (!count($ids)) {
            return;
        }
        app('OrderManager')->make('MemberCart')->whereIn('id', $ids)->delete();
    }
}