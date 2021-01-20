<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/10
 * Time: 下午7:51
 */

namespace Yunshop\Micro\common\services\MicroShopGoods;

use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopGoods;

class MicroShopGoodsService
{
    /**
     * @name 验证商品是否属于该微店
     * @author 杨洋
     * @return bool
     */
    public static function verifyGoodsBelongToMicroShop($shop_id, $goods_id)
    {
        $goods = MicroShopGoods::getGoods($shop_id, $goods_id);
        if (isset($goods)) {
            return $goods;
        }
        return false;
    }
}