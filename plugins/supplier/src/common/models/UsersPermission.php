<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/12
 * Time: 下午8:07
 */

namespace Yunshop\Supplier\common\models;


use app\common\models\BaseModel;

class UsersPermission extends BaseModel 
{
    public $table = 'users_permission';
    protected $guarded = [''];
    public $timestamps = false;
    protected $attributes = [
        'url'   => ''
    ];

    public function __construct()
    {
        if (config('app.framework') == 'platform') {
            $this->table = 'yz_users_permission';
        }
    }

    public function addUsersPermission($uid)
    {
        $permission = [
            'uniacid'       => \YunShop::app()->uniacid,
            'uid'           => $uid,
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