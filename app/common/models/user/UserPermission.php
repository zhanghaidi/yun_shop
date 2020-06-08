<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 02/03/2017
 * Time: 18:27
 */

namespace app\common\models\user;


use app\common\models\BaseModel;

class UserPermission extends BaseModel
{
    public $table = 'users_permission';

    protected $guarded = [''];

    public $timestamps = false;

    public function __construct()
    {
        if (config('app.framework') == 'platform') {
            $this->table = 'yz_users_permission';
        }
    }

    final function addUserPermission($userId)
    {
        return $this->insert([
            'uniacid' => \YunShop::app()->uniacid,
            'uid' => $userId,
            'type' => 'yun_shop',
            'permission' => 'all',
            'url' => ''
        ]);
    }
}