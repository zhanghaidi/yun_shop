<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/4/2
 * Time: 4:16 PM
 */

namespace Yunshop\MinApp\Common\Config;


class MenuHook
{
    public static function menu()
    {
        $menu = [
            'name'             => '小程序',
            'type'             => 'tool',
            'url'              => 'plugin.min-app.Backend.Controllers.base-set',
            'url_params'       => '',
            'permit'           => 1,
            'menu'             => 1,
            'top_show'         => 0,
            'left_first_show'  => 1,
            'left_second_show' => 1,
            'icon'             => 'fa-weixin',
            'list_icon'        => 'min_app',
            'parents'          => [],
            'child'            => [
//                'plugin.min-app.admin.audit'     => [
//                    'name'       => '一键发布',
//                    'permit'     => 1,
//                    'menu'       => 1,
//                    'icon'       => '',
//                    'url'        => 'plugin.min-app.Backend.Controllers.audit.index',
//                    'url_params' => '',
//                    'parents'    => ['min-app'],
//                    'child'      => []
//                ],
                'pluginWeChatAppletManualPush'   => [
                    'name'       => '手动发布',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'url'        => 'plugin.min-app.Backend.Modules.Manual.Controllers.index.index',
                    'url_params' => '',
                    'parents'    => ['min-app'],
                    'item'       => 'pluginWeChatAppletManualPush',
                    'child'      => [
                        'pluginWeChatAppletManualPushLogin'  => [
                            'name'       => '手动发布',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'url'        => 'plugin.min-app.Backend.Modules.Manual.Controllers.login.index',
                            'url_params' => '',
                            'parents'    => ['min-app', 'pluginWeChatAppletManualPush'],
                            'item'       => 'pluginWeChatAppletManualPushLogin',
                        ],
                        'pluginWeChatAppletManualPushOutput' => [
                            'name'       => '手动发布',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'url'        => 'plugin.min-app.Backend.Modules.Manual.Controllers.output.index',
                            'url_params' => '',
                            'parents'    => ['min-app', 'pluginWeChatAppletManualPush'],
                            'item'       => 'pluginWeChatAppletManualPushOutput',
                        ]
                    ]
                ],
                'plugin.min-app.admin.set'       => [
                    'name'       => '基础设置',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'url'        => 'plugin.min-app.Backend.Controllers.base-set.index',
                    'url_params' => '',
                    'parents'    => ['min-app'],
                    'child'      => []
                ],
                'plugin.min-app.admin.assistant' => [
                    'name'       => '小程序数据助手',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'url'        => 'plugin.min-app.Backend.Controllers.assistant.index',
                    'url_params' => '',
                    'parents'    => ['min-app'],
                    'child'      => []
                ],
                'plugin.min-app.admin.popup' => [
                    'name'       => '小程序弹窗设置',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'url'        => 'plugin.min-app.Backend.Controllers.popup.index',
                    'url_params' => '',
                    'parents'    => ['min-app'],
                    'child'      => [
                        'plugin.min-app.admin.popup.edit'  => [
                            'name'       => '编辑弹窗',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'url'        => 'plugin.min-app.Backend.Controllers.popup.position',
                            'url_params' => '',
                            'parents'    => ['min-app', 'plugin.min-app.admin.popup'],
                            'item'       => 'plugin.min-app.admin.popup.edit',
                        ],
                        'plugin.min-app.admin.popup.position-index'  => [
                            'name'       => '弹窗位置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'url'        => 'plugin.min-app.Backend.Controllers.popup.position',
                            'url_params' => '',
                            'parents'    => ['min-app', 'plugin.min-app.admin.popup'],
                            'item'       => 'plugin.min-app.admin.popup.position-index',
                        ],
                        'plugin.min-app.admin.popup.position-edit'  => [
                            'name'       => '编辑弹窗位置',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'url'        => 'plugin.min-app.Backend.Controllers.popup.position-edit',
                            'url_params' => '',
                            'parents'    => ['min-app', 'plugin.min-app.admin.popup'],
                            'item'       => 'plugin.min-app.admin.popup.position-edit',
                        ],
                    ]
                ],
                'plugin.min-app.admin.search' => [
                    'name'       => '小程序搜索',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'url'        => 'plugin.min-app.Backend.Controllers.search.site-search',
                    'url_params' => '',
                    'parents'    => ['min-app'],
                    'child'      => [
                        'plugin.min-app.admin.search.submit-pages'  => [
                            'name'       => '提交页面',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'url'        => 'plugin.min-app.Backend.Controllers.search.submit-pages',
                            'url_params' => '',
                            'parents'    => ['min-app', 'plugin.min-app.admin.search'],
                            'item'       => 'plugin.min-app.admin.search',
                        ],
                        'plugin.min-app.admin.search.one-key'  => [
                            'name'       => '自动提交',
                            'permit'     => 1,
                            'menu'       => 0,
                            'icon'       => '',
                            'url'        => 'plugin.min-app.Backend.Controllers.search.one-key',
                            'url_params' => '',
                            'parents'    => ['min-app', 'plugin.min-app.admin.search'],
                            'item'       => 'plugin.min-app.admin.search',
                        ],
                    ]
                ],
            ]
        ];
        return $menu;
    }
}
