<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/7
 * Time: 下午9:24
 */

namespace Yunshop\Supplier\Listener;


use app\common\events\Event;
use app\common\events\order\CreatingOrder;
use app\common\events\order\ShowPreGenerateOrder;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\supplier\models\SupplierGoodsJoinGoods;

class ShowPreGenerateOrderListener
{
    /**
     * @var ShowPreGenerateOrder;
     */
    private $event;

    /**
     * 获取供应商订单信息组
     * @param $memberCarts
     * @return \Illuminate\Support\Collection
     */
    private function getSupplierOrderData($memberCarts)
    {
        $order = OrderService::createOrderByMemberCarts($memberCarts);
        return OrderService::getOrderData($order);
    }

    public function onCreate(CreatingOrder $event)
    {
        $this->event = $event;
        $memberCartsGroups = $this->getSupplierMemberCartsGroups();
        if ($memberCartsGroups->isEmpty()) {
            return null;
        }
        $orderData = $memberCartsGroups->map(function ($memberCartsGroup) {
            //dd($memberCartsGroup);
            return OrderService::createOrderByMemberCarts($memberCartsGroup);
        });
//dd($orderData);
        $event->addData($orderData);
    }

    /**
     * 监听预下单订单分组事件
     * @param ShowPreGenerateOrder $event
     * @return null
     */
    public function onShow(ShowPreGenerateOrder $event)
    {
        $this->event = $event;
        $memberCartsGroups = $this->getSupplierMemberCartsGroups();
        if ($memberCartsGroups->isEmpty()) {
            return null;
        }
        $orderData = $memberCartsGroups->map(function ($memberCartsGroup, $supplier_id) {
            $order_data_item = $this->getSupplierOrderData($memberCartsGroup);
            $order_data_item->put('supplier', Supplier::select('username','id')->find($supplier_id)->toArray());
            return $order_data_item;
        });

        $event->addData($orderData);
    }

    /**
     * 将购物车记录按供应商分组
     * @return Collection
     */
    private function getSupplierMemberCartsGroups()
    {
        //todo 此处啰嗦,需想办法调整
        //dd($this->getSupplierMemberCarts());
        $memberCarts = $this->getSupplierMemberCarts()->map(function ($memberCart) {
            //获取购物车商品对应的供应商
            /**
             * @var MemberCart $memberCart
             */
            $supplier_id = $memberCart->hasOne(SupplierGoodsJoinGoods::class, 'id', 'goods_id')->JoinGoods()->first()->supplier_id;
            $memberCart['supplier_id'] = $supplier_id ? $supplier_id : 0;
            return $memberCart;
        }
        );
//dd($memberCarts);
        return $memberCarts->groupBy('supplier_id');
    }

    /**
     * 获取所有属于插件的购物车记录
     * @return \Illuminate\Support\Collection
     */
    private function getSupplierMemberCarts()
    {
        return MemberCartService::filterPluginMemberCart($this->event->getMemberCarts());
    }

    public function subscribe(Dispatcher $event)
    {
        $event->listen(ShowPreGenerateOrder::class, self::class . '@onShow');
        $event->listen(CreatingOrder::class, self::class . '@onCreate');
    }
}