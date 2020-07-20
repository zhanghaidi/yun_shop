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
                    'name' => '房间管理',
                    'url' => 'plugin.appletslive.admin.controllers.room.index',
                    'url_params' => '',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => 'fa-cog',
                    'item' => '',
                    'parents' => ['appletslive'],
                    'child' => [
                        'appletslive_room_index' => [
                            'name' => '房间列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.index',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_set' => [
                            'name' => '房间设置',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.set',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_add' => [
                            'name' => '添加房间(录播)',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.add',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_del' => [
                            'name' => '删除房间(录播)',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.del',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_replaylist' => [
                            'name' => '回看列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.replaylist',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_replayadd' => [
                            'name' => '添加回看',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.replayadd',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_replayedit' => [
                            'name' => '编辑回看',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.replayedit',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_replaydel' => [
                            'name' => '删除回看',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.replaydel',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                    ]
                ],
            ]
        ]);
    }
}