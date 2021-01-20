<?php

namespace Yunshop\LeaseToy\models;

use Illuminate\Support\Facades\DB;
use Yunshop\LeaseToy\models\LeaseOrderModel;
use Yunshop\LeaseToy\models\OrderGoods;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/3/14
* Time: 16:31
*/
class Order extends \app\backend\modules\order\models\Order
{

    protected $appends = ['status_name', 'pay_type_name', 'lease_toy'];


    static public function getLeaseOrderList($search)
    {
        return self::orders($search);
    }    

    public function scopeOrders($order_builder, $search)
    {
        return parent::scopeOrders($order_builder, $search)->pluginId()->with([
            'hasOneLeaseToyOrder' => self::leaseReturnBuilder(),
            'hasOneAreaLeaseLog'
        ]);

    }
   
    static protected function leaseReturnBuilder()
    {
        return function ($query) {
            return $query->with(['LeaseAddress', 'LeaseExpress']);
        };
    }

    public function getLeaseToyAttribute()
    {
        if ($this->hasOneLeaseToyOrder) {
            return [
                'deposit_total' => $this->hasOneLeaseToyOrder->deposit_total,
                'days' => $this->hasOneLeaseToyOrder->return_days,
                'end_time' => $this->hasOneLeaseToyOrder->end_time,
            ];
        }
    }

     public function hasManyOrderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'id');
    }

    public function hasOneLeaseToyOrder()
    {
        return $this->hasOne(LeaseOrderModel::class, 'order_id', 'id');
    }

    public function hasOneAreaLeaseLog()
    {
        return $this->hasOne(AreaLeaseReturnLogModel::class, 'order_id', 'id');
    }

    public function scopePluginId($query, $pluginId = 0)
    {
        return $query->wherePluginId(LeaseOrderModel::PLUGIN_ID);
    }
}