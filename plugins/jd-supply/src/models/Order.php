<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/17
 * Time: 15:54
 */

namespace Yunshop\JdSupply\models;


class Order extends \app\backend\modules\order\models\Order
{

    public function scopePluginId($query, $pluginId = JdSupplyOrder::PLUGIN_ID)
    {
        return parent::scopePluginId($query, $pluginId); // TODO: Change the autogenerated stub
    }

    //获取会员订单数量
    public static function getOrderNum($member_id, $plugin_id = JdSupplyOrder::PLUGIN_ID)
    {
        return self::uniacid()->where('uid', $member_id)->pluginId($plugin_id);
    }

    public function hasOneJdSupplyOrder()
    {
        return $this->hasOne(JdSupplyOrder::class, 'order_id', 'id');
    }

    public function hasManyJdSupplyOrderGoods()
    {
        return $this->hasMany(JdSupplyOrderGoods::class, 'order_id', 'id');
    }


}