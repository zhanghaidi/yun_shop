<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/4
 * Time: 上午11:00
 */

namespace Yunshop\RechargeCode\common\services;


use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;

class RechargeBalance extends BalanceChange
{
    public function rechargeCode(array $data)
    {
        $this->source = ConstService::SOURCE_RECHARGE_CODE;
        return $this->addition($data);
    }
}