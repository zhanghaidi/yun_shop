<?php

namespace app\frontend\modules\goods\listeners;

use app\common\events\order\AfterOrderCreatedImmediatelyEvent;
use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\events\order\CreatedOrderEvent;
use app\common\exceptions\GoodsStockNotEnough;
use app\common\facades\SiteSetting;
use app\common\models\OrderGoods;
use app\frontend\models\goods;
use app\frontend\models\GoodsOption;
use Illuminate\Support\Facades\Redis;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午10:34
 */
class GoodsStock
{
    public function onOrderCreated(AfterOrderCreatedImmediatelyEvent $event)
    {

        $order = $event->getOrderModel();
        $order->hasManyOrderGoods->map(function ($orderGoods) {

            if (!in_array($orderGoods->belongsToGood->reduce_stock_method, [0, 2])) {
                return false;
            }
            $this->reduceStock($orderGoods);
        });
    }

    public function onOrderPaid(AfterOrderPaidImmediatelyEvent $event)
    {

        $order = $event->getOrderModel();
        $order->hasManyOrderGoods->map(function ($orderGoods) {
            if (!in_array($orderGoods->belongsToGood->reduce_stock_method, [1, 2])) {
                return false;
            }
            try {
                $this->reduceStock($orderGoods);
            } catch (GoodsStockNotEnough $e){
                \Log::error('商品超卖',$e->getMessage());
            }
        });
    }

    private function reduceStock($orderGoods)
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
            $goods_option->reduceStock($orderGoods->total);
            $orderGoods->hasOneGoods->addSales($orderGoods->total);
            return true;
        }
        /**
         * @var $goods Goods
         */
        $goods = $orderGoods->hasOneGoods;
        \Log::info("订单{$orderGoods->order_id}商品:{$orderGoods->goods_id}库存{$goods->stock}减{$orderGoods->total}");
        $goods->reduceStock($orderGoods->total);
        $goods->addSales($orderGoods->total);
        return true;
    }

    public function subscribe($events)
    {
        // 开启缓存
        if(SiteSetting::get('base.stock_cache')){
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