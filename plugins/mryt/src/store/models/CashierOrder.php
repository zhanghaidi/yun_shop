<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/1
 * Time: 下午5:19
 */

namespace Yunshop\Mryt\store\models;

use app\common\models\BaseModel;
use app\common\models\order\OrderCoupon;
use app\common\models\order\OrderDeduction;

class CashierOrder extends BaseModel
{
    public $table = 'yz_plugin_cashier_order';
    public $timestamps = true;
    protected $guarded = [''];

    const NOT_HAS_SETTLEMENT = 0;
    const HAS_SETTLEMENT = 1;
    const HAS_WITHDRAW = 1;
    const NOT_HAS_WITHDRAW = 0;
    const INCOME_TYPE_NAME = '收银台提现';

    public static function getListByHasWithdraw($has_withdraw)
    {
        return self::select()->byHasWithdraw($has_withdraw);
    }

    public static function getListByHasSettlement($has_settlement)
    {
        return self::select()->byHasSettlement($has_settlement);
    }

    public static function getListByCashierIdAndByHasSettlement($cashier_id, $has_settlement)
    {
        return self::select()->byCashierId($cashier_id)->byHasSettlement($has_settlement);
    }

    public static function getCashierOrderByOrderId($order_id)
    {
        return self::select()->byOrderId($order_id);
    }

    public function scopeByPayTypeId($query, $pay_type_id)
    {
        return $query->where('pay_type_id', '!=', $pay_type_id);
    }

    public function scopeByHasWithdraw($query, $has_withdraw)
    {
        return $query->where('has_withdraw', $has_withdraw);
    }

    public function scopeByCashierId($query, $cashier_id)
    {
        return $query->where('cashier_id', $cashier_id);
    }

    public function scopeByOrderId($query, $order_id)
    {
        return $query->where('order_id', $order_id);
    }

    public function scopeByHasSettlement($query, $has_settlement)
    {
        return $query->where('has_settlement', $has_settlement);
    }

    public function scopeByOrderSearch($query, $cashier_id)
    {
        if ($cashier_id) {
            $query->where('cashier_id', $cashier_id);
        }
        return $query;
    }

    public function order()
    {
        return $this->hasOne(\app\backend\modules\order\models\Order::class, 'id', 'order_id');
    }

    public function hasOneOrder()
    {
        return $this->hasOne(CashierShopOrder::class, 'id', 'order_id');
    }

    public function hasOneStore()
    {
        return $this->hasOne(Store::class, 'cashier_id', 'cashier_id');
    }

    public function hasManyGiveReward()
    {
        return $this->hasMany(GiveReward::class, 'order_id', 'order_id');
    }

    public function hasManyGiveCoupon()
    {
        return $this->hasMany(GiveCoupon::class, 'order_id', 'order_id');
    }

    public function hasManyOrderDeduction()
    {
        return $this->hasMany(OrderDeduction::class, 'order_id', 'order_id');
    }

    public function hasManyCoupon()
    {
        return $this->hasMany(OrderCoupon::class, 'order_id', 'order_id');
    }

    public function scopeSearch($query, $cashierOrderSearch)
    {
        return $query->whereHas('hasOneStore', function($storeBuilder)use($cashierOrderSearch){
            $storeBuilder->search($cashierOrderSearch);
        });
    }
}