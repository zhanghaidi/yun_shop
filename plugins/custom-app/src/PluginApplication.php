<?php

namespace Yunshop\CustomApp;

use Yunshop\CustomApp\services\CustomAppService;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu(CustomAppService::get(), [
            'name' => CustomAppService::get('name'),
            'type' => 'tool',
            'url' => 'plugin.custom-app.admin.article-sort.index', // url 可以填写http 也可以直接写路由
            'url_params' => '', //如果是url填写的是路由则启用参数否则不启用
            'permit' => 1, //如果不设置则不会做权限检测
            'menu' => 1, //如果不设置则不显示菜单，子菜单也将不显示
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => '', //菜单图标
            'list_icon' => 'app_set',
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
                        'article_edit' => [
                            'name' => '编辑文章内容',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.custom-app.admin.article.edit',
                            'parents' => ['custom_app', 'article_sort_manage'],
                        ],
                    ],
                ],

                'element_sort_manage' => [
                    'name' => '元素管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.custom-app.admin.element-sort.index',
                    'url_params' => '',
                    'parents' => ['custom_app'],
                    'child' => [
                        'element_sort_add' => [
                            'name' => '添加元素',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.custom-app.admin.element-sort.add',
                            'parents' => ['custom_app', 'element_sort_manage'],
                        ],
                        'element_edit' => [
                            'name' => '编辑元素内容',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.custom-app.admin.element.edit',
                            'parents' => ['custom_app', 'element_sort_manage'],
                        ],
                    ],
                ],

            ],
        ]);
    }
}
