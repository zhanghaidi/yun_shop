<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 14:13
 */

namespace Yunshop\JdSupply\models;


class Goods extends \app\backend\modules\goods\models\Goods
{
    public function scopePluginId($query, $pluginId = JdSupplyOrder::PLUGIN_ID)
    {
        return $query->where('plugin_id', $pluginId);
    }

    public function hasOneJdGoods()
    {
        return $this->hasOne(JdGoods::class, 'goods_id', 'id');
    }
}