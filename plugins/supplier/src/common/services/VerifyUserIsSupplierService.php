<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 下午5:26
 */

namespace Yunshop\Supplier\common\services;

use Yunshop\Supplier\common\models\Supplier;

class VerifyUserIsSupplierService
{
    /**
     * @name 验证是否为供应商
     * @author yangyang
     * @param $supplier_id
     * @return bool|mixed
     */
    public static function verify($supplier_id)
    {
        $result = Supplier::getSupplierById($supplier_id, 1);
        if (!$result) {
            return false;
        }
        return $result;
    }
}