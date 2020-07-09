<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/13
 * Time: 下午4:14
 */

namespace Yunshop\Supplier\supplier\models;


class OrderGoods extends \app\backend\modules\order\models\OrderGoods
{
    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }
}