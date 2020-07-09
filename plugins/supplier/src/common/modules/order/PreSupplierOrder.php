<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/26
 * Time: 3:31 PM
 */

namespace Yunshop\Supplier\common\modules\order;

use app\common\models\GoodsOption;
use app\common\models\Order;
use Setting;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\models\SupplierOrder;

class PreSupplierOrder extends SupplierOrder
{
    /**
     * @var Order
     */
    private $order;

    public function afterCreating()
    {
        $this->fill([
            'member_id' => $this->getMemberId(),
            'supplier_id' => $this->getSupplierId(),
            'uniacid' => \YunShop::app()->uniacid,
        ]);
    }

    public function init(Order $order)
    {
        $this->order = $order;

    }

    public function initAttributes()
    {
        $attributes = [
            'supplier_profit' => $this->getSupplierProfit(),
            'order_goods_information' => $this->getOrderGoodsInformation(),
        ];
        $attributes = array_merge($this->getAttributes(), $attributes);
        $this->setRawAttributes($attributes);
    }

    private function getSupplierId()
    {
        return $this->order->orderGoods->first()->goods->supplierGoods->supplier_id;
    }

    private function getMemberId()
    {
        return $this->order->orderGoods->first()->goods->supplierGoods->member_id;

    }

    private function getSupplierProfit()
    {
        $set = Setting::get('plugin.supplier');
        if (!$set['culate_method']) {
            return $this->order->cost_amount + $this->order->dispatch_price;
        } else {
            $shopCommission = $this->getShopCommission($set['shop_commission']);
            return $this->order->order_goods_price * (1 - $shopCommission / 100) + $this->order->dispatch_price;
        }
    }

    private function getShopCommission($shopCommission)
    {
        $supplierModel = Supplier::find($this->getSupplierId());
        if ($supplierModel->shop_commission > 0) {
            $shopCommission = $supplierModel->shop_commission;
        }
        if ($shopCommission > 100) {
            $shopCommission = 100;
        }
        return $shopCommission;
    }

    private function getOrderGoodsInformation()
    {
        $orderGoodsInformation = $this->order->orderGoods->map(function ($orderGoods) {
            $order_goods = [
                'goods_id' => $orderGoods->goods_id,
                'option_id' => $orderGoods->goods_option_id,
                'cost_price' => $orderGoods->goods_cost_price
            ];
            return $order_goods;
        })->toArray();
        return serialize($orderGoodsInformation);
    }
}