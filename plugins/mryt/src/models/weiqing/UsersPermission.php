<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/1
 * Time: 下午5:42
 */

namespace Yunshop\Mryt\models\weiqing;


use app\common\models\BaseModel;

class UsersPermission extends BaseModel
{
    public $table = 'users_permission';
    protected $guarded = [''];
    public $timestamps = false;
    protected $attributes = [
        'url'   => ''
    ];

    public function addPermission($user_uid)
    {
        $permission = [
            'uniacid'       => \YunShop::app()->uniacid,
            'uid'           => $user_uid,
            'type'          => 'yun_shop',
            'permission'    => 'yun_shop_rule|yun_shop_menu_shop',
            'url'           => 'www.yunzshop.com'
        ];
        if ($this->hasColumn('modules')) {
            $permission['modules'] = 'yunzhong';
        }
        if ($this->hasColumn('templates')) {
            $permission['templates'] = 'yunzhong';
        }
        $model = new UsersPermission();
        $model->fill($permission);
        $model->save();
    }
}