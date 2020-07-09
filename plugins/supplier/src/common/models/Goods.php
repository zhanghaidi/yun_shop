<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Supplier\common\models;


class Goods extends \app\backend\modules\goods\models\Goods
{
    public function scopeIsPlugin($query)
    {
        return $query->where('is_plugin', 1);
    }

    public function hasOneSupplierGoods()
    {
        return $this->hasOne(SupplierGoods::class, 'goods_id', 'id');
    }

    public static function getGoodsList($search)
    {
        return self::whereHas('hasOneSupplierGoods')->select()->search($search);
    }
}