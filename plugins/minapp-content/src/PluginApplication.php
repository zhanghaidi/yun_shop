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
            'url' => 'plugin.custom-app.admin.article-sort.index', // url 可以填写http 也可以直接写路由
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
                'article_sort_manage' => [
                    'name' => '文章管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.custom-app.admin.article-sort.index',
                    'url_params' => '',
                    'parents' => ['custom_app'],
                    'child' => [
                        'article_sort_add' => [
                            'name' => '添加分类',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.custom-app.admin.article-sort.add',
                            'parents' => ['custom_app', 'article_sort_manage'],
                        ],
                    ],
                ],

            ],
        ]);
    }
}
