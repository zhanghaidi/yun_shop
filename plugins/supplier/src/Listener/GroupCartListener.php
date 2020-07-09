<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午2:14
 */
namespace Yunshop\Supplier\Listener;
use app\common\events\cart\GroupingCartEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Supplier\common\models\SupplierGoods;

class GroupCartListener
{
    /**
     * 购物车分组
     *
     * @param  Dispatcher $events
     * @return mixed
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(GroupingCartEvent::class, function($event) {
            $carts = $event->getCarts();
            $supplier_carts = [];
            $supplier_goods_ids = [];
            foreach ($carts as $key => $cart) {
                $supplier_goods = SupplierGoods::getSupplierGoodsById($cart['goods_id']);
                if ($supplier_goods) {
                    $supplier_carts[$supplier_goods->supplier_id][] = $cart;
                    $supplier_goods_ids[$cart['goods_id']] = $cart['goods_id'];
                }
            }
            if ($supplier_carts) {
                $event->addMap('supplier', $supplier_carts);
                $event->addMap('goods_ids', $supplier_goods_ids);
            }
        });
    }
}