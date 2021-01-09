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
        //Config::set('plugins.sign.set_tabs', \Yunshop\Sign\Common\Config\SetTabsHook::getSetTabs());

    }

   /* public function getTemplateItems()
    {
        return ['sign_success_notice' => [
            'title' => trans('Yunshop\EnterpriseWechat::sign.plugin_name') . '通知',
            'subtitle' => '企业微信签到通知',
            'value' => 'sign_notice',
            'param' => [
                '昵称', '签到时间', '连签天数', '签到奖励',
            ]
        ]];
    }*/

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
                'plugin.activity-qrcode.activity_see' => [
                    'name'      => '活码列表',
                    'permit'    => 1,
                    'menu'      => 1,
                    'icon'      => '',
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
                            'item' => 'activity_destroy',
                            'url' => 'plugin.activity-qrcode.admin.activity.deleted',
                            'url_params' => '',
                            'parents' => ['activity-qrcode','plugin.activity-qrcode.activity_see'],
                        ],
                    ]
                ],

            ],

            'plugin.activity-qrcode.qrcode' => [
                'name' => '二维码管理',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.activity-qrcode.admin.qrcode',
                'url_params' => '',
                'parents' => ['activity-qrcode'],
            ],

        ]);
    }

    public function boot()
    {

    }

}