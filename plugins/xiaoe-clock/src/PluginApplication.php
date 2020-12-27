<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\XiaoeClock;


use app\backend\modules\menu\Menu;
use Config;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
        /**
         * 设置菜单 config
         */
    }


    protected function setMenuConfig()
    {
        /**
         * 菜单、权限、路由
         */

        Menu::current()->setPluginMenu('xiaoe_clock', [
            'name' => '打卡活动',
            'type' => 'marketing',
            'url' => 'plugin.xiaoe-clock.admin.set.index',
            'url_params' => '',
            'permit' => 1,
            'menu' => 1,
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => 'fa-circle',
            'list_icon' => 'clock_in',
            'parents' => [],
            'child' => [
                'circle' => [
                    'name' => '打卡设置',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.xiaoe-clock.admin.set.index',
                    'url_params' => '',
                    'parents' => ['xiaoe_clock'],
                    'child' => []
                ],
                'clock' => [
                    'name' => '打卡管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.xiaoe-clock.admin.clock.clock_index',
                    'url_params' => '',
                    'parents' => ['xiaoe_clock'],
                    'child' => [
                        'clock_add' => [
                            'name' => '添加打卡活动',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.clock_add',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],
                        'clock_edit' => [
                            'name' => '编辑打卡活动',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.clock_edit',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],
                        'clock_task_list' => [
                            'name' => '主题|作业列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.clock_task_list',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],
                        'clock_task_edit' => [
                            'name' => '主题|作业编辑',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.clock_task_edit',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],
                        'clock_task_add' => [
                            'name' => '主题|作业添加',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.clock_task_add',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],
                        'clock_add' => [
                            'name' => '搜索课程',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.get_search_course',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],
                        'users_clock_list' => [
                            'name' => '日记列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.users_clock_list',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],
                        'users_clock_del' => [
                            'name' => '日记删除',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.users_clock_del',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],
                        'users_clock_detail' => [
                            'name' => '日记详情',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.users_clock_detail',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],
                        'users_clock_comment_del' => [
                            'name' => '评论删除',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.users_clock_comment_del',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],
                        'clock_users_list' => [
                            'name' => '学员列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.xiaoe-clock.admin.clock.clock_users_list',
                            'url_params' => '',
                            'parents' => ['xiaoe_clock', 'clock'],
                        ],

                    ]
                ]

            ]
        ]);
    }

    public function boot()
    {

    }

}