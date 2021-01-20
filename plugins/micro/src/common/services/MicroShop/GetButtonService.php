<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/19
 * Time: 下午3:02
 */

namespace Yunshop\Micro\common\services\MicroShop;


use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopGoods;

class GetButtonService
{
    public static function verify($member_id)
    {
        $micro_shop = MicroShop::getMicroShopByMemberId($member_id);
        if (!$micro_shop) {
            return [
                'value'     => '我要开店',
                'status'    => 0
            ];
        } else {
            return [
                'value'     => '微店中心',
                'status'    => 1
            ];
            /*$micro_shop_goods = MicroShopGoods::getGoodsByMemberId($member_id);
            if ($micro_shop_goods->isEmpty()) {
                return [
                    'status'    => -1
                ];
            } else {
                return [
                    'value'     => '微店中心',
                    'status'    => 1
                ];
            }*/
        }
    }
}