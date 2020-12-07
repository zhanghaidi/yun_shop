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
        define('APPLETSLIVE_ROOM_LIVESTATUS_101', 101);
        define('APPLETSLIVE_ROOM_LIVESTATUS_101_TEXT', '直播中');
        define('APPLETSLIVE_ROOM_LIVESTATUS_102', 102);
        define('APPLETSLIVE_ROOM_LIVESTATUS_102_TEXT', '待开播');
        define('APPLETSLIVE_ROOM_LIVESTATUS_103', 103);
        define('APPLETSLIVE_ROOM_LIVESTATUS_103_TEXT', '已结束');
        define('APPLETSLIVE_ROOM_LIVESTATUS_104', 104);
        define('APPLETSLIVE_ROOM_LIVESTATUS_104_TEXT', '禁播');
        define('APPLETSLIVE_ROOM_LIVESTATUS_105', 105);
        define('APPLETSLIVE_ROOM_LIVESTATUS_105_TEXT', '暂停');
        define('APPLETSLIVE_ROOM_LIVESTATUS_106', 106);
        define('APPLETSLIVE_ROOM_LIVESTATUS_106_TEXT', '异常');
        define('APPLETSLIVE_ROOM_LIVESTATUS_107', 107);
        define('APPLETSLIVE_ROOM_LIVESTATUS_107_TEXT', '已过期');
        define('APPLETSLIVE_ROOM_LIVESTATUS_108', 108);
        define('APPLETSLIVE_ROOM_LIVESTATUS_108_TEXT', '已删除');
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
                        'appletslive_room_commentlist' => [
                            'name' => '评论列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.commentlist',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_commentreplylist' => [
                            'name' => '评论回复列表',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.commentreplylist',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_commentdel' => [
                            'name' => '评论回复删除',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.commentdel',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-room'],
                        ],
                        'appletslive_room_commentverify' => [
                            'name' => '敏感评论审核',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.room.commentverify',
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
                        'appletslive_live_import' => [
                            'name' => '直播间导入商品',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.live.import',
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
                        'appletslive_goods_add' => [
                            'name' => '添加商品并提审',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.goods.add',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-goods'],
                        ],
                        'appletslive_goods_resetaudit' => [
                            'name' => '撤回审核',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.goods.resetaudit',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-goods'],
                        ],
                        'appletslive_goods_audit' => [
                            'name' => '重新提审',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.goods.audit',
                            'url_params' => '',
                            'parents' => ['appletslive', 'appletslive-goods'],
                        ],
                        'appletslive_goods_edit' => [
                            'name' => '更新商品',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.appletslive.admin.controllers.goods.edit',
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