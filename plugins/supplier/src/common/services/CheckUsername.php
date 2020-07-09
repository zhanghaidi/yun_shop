<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/10/26
 * Time: 下午5:37
 */

namespace Yunshop\Supplier\common\services;


use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\models\WeiQingUsers;

class CheckUsername
{
    public static function update()
    {
        $supplier_list = Supplier::select('id', 'username', 'uid')->status(1)->where('uid', '!=', 0)->get();
        if (!$supplier_list->isEmpty()) {
            $supplier_list->each(function($supplier){
                $user = WeiQingUsers::getUserByUid($supplier->uid)->first();
                if ($user && ($user->username !== $supplier->username)) {
                    $supplier->username = $user->username;
                    $supplier->save();
                }
            });
        }
    }
}