<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/21
 * Time: 下午2:36
 */

namespace Yunshop\Exhelper\common\models;


class OrderGoods extends \app\backend\modules\order\models\OrderGoods
{
    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }
}