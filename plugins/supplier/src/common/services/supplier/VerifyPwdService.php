<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午4:13
 */

namespace Yunshop\Supplier\common\services\supplier;

use Illuminate\Support\Facades\Hash;
use Yunshop\Supplier\common\models\Supplier;

class VerifyPwdService
{
    /**
     * @name 修改供应商密码
     * @author yangyang
     * @param array $pwd
     * @param $supplier_id
     * @return array
     */
    public static function verify(array $pwd, $supplier_id)
    {
        if ($pwd && $pwd['new_pwd'] === $pwd['new_pwd_too']) {
            $supplier = Supplier::getSupplierById($supplier_id)->toArray();
            $password = Supplier::user_hash($pwd['new_pwd'], $supplier['salt']);
            Supplier::where('id', $supplier_id)->update(['password' => $password]);
            return [
                'status' => 1,
                'msg'    => '修改密码成功'
            ];
        }
        return [
            'status' => 0,
            'msg'    => '修改密码失败'
        ];
    }
}