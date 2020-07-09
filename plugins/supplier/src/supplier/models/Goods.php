<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/13
 * Time: 下午4:15
 */

namespace Yunshop\Supplier\supplier\models;


class Goods extends \app\backend\modules\goods\models\Goods
{
    public function scopeIsPlugin($query)
    {
        return $query->where('is_plugin', 1);
    }
}