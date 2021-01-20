<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/23
 * Time: 下午4:57
 */

namespace Yunshop\Micro\common\models;


class Order extends \app\backend\modules\order\models\Order
{
    public function scopeIsPlugin($query)
    {
        return $query;
    }
}