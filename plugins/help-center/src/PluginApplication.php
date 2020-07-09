<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\HelpCenter;


use Yunshop\HelpCenter\services\HelpCenterService;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {


    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('help_center', [
            'name' => (new HelpCenterService)->get('plugin_name'),
            'type' => 'tool',
            'url' => 'plugin.help-center.admin.help-center-manage.index',// url 可以填写http 也可以直接写路由
            'url_params' => '',//如果是url填写的是路由则启用参数否则不启用
            'permit' => 1,//如果不设置则不会做权限检测
            'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => '',//菜单图标
            'list_icon' => 'help_center',
            'parents' => [],
            'child' => [
                'help_center_manage' => [
                    'name' => '帮助管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.help-center.admin.help-center-manage.index',
                    'url_params' => '',
                    'parents' => ['help_center'],
                    'child' => [

                        'add_help' => [
                            'name' => '添加帮助',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.help-center.admin.help-center-add.index',
                            'parents' => ['help_center', 'help_center_manage'],
                        ],
                        'edit' => [
                            'name' => (new HelpCenterService)->get('plugin_name') . '编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.help-center.admin.help-center-add.edit',
                            'parents' => ['help_center', 'help_center_manage'],
                        ],
                        'delete' => [
                            'name' => (new HelpCenterService)->get('plugin_name') . '删除',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.help-center.admin.help-center-add.del',
                            'parents' => ['help_center', 'help_center_manage'],
                        ],
                    ]
                ],

                'help_center_set' => [
                    'name' => '基础设置',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.help-center.admin.help-center-set.index',
                    'url_params' => '',
                    'parents' => ['help_center'],
                ],

            ]
        ]);
    }

    public function boot()
    {

    }

}