<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/21
 * Time: 下午3:26
 */

namespace Yunshop\Exhelper\common\models;


use app\common\models\BaseModel;

class ExhelperOrder extends BaseModel
{
    public $table = 'yz_exhelper_order';
    protected $guarded = [''];
    public $timestamps = false;

    public static function getOrderSend($order_sn)
    {
        return self::select()->byOrderSn($order_sn);
    }

    public function scopeByOrderSn($query, $order_sn)
    {
        return $query->where('order_sn', $order_sn);
    }
}