<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-05-15
 * Time: 15:45
 */

namespace Yunshop\Supplier\frontend\models;


use app\common\models\Goods;

class SupplierGoods extends Goods
{
    public $appends = ['change_thumb', 'status_name'];

    public function getChangeThumbAttribute()
    {
        return $this->attributes['change_thumb'] = yz_tomedia($this->attributes['thumb'], true);
    }

    public function getStatusNameAttribute()
    {
        return [0 => '下架', 1 => '上架'][$this->status];
    }
}