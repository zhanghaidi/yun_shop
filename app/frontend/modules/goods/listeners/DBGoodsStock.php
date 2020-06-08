<?php

namespace app\frontend\modules\goods\listeners;

use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderCreatedImmediatelyEvent;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\events\order\CreatedOrderEvent;
use app\common\events\order\CreatedOrderStatusChangedEvent;
use app\common\exceptions\GoodsStockNotEnough;
use app\common\facades\SiteSetting;
use app\common\models\OrderGoods;
use app\frontend\models\goods;
use app\frontend\models\GoodsOption;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午10:34
 */
class DBGoodsStock
{
    public function onOrderCreated(CreatedOrderEvent $event)
    {

        $order = $event->getOrderModel();
        $order->hasManyOrderGoods->map(function ($orderGoods) {

            if (!in_array($orderGoods->belongsToGood->reduce_stock_method, [0, 2])) {
                return false;
            }
            $this->reduceDBStock($orderGoods);
        });
    }

    public function onOrderPaid(CreatedOrderEvent $event)
    {

        $order = $event->getOrderModel();
        $order->hasManyOrderGoods->map(function ($orderGoods) {
            if (!in_array($orderGoods->belongsToGood->reduce_stock_method, [1, 2])) {
                return false;
            }
            try {
                $this->reduceDBStock($orderGoods);
            } catch (GoodsStockNotEnough $e) {
                \Log::error('商品超卖', $e->getMessage());
            }
        });
    }

    private function reduceDBStock($orderGoods)
    {
        /**
         * @var OrderGoods $orderGoods
         */
        if ($orderGoods->isOption()) {
            $goods_option = $orderGoods->goodsOption;
            /**
             * @var $goods_option GoodsOption
             */
            \Log::info("订单{$orderGoods->order_id}商品:{$orderGoods->goods_option_id}库存{$goods_option->stock}减{$orderGoods->total}");
            $goods_option->reduceDBStock($orderGoods->total);
            $orderGoods->hasOneGoods->addDBSales($orderGoods->total);
            return true;
        }
        /**
         * @var $goods Goods
         */
        $goods = $orderGoods->hasOneGoods;
        \Log::info("订单{$orderGoods->order_id}商品:{$orderGoods->goods_id}库存{$goods->stock}减{$orderGoods->total}");
        $goods->reduceDBStock($orderGoods->total);
        $goods->addDBSales($orderGoods->total);
        return true;
    }

    public function subscribe($events)
    {
        // 开启缓存
        if (SiteSetting::get('base.stock_cache')) {
            $events->listen(
                AfterOrderCreatedEvent::class,
                self::class . '@onOrderCreated'
            );
            $events->listen(
                AfterOrderPaidEvent::class,
                self::class . '@onOrderPaid'
            );
        } else {
            $events->listen(
                AfterOrderCreatedImmediatelyEvent::class,
                self::class . '@onOrderCreated'
            );
            $events->listen(
                AfterOrderPaidImmediatelyEvent::class,
                self::class . '@onOrderPaid'
            );
        }
    }
}