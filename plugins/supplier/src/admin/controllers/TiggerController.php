<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/12
 * Time: 下午8:29
 */

namespace Yunshop\Supplier\admin\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\user\UniAccountUser;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\models\UsersPermission;
use Yunshop\Supplier\common\models\WeiQingUsers;

class TiggerController extends BaseController
{
    public function handle()
    {
        $suppliers = Supplier::getSupplierList(null, 1)->get();
        $suppliers->each(function($item){
            $res = WeiQingUsers::getUserByUserName($item->username)->first($item->username);
            if (!$res) {
                $uid = user_register(array('username' => $item->username, 'password' => '000000'));

                //todo 公众号权限
                $uni_model = new UniAccountUser();
                $uni_model->fill([
                    'uid'       => $uid,
                    'uniacid'   => $item->uniacid,
                    'role'      => 'operator'
                ]);
                $uni_model->save();

                $item->uid = $uid;
                $item->password = '000000';
                $item->save();

                // todo 模块权限
                $perm_model = new UsersPermission();
                $perm_model->fill([
                    'uniacid'       => \YunShop::app()->uniacid,
                    'uid'           => $uid,
                    'type'          => 'yun_shop',
                    'permission'    => 'yun_shop_rule|yun_shop_menu_shop',
                    'url'           => 'www.yunzshop.com'
                ]);
                $perm_model->save();
            }
        });
        return $this->message('更改供应商数据成功', Url::absoluteWeb('plugin.supplier.admin.controllers.supplier.supplier-list.index'));
    }
}