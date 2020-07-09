<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/22
 * Time: 下午2:45
 */

namespace Yunshop\Supplier\common\services\withdraw;


class SupplierWithdrawService
{
    /**
     * @name 验证提现是否存在
     * @author yangyang
     * @param $withdraw
     * @return mixed
     */
    public static function verifyWithdrawIsEmpty($withdraw)
    {
        if (!$withdraw) {
            exit('不存在');
        }
        return $withdraw;
    }
}