<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/8
 * Time: 10:07
 */

namespace Yunshop\Tbk\common\models;


class Goods extends \app\backend\modules\goods\models\Goods
{
    //protected $appends = ['store_name', 'store_id'];
    static protected $needLog = false;


    public function tbkCoupon() {
        return $this->hasOne(TbkCoupon::class, 'goods_id', 'id');
    }

    public function scopePluginId($query, $pluginId = 188)
    {
        return $query->uniacid()->where('plugin_id', $pluginId);
    }

    public function scopeIsPlugin($query)
    {
        return $query;
    }

    public static function getGoodsList($search)
    {
        return self::select()->search($search);
    }

}