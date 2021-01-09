<?php

namespace Yunshop\MinappContent;

use Yunshop\MinappContent\services\MinappContentService;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu(MinappContentService::get(), [
            'name' => MinappContentService::get('name'),
            'type' => 'tool',
            'url' => 'plugin.minapp-content.admin.acupoint.index', // url 可以填写http 也可以直接写路由
            'url_params' => '', //如果是url填写的是路由则启用参数否则不启用
            'permit' => 1, //如果不设置则不会做权限检测
            'menu' => 1, //如果不设置则不显示菜单，子菜单也将不显示
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => '', //菜单图标
            'list_icon' => 'declaration',
            'parents' => [],
            'child' => [
                'acupoint_manage' => [
                    'name' => '穴位管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.minapp-content.admin.acupoint.index',
                    'url_params' => '',
                    'parents' => ['minapp_content'],
                    'child' => [
                        'acupoint_edit' => [
                            'name' => '编辑穴位',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.acupoint.edit',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'acupoint_del' => [
                            'name' => '删除穴位',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.acupoint.delete',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'meridian_manage' => [
                            'name' => '经络管理',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.index',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'meridian_edit' => [
                            'name' => '编辑经络',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.edit',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'meridian_del' => [
                            'name' => '删除经络',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.delete',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'course_hour' => [
                            'name' => '经络关联课时列表',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.course-hour',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                        'course_hour' => [
                            'name' => '经络所属穴位',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.minapp-content.admin.meridian.acupoints',
                            'parents' => ['minapp_content', 'acupoint_manage'],
                        ],
                    ],
                ],

            ],
        ]);
    }
}
