<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/28
 * Time: 下午5:23
 */

namespace Yunshop\Supplier\common\services\withdraw;


use Yunshop\Supplier\common\models\SupplierGoods;

class IsOwnerSupplier
{
    public static function verify($order)
    {
        $is_supplier = false;
        $goods_id = $order->hasManyOrderGoods->first()->goods_id;
        $supplier_goods = SupplierGoods::getSupplierGoodsById($goods_id);
        if ($supplier_goods) {
            $is_supplier = true;
        }
        return $is_supplier;
    }
}