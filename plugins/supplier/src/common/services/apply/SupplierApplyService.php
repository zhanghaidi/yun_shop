<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午5:21
 */

namespace Yunshop\Supplier\common\services\apply;

use Yunshop\Supplier\admin\models\Supplier;

class SupplierApplyService
{
    /**
     * @param Supplier $apply
     * @return array
     */
    public static function verifyApply($apply)
    {
        if (!isset($apply)) {
            return [
                'status' => 0,
                'msg'    => '申请不存在！'
            ];
        } else {
            if ($apply->status != 0) {
                return [
                    'status' => 0,
                    'msg'    => '状态不满足审核！'
                ];
            }
            return [
                'status'        => 1,
                'apply_model'   => $apply
            ];
        }
    }
}