<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/27
 * Time: 下午2:19
 */

namespace Yunshop\Mryt\store\models;



class CashierShopOrder extends Order
{
    public function scopePluginId($query)
    {
        return $query->where('plugin_id', Store::CASHIER_PLUGIN_ID);
    }

    public function hasManyCashierOrder()
    {
        return $this->hasMany(CashierOrder::class, 'order_id', 'id');
    }
}