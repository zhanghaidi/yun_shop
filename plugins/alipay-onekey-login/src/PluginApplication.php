<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\AlipayOnekeyLogin;


class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }
    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('alipay_onekey_login', [
            'name'              => '支付宝登录',
            'type'              => 'tool',
            'url'               => 'plugin.alipay-onekey-login.admin.set.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'top_show'          => 0,
            'left_first_show'   => 0,
            'left_second_show'  => 1,
            'icon'              => 'fa-hourglass-2',
            'list_icon'         => 'alipay_onekey_login',
            'parents'           => [],
            'child'             => [
                'alipay-onekey-login' => [
                    'name'      => '基础设置',
                    'permit'    => 1,
                    'menu'      => 1,
                    'icon'      => '',
                    'url'       => 'plugin.alipay-onekey-login.admin.set.index',
                    'url_params'=> '',
                    'parents'   => ['alipay_onekey_login'],
                    'child'     => []
                ]
            ]
        ]);

    }

    public function boot()
    {

    }

}