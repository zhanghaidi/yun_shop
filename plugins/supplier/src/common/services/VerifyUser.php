<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午7:58
 */

namespace Yunshop\Supplier\common\services;

use app\common\services\Session;
use Yunshop\Supplier\common\models\Supplier;

class VerifyUser
{
    /**
     * @name 登录验证用户
     * @author yangyang
     * @param $user
     * @return bool
     */
    public static function verifyUser($user)
    {
        if ($user) {
            if ($user['username'] && $user['password']) {
                $supplier = Supplier::getSupplierByUsername($user['username']);
                if ($supplier) {
                    $password = Supplier::user_hash($user['password'], $supplier->toArray()['salt']);
                    if ($password == $supplier->toArray()['password']) {
                        session_start();
                        Session::set('supplier', $supplier->toArray(), '86400');
                        return true;
                    }
                }
            }
        }
        return false;
    }
}