<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/10
 * Time: 下午6:16
 */

namespace Yunshop\Micro\common\services\MicroShop;

use app\common\models\MemberShopInfo;
use Yunshop\Micro\common\models\MicroShop;

class MicroShopService
{
    public static $lower_total = 0;

    /**
     * @name 验证url上的micro_shop_id是否为微店
     * @author 杨洋
     * @return bool
     */
    public static function verifyMicroShopByUrlId($shop_id)
    {
        if (!isset($shop_id)) {
            return false;
        }
        $micro_shop_model = MicroShop::getMicroShopById($shop_id);
        if ($micro_shop_model) {
            return $micro_shop_model;
        }
        return false;
    }

    /**
     * @name 统计一级下线微店人数
     * @author 杨洋
     * @param $member_id
     * @return int
     */
    public static function getLowerTotal($member_id)
    {
        $lowers = MemberShopInfo::select()->uniacid()->where('parent_id', $member_id)->where('is_black', 0)->get();
        if ($lowers->isEmpty()) {
            return self::$lower_total;
        }
        foreach ($lowers as $row) {
            if (MicroShop::getMicroShopByMemberId($row->member_id)) {
                self::$lower_total += 1;
            }
        }
        return self::$lower_total;
    }

    /**
     * @name 验证上级是否为微店
     * @author 杨洋
     * @param $member_id
     * @return bool
     */
    public static function verifyAgentMicroShop($member_id)
    {
        $member = MemberShopInfo::getMemberShopInfo($member_id);
        if ($member->parent_id == 0) {
            return false;
        }
        /*$micro = MicroShop::getMicroShopByMemberId($member_id);*/
        $agent = MicroShop::getMicroShopByMemberId($member->parent_id);
        if (!$agent) {
            return false;
        }
        /*if ($agent->hasOneMicroShopLevel->level_weight <= $micro->hasOneMicroShopLevel->level_weight) {
            return false;
        }*/
        return $agent;
    }
}