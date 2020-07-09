<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午2:14
 */
namespace Yunshop\Supplier\Listener;
use app\common\events\cart\GroupingCartEvent;
use app\common\events\cart\GroupingCartIdEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Supplier\common\models\SupplierGoods;

class GroupCartIdListener
{
    /**
     * 购物车id分组
     *
     * @param  Dispatcher $events
     * @return mixed
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(GroupingCartIdEvent::class, function($event) {
            $cart_ids = $event->getCartIds();
            foreach ($cart_ids as $key => $goods_id) {
                $supplier_goods = SupplierGoods::getSupplierGoodsById($goods_id);
                if ($supplier_goods) {
                    $event->addMap($supplier_goods->supplier_id, $goods_id);
                }
            }
        });
    }
}