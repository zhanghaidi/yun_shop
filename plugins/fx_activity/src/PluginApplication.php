<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\FxActivity;
use app\backend\modules\menu\Menu;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {

    }

    protected function setMenuConfig()
    {
        Menu::current()->setPluginMenu('fx_activity', [
            'name'              => '活动报名',
            'type'              => 'api',
            'url'               => 'plugin.fx_activity.admin.set.edit',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'top_show'          => 0,
            'left_first_show'   => 0,
            'left_second_show'  => 1,
            'icon'              => 'fa-jpy',//菜单图标
            'list_icon'         => 'fx_activity',
            'parents'           => [],
            'child'             => [

                'plugin.fx_activity.admin' => [
                    'name'          => '基础设置',
                    'permit'        => 1,
                    'menu'          => 1,
                    'icon'          => '',
                    'url'           => 'plugin.fx_activity.admin.set.edit',
                    'url_params'    => '',
                    'parents'       => ['fx_activity'],
                    'child'         => [

                        'plugin.fx_activity.admin.marketing_set' => [
                            'name'      => '营销设置',
                            'url'       => 'plugin.fx_activity.admin.set.edit',
                            'permit'    => 1,
                            'menu'      => 0,
                            'parents'   => ['fx_activity', 'set'],
                        ],
                        'plugin.fx_activity.admin.fee_splitting_set' => [
                            'name'      => '分润设置',
                            'url'       => 'plugin.fx_activity.admin.set.edit',
                            'permit'    => 1,
                            'menu'      => 0,
                            'parents'   => ['fx_activity', 'set'],
                        ],
                        'plugin.fx_activity.admin.cash_back_set' => [
                            'name'      => '返现设置',
                            'url'       => 'plugin.fx_activity.admin.set.edit',
                            'permit'    => 1,
                            'menu'      => 0,
                            'parents'   => ['fx_activity', 'set'],
                        ]
                    ]
                ],

                'plugin.fx_activity.admin.order' => [
                    'name'       => '活动报名订单',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'url'        => 'plugin.fx_activity.admin.order.index',
                    'url_params' => '',
                    'parents'    => ['fx_activity'],
                ],

                'plugin.fx_activity.admin.ip-whitelist' => [
                    'name'       => 'IP白名单',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'url'        => 'plugin.fx_activity.admin.IpWhiteList.index',
                    'url_params' => '',
                    'parents'    => ['fx_activity'],
                ]
            ]
        ]);

    }

    public function boot()
    {
        $events = app('events');

        //完成订单监听事件
        $events->subscribe(\Yunshop\FxActivity\listeners\OrderReceiveListener::class);
    }

}