<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/9
 * Time: 下午7:33
 */

namespace Yunshop\Micro\common\services\MicroShopLevel;

use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopLevel;

class LevelService
{
    /**
     * @name 当前等级下是否存在微店
     * @author 杨洋
     * @param $id
     * @return bool
     */
    public static function thisLevelExistsMiceoShop($id)
    {
        $micro_shop = MicroShop::getMicroShopByLevelId($id);
        if (!$micro_shop->isEmpty()) {
            return true;
        }
        return false;
    }

    /**
     * @name 判断购买的商品是不是微店等级选择的商品
     * @author 杨洋
     * @param $goods_id
     * @return bool
     */
    public static function verifyGoodsBelongLevel($goods_id)
    {
        $level_result = MicroShopLevel::getLevelByGoodsId($goods_id);
        if (isset($level_result)) {
            return $level_result;
        }
        return false;
    }
}