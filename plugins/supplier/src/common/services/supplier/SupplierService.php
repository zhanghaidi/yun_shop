<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午4:13
 */

namespace Yunshop\Supplier\common\services\supplier;

use app\common\traits\MessageTrait;
use Yunshop\Supplier\common\models\Supplier;

class SupplierService
{
    use MessageTrait;

    /**
     * @name 验证供应商是否存在
     * @author yangyang
     * @param Supplier $supplier
     * @return bool|Supplier
     */
    public static function verifySupplierIsEmpty(Supplier $supplier)
    {
        if (!$supplier) {
            return false;
        }
        return $supplier;
    }

    /**
     * @name 验证该会员是否已经绑定供应商
     * @author yangyang
     * @param $member_id
     * @return mixed
     */
    public static function verifyMemberIsBind($member_id)
    {
        $result = Supplier::getSupplierByMemberId($member_id);
        return $result;
    }

    public static function verifyMemberIsRepeat($member_id, $supplier_id)
    {
        $repeat = false;
        $result = self::verifyMemberIsBind($member_id);
        if ($result) {
            if (($result->id == $supplier_id)) {
                $repeat = true;
            }
        } else {
            $repeat = true;
        }
        return $repeat;
    }
}