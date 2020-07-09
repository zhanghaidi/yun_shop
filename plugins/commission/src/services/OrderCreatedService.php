<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/10/23
 * Time: 上午11:02
 */

namespace Yunshop\Commission\services;


use Yunshop\Commission\models\YzMember;

class OrderCreatedService
{
    public static function getParentAgents($uid, $selfBuy)
    {
        return YzMember::getParentAgents($uid, $selfBuy)->first();
    }


}