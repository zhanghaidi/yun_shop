<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 10:41
 */

namespace Yunshop\JdSupply\models;


use app\common\models\BaseModel;

class JdSupplyOrder extends BaseModel
{
    public $table = 'yz_plugin_jd_supply_order';

    protected $guarded = [''];

    protected $attributes = [
        'status' => 0,
    ];

    const PLUGIN_ID = 44; // 京东供应链 订单类型


    public function scopeStatus($query, $status = 0)
    {
        return $query->where('status', $status);
    }


    public static function isJdOrder($order_id)
    {
        return self::where('order_id', $order_id)->first();
    }

    public static function updateStatus($order_id, $status)
    {
        return static::where('order_id', $order_id)->update(['status' => $status]);
    }
}