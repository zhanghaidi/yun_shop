<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\RechargeCode;


class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {

    }
    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('recharge_code', [
            'name' => '充值码',
            'type' => 'marketing',
            'url' => 'plugin.recharge-code.admin.list.index',// url 可以填写http 也可以直接写路由
            'urlParams' => '',//如果是url填写的是路由则启用参数否则不启用
            'permit' => 1,//如果不设置则不会做权限检测
            'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
            'top_show'    => 0,
            'left_first_show'   => 0,
            'left_second_show'   => 1,
            'icon' => 'fa-ticket',//菜单图标
            'list_icon' => 'recharge_code',
            'parents' => [],
            'child' => [
                'recharge_code.add' => [
                    'name' => '生成充值码',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-plus-square',
                    'url' => 'plugin.recharge-code.admin.create.index',
                    'url_params' => '',
                    'parents' => ['recharge_code'],
                    'child' => [
                    ]
                ],
                'recharge_code.list' => [
                    'name' => '充值码列表',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-clipboard',
                    'url' => 'plugin.recharge-code.admin.list.index',
                    'url_params' => '',
                    'parents' => ['recharge_code'],
                    'child' => [
                    ]
                ],
                'recharge_code.set' => [
                    'name' => '基础设置',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.recharge-code.admin.set.index',
                    'url_params' => '',
                    'parents' => ['recharge_code'],
                    'child' => [
                    ]
                ]
            ]
        ]);

    }

    public function boot()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Recharge-Code', '*/5 * * * *', function () {
                (new \Yunshop\RechargeCode\common\services\TimedTaskService())->handle();
                return;
            });
        });
    }

}