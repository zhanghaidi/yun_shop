<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午3:28
 */

namespace Yunshop\Supplier\common\services\goods;

use Yunshop\Supplier\admin\models\SupplierGoods;

class SupplierGoodsService
{
    public static function verifyGoodsIsEmpty(SupplierGoods $goods)
    {
        if (!$goods) {
            exit('不存在');
        }
        return $goods;
    }
}