<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/29
 * Time: 上午11:05
 */

namespace Yunshop\Supplier\common\services\apply;


class VerifyUserIsApplyService
{
    const IS_SUPPLIER = -1; //todo 已经成为供应商，无需再申请
    const REPEAT_APPLY = 0; //todo 已经提交申请，等待审核。
    const VISIT_SUCCESS = 1; //todo 访问成功

    public static function verifyUserIsApply($apply)
    {
        if ($apply) {
            if ($apply->status == self::REPEAT_APPLY) {
                return self::REPEAT_APPLY;
            } else if ($apply->status == self::VISIT_SUCCESS) {
                return self::IS_SUPPLIER;
            }
        }
        return null;
    }
}