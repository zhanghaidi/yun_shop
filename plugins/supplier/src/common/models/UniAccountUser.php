<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/10/20
 * Time: 下午2:34
 */

namespace Yunshop\Supplier\common\models;


class UniAccountUser extends \app\common\models\user\UniAccountUser
{
    public static function AddUniAccountUser($uid)
    {
        $uni_model = new self();
        $uni_model->fill([
            'uid'       => $uid,
            'uniacid'   => \YunShop::app()->uniacid,
            'role'      => 'clerk'
        ]);
        $uni_model->save();
    }
}