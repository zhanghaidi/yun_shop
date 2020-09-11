<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-12-18
 * Time: 16:03
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Appletslive;


class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
        define('APPLETSLIVE_ROOM_TYPE_COURSE', 1);
        define('APPLETSLIVE_ROOM_TYPE_BRANDSALE', 2);
    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('appletslive', [
            'name' => '小程序直播',
            'type' => 'tool',
            'url' => 'plugin.appletslive.admin.controllers.set.index',
            'url_params' => '',
            'permit' => 1,
            'menu' => 1,
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => 'fa-cog',
            'list_icon' => 'appletslive',
            'item' => '',
            'parents' => [],
            'child' => [
                'appletslive-set' => [
                    'name' => '基础配置',
                    'url' => 'plugin.appletslive.admin.controllers.set.index',
                    'url_params' => '',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-cog',
                    'item' => '',
                    'parents' => ['appletslive'],
                    'child' => []
                ],
                'appletslive-room' => [
                    'name' => '课程管理',
                    'url' => 'plugin.appletslive.admin.controllers.room.index',
                    'url_params' => '',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-cog',
                    'item' => '',
                    'parents' => ['appletslive'],
                    'child' => [
                        'appletslive_room_index' => [
                            'name' => '课程列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.index',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_edit' => [
                            'name' => '课程设置',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.edit',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_add' => [
                            'name' => '添加课程',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.add',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_showhide' => [
                            'name' => '课程显示与隐藏',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.showhide',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_replaylist' => [
                            'name' => '视频列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.replaylist',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_replayedit' => [
                            'name' => '编辑视频',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.replayedit',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_replayadd' => [
                            'name' => '添加视频',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.replayadd',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_replayshowhide' => [
                            'name' => '视频显示与隐藏',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.replayshowhide',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                    ]
                ],
                'appletslive-live' => [
                    'name' => '直播管理',
                    'url' => 'plugin.appletslive.admin.controllers.live.index',
                    'url_params' => '',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-cog',
                    'item' => '',
                    'parents' => ['appletslive'],
                    'child' => [
                        'appletslive_live_index' => [
                            'name' => '直播间列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.live.index',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-live'],
                        ],
                        'appletslive_live_edit' => [
                            'name' => '编辑直播间',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.live.edit',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-live'],
                        ],
                        'appletslive_live_add' => [
                            'name' => '添加直播间',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.live.add',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-live'],
                        ],
                    ],
                ],
                'appletslive-goods' => [
                    'name' => '商品管理',
                    'url' => 'plugin.appletslive.admin.controllers.goods.index',
                    'url_params' => '',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-cog',
                    'item' => '',
                    'parents' => ['appletslive'],
                    'child' => [
                        'appletslive_goods_index' => [
                            'name' => '商品列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.goods.index',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-goods'],
                        ],
                        'appletslive_goods_edit' => [
                            'name' => '编辑商品',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.goods.edit',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-goods'],
                        ],
                        'appletslive_goods_add' => [
                            'name' => '添加商品',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.goods.add',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-goods'],
                        ],
                        'appletslive_goods_del' => [
                            'name' => '删除商品',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.goods.del',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-goods'],
                        ],
                    ],
                ],
            ]
        ]);
    }
}