<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\ActivityQrcode;


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
        Menu::current()->setPluginMenu('activity-qrcode', [
            'name'              => '活码管理',
            'type'              => 'tool',
            'url'               => 'plugin.activity-qrcode.admin.activity.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'top_show'          => 0,
            'left_first_show'   => 0,
            'left_second_show'  => 1,
            'icon'              => '',
            'list_icon'         => 'poster',
            'parents'           => [],
            'child'             => [
                'activity-qrcode.set' => [
                    'name' => '基础设置',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.activity-qrcode.admin.set.index',
                    'url_params' => '',
                    'parents' => ['activity-qrcode'],
                    'child' => [
                    ]
                ],
                'plugin.activity-qrcode.activity_see' => [
                    'name'      => '活码列表',
                    'permit'    => 1,
                    'menu'      => 1,
                    'icon'      => 'fa-clipboard',
                    'url'       => 'plugin.activity-qrcode.admin.activity.index',
                    'url_params'=> '',
                    'item' => 'plugin.activity-qrcode.activity_see',
                    'parents'   => ['activity-qrcode'],
                    'child'     => [
                        'plugin.activity-qrcode.activity_add' => [
                            'name' => '添加活码',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin.activity-qrcode.activity_add',
                            'url' => 'plugin.activity-qrcode.admin.activity.add',
                            'url_params' => '',
                            'parents' => ['activity-qrcode','plugin.activity-qrcode.activity_see'],
                        ],
                        'plugin.activity-qrcode.activity_edit' => [
                            'name' => '编辑活码',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin.activity-qrcode.activity_edit',
                            'url' => 'plugin.activity-qrcode.admin.activity.edit',
                            'url_params' => '',
                            'parents' => ['activity-qrcode','plugin.activity-qrcode.activity_see'],
                        ],
                        'plugin.activity-qrcode.activity_destroy' => [
                            'name' => '活码删除',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin.activity-qrcode.activity_destroy',
                            'url' => 'plugin.activity-qrcode.admin.activity.deleted',
                            'url_params' => '',
                            'parents' => ['activity-qrcode','plugin.activity-qrcode.activity_see'],
                        ],
                        'plugin.activity-qrcode.qrcode_see' => [
                            'name' => '二维码列表',
                            'permit' => 1,
                            'menu' => 1,
                            'icon' => '',
                            'item' => 'plugin.activity-qrcode.qrcode_see',
                            'url' => 'plugin.activity-qrcode.admin.qrcode.index',
                            'url_params' => '',
                            'parents' => ['activity-qrcode'],
                            'child' =>[
                                'plugin.activity-qrcode.qrcode_add' => [
                                    'name' => '二维码添加',
                                    'permit' => 1,
                                    'menu' => 1,
                                    'icon' => '',
                                    'item' => 'plugin.activity-qrcode.qrcode_add',
                                    'url' => 'plugin.activity-qrcode.admin.qrcode.add',
                                    'url_params' => '',
                                    'parents' => ['activity-qrcode','plugin.activity-qrcode.activity_see','plugin.activity-qrcode.qrcode_see'],
                                ],
                                'plugin.activity-qrcode.qrcode_edit' => [
                                    'name' => '二维码编辑',
                                    'permit' => 1,
                                    'menu' => 1,
                                    'icon' => '',
                                    'item' => 'plugin.activity-qrcode.qrcode_edit',
                                    'url' => 'plugin.activity-qrcode.admin.qrcode.edit',
                                    'url_params' => '',
                                    'parents' => ['activity-qrcode','plugin.activity-qrcode.activity_see','plugin.activity-qrcode.qrcode_see'],
                                ],
                                'plugin.activity-qrcode.qrcode_destroy' => [
                                    'name' => '二维码删除',
                                    'permit' => 1,
                                    'menu' => 1,
                                    'icon' => '',
                                    'item' => 'plugin.activity-qrcode.qrcode_destroy',
                                    'url' => 'plugin.activity-qrcode.admin.qrcode.deleted',
                                    'url_params' => '',
                                    'parents' => ['activity-qrcode','plugin.activity-qrcode.activity_see','plugin.activity-qrcode.qrcode_see'],
                                ],
                            ]
                        ],

                    ]
                ],

            ],


        ]);
    }

    public function boot()
    {

    }

}