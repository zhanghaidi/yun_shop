<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\LuckyDraw;
use app\backend\modules\menu\Menu;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {

    }

    protected function setMenuConfig()
    {
        Menu::current()->setPluginMenu('lucky-draw', [
            'name'              => '幸运大抽奖',
            'type'              => 'marketing',
            'url'               => 'plugin.lucky-draw.admin.controllers.activity.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'top_show'          => 0,
            'left_first_show'   => 0,
            'left_second_show'  => 1,
            'icon'              => 'fa-american-sign-language-interpreting',
            'list_icon'         => 'lucky-draw',
            'item'              => 'lucky-draw',
            'parents'           => [],
            'child'             => [

                'lucky_draw_manage' => [
                    'name'              => '幸运大抽奖',
                    'url'               => 'plugin.lucky-draw.admin.controllers.activity.index',
                    'url_params'        => '',
                    'permit'            => 1,
                    'menu'              => 1,
                    'icon'              => '',
                    'item'              => '',
                    'parents'           => ['lucky_draw'],
                    'child'             => [
                        'lucky_draw_list' => [
                            'name'              => '活动列表',
                            'url'               => 'plugin.lucky-draw.admin.controllers.activity.index',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => '',
                            'parents'           => ['lucky_draw','lucky_draw_manage'],
                        ],

                        'lucky_draw_add' => [
                            'name'              => '创建活动',
                            'url'               => 'plugin.lucky-draw.admin.controllers.activity.add',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => '',
                            'parents'           => ['lucky_draw','lucky_draw_manage'],
                            'child'             => [
                                'lucky_draw_add_prize' => [
                                    'name'              => '添加奖品',
                                    'url'               => 'plugin.lucky-draw.admin.controllers.activity.add-prize',
                                    'url_params'        => '',
                                    'permit'            => 1,
                                    'menu'              => 1,
                                    'icon'              => '',
                                    'item'              => '',
                                    'parents'           => ['lucky_draw','lucky_draw_manage','lucky_draw_add'],
                                ],
                            ],
                        ],

                        'lucky_draw_edit' => [
                            'name'              => '编辑活动',
                            'url'               => 'plugin.lucky-draw.admin.controllers.activity.edit',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => '',
                            'parents'           => ['lucky_draw','lucky_draw_manage'],
                            'child'             => [
                                'lucky_draw_add_prize' => [
                                    'name'              => '添加奖品',
                                    'url'               => 'plugin.lucky-draw.admin.controllers.activity.add-prize',
                                    'url_params'        => '',
                                    'permit'            => 1,
                                    'menu'              => 1,
                                    'icon'              => '',
                                    'item'              => '',
                                    'parents'           => ['lucky_draw','lucky_draw_manage','lucky_draw_edit'],
                                ],
                            ],
                        ],

                        'lucky_draw_record' => [
                            'name'              => '活动数据',
                            'url'               => 'plugin.lucky-draw.admin.controllers.activity.record',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'item'              => '',
                            'parents'           => ['lucky_draw','lucky_draw_manage'],
                            'child'             => [
                                'lucky_draw_add_prize' => [
                                    'name'              => '活动数据',
                                    'url'               => 'plugin.lucky-draw.admin.controllers.activity.record',
                                    'url_params'        => '',
                                    'permit'            => 1,
                                    'menu'              => 1,
                                    'icon'              => '',
                                    'item'              => '',
                                    'parents'           => ['lucky_draw','lucky_draw_manage','lucky_draw_record'],
                                ],
                            ],
                        ],
                    ]
                ],

//            'lucky_draw_record' => [
//                'name'              => '数据',
//                'url'               => 'plugin.lucky-draw.admin.controllers.activity.record',
//                'url_params'        => '',
//                'permit'            => 1,
//                'menu'              => 1,
//                'icon'              => '',
//                'item'              => '',
//                'parents'           => ['lucky_draw'],
//                'child'             => [
//                    'lucky_draw_record_export' => [
//                        'name'              => '导出数据',
//                        'url'               => 'plugin.lucky-draw.admin.controllers.activity.export',
//                        'url_params'        => '',
//                        'permit'            => 1,
//                        'menu'              => 0,
//                        'icon'              => '',
//                        'item'              => '',
//                        'parents'           => ['lucky_draw','lucky_draw_record'],
//                    ],
//                ]
//            ],
            ]
        ]);

    }

    public function boot()
    {

    }

}