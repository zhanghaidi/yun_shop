<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 下午5:34
 */

namespace Yunshop\Supplier\supplier\models;


use app\backend\modules\order\models\Order;
use Setting;

class SupplierOrder extends \Yunshop\Supplier\common\models\SupplierOrder
{
    /**
     * @name 获取指定供应商订单列表
     * @author yangyang
     * @param $supplier_id
     * @param null $limit_time
     * @return mixed
     */
    public static function getSupplierOrder($supplier_id, $limit_time = null)
    {
        $order_information = SupplierOrder::select()->where('apply_status', 0)->id($supplier_id)->byOrder()->get();
        return $order_information;
    }

    public static function getOrderBySupplierIdAndOrderId($sid, $order_id)
    {
        return self::select()->where('supplier_id', $sid)->where('order_id', $order_id);
    }

    /**
     * @name 关联商城订单表
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneOrder()
    {
        return $this->hasOne(\app\common\models\Order::class, 'id', 'order_id');
    }

    public function scopeId($query, $id)
    {
        return $query->where('supplier_id', $id);
    }

    public static function updateApplyStatus($value, $data)
    {
        SupplierOrder::whereIn('order_id', $data)->update(['apply_status' => $value]);
    }

    public function scopeByOrder($query)
    {
        $set = Setting::get('plugin.supplier');
        $time = time() - $set['apply_day'] * 86400;
        return $query->whereHas('hasOneOrder', function($order)use($time) {
                $order->select('id')
                    ->where('status', 3)
                    ->where('finish_time', '<', $time);
            });
    }
}