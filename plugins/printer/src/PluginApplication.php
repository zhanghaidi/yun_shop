<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\Printer;
use Config;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
        \app\common\modules\shop\ShopConfig::current()->set('printer_owner', [
            'owner' => 1,
            'owner_id' => 0
        ]);

    }

    protected function setMenuConfig()
    {

        $printer_menu = [
            'printer_list' => [
                'name'              => '打印机管理',
                'url'               => 'plugin.printer.admin.list.index',
                'url_params'        => '',
                'permit'            => 0,
                'menu'              => 1,
                'icon'              => 'fa-list',
                'item'              => 'printer_list',
                'parents'           => ['printer'],
                'child'             => [

                    'printer_add' => [
                        'name'              => '添加',
                        'url'               => 'plugin.printer.admin.list.add',
                        'url_params'        => '',
                        'permit'            => 0,
                        'menu'              => 0,
                        'icon'              => '',
                        'item'              => 'printer_add',
                        'parents'           => ['printer','printer_list'],
                    ],

                    'printer_edit' => [
                        'name'              => '修改',
                        'url'               => 'plugin.printer.admin.list.edit',
                        'url_params'        => '',
                        'permit'            => 0,
                        'menu'              => 0,
                        'icon'              => '',
                        'item'              => 'printer_edit',
                        'parents'           => ['printer','printer_list'],
                    ],

                    'printer_del' => [
                        'name'              => '删除',
                        'url'               => 'plugin.printer.admin.list.del',
                        'url_params'        => '',
                        'permit'            => 0,
                        'menu'              => 0,
                        'icon'              => '',
                        'item'              => 'printer_del',
                        'parents'           => ['printer','printer_list'],
                    ],

                    'printer_change_status' => [
                        'name'              => '更改状态',
                        'url'               => 'plugin.printer.admin.list.change-status',
                        'url_params'        => '',
                        'permit'            => 0,
                        'menu'              => 0,
                        'icon'              => '',
                        'item'              => 'printer_change_status',
                        'parents'           => ['printer','printer_list'],
                    ]
                ]
            ],

            'temp_list' => [
                'name'              => '模板库管理',
                'url'               => 'plugin.printer.admin.temp.index',
                'url_params'        => '',
                'permit'            => 0,
                'menu'              => 1,
                'icon'              => 'fa-list',
                'item'              => 'temp_list',
                'parents'           => ['printer'],
                'child'             => [

                    'temp_list_add' => [
                        'name'              => '添加',
                        'url'               => 'plugin.printer.admin.temp.add',
                        'url_params'        => '',
                        'permit'            => 0,
                        'menu'              => 0,
                        'icon'              => 'fa-clipboard',
                        'item'              => 'temp_list_add',
                        'parents'           => ['printer','temp_list'],
                    ],

                    'temp_list_edit' => [
                        'name'              => '修改',
                        'url'               => 'plugin.printer.admin.temp.edit',
                        'url_params'        => '',
                        'permit'            => 0,
                        'menu'              => 0,
                        'icon'              => 'fa-clipboard',
                        'item'              => 'temp_list_edit',
                        'parents'           => ['printer','temp_list'],
                    ],

                    'temp_list_del' => [
                        'name'              => '删除',
                        'url'               => 'plugin.printer.admin.temp.del',
                        'url_params'        => '',
                        'permit'            => 0,
                        'menu'              => 0,
                        'icon'              => '',
                        'item'              => 'temp_list_del',
                        'parents'           => ['printer','temp_list'],
                    ],


                    'temp_list_tpl' => [
                        'name'              => '添加建',
                        'url'               => 'plugin.printer.admin.temp.tpl',
                        'url_params'        => '',
                        'permit'            => 0,
                        'menu'              => 0,
                        'icon'              => '',
                        'item'              => 'temp_list_tpl',
                        'parents'           => ['printer','temp_list'],
                    ]
                ]
            ],

            'printer_set' => [
                'name'              => '打印机设置',
                'url'               => 'plugin.printer.admin.set.index',
                'url_params'        => '',
                'permit'            => 0,
                'menu'              => 1,
                'icon'              => 'fa-cogs',
                'item'              => 'printer_set',
                'parents'           => ['printer'],
                'child'             => []
            ]
        ];

        \app\backend\modules\menu\Menu::current()->setPluginMenu('printer', [
            'name' => '打印机',
            'type' => 'tool',
            'url' => 'plugin.printer.admin.list.index',// url 可以填写http 也可以直接写路由
            'urlParams' => '',//如果是url填写的是路由则启用参数否则不启用
            'permit' => 0,//如果不设置则不会做权限检测
            'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
            'top_show'    => 0,
            'left_first_show'   => 0,
            'left_second_show'   => 1,
            'icon' => 'fa-print',//菜单图标
            'list_icon' => 'printer',
            'parents' => [],
            'child' => $printer_menu
        ]);
    }

    public function boot()
    {
        $events = app('events');

        $events->subscribe(\Yunshop\Printer\common\listeners\OrderCreatedListener::class);

        $events->subscribe(\Yunshop\Printer\common\listeners\OrderPaidListener::class);
    }

}