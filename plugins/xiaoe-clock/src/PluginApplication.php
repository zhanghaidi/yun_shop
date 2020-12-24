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

                    ]
                ]

            ]
        ]);
    }

    public function boot()
    {

    }

}