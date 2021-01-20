<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\GroupPurchase;
use app\backend\modules\menu\Menu;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {

    }

    protected function setMenuConfig()
    {
        Menu::current()->setPluginMenu('plugins_menu.group_purchase', [
            'name'              => '拼团',
            'type'              => 'api',
            'url'               => 'plugin.group-purchase.admin.purchase-set.edit',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'top_show'          => 0,
            'left_first_show'   => 0,
            'left_second_show'  => 1,
            'icon'              => '',//菜单图标
            'list_icon'         => 'group_purchase',//列表图标
            'parents'           => [],
            'child'             => [

                'plugin.group-purchase.admin.group_purchase_set' => [
                    'name'          => '拼团设置',
                    'permit'        => 1,
                    'menu'          => 1,
                    'icon'          => '',
                    'url'           => 'plugin.group-purchase.admin.purchase-set.edit',
                    'url_params'    => '',
                    'parents'       => ['group_purchase'],
                    'child'         => [

                        'plugin.group-purchase.admin.marketing_set' => [
                            'name'      => '营销设置',
                            'url'       => 'plugin.group-purchase.admin.purchase-set.edit',
                            'permit'    => 1,
                            'menu'      => 0,
                            'parents'   => ['group_purchase', 'group_purchase_set'],
                        ],
                        'plugin.group-purchase.admin.fee_splitting_set' => [
                            'name'      => '分润设置',
                            'url'       => 'plugin.group-purchase.admin.purchase-set.edit',
                            'permit'    => 1,
                            'menu'      => 0,
                            'parents'   => ['group_purchase', 'group_purchase_set'],
                        ],
                        'plugin.group-purchase.admin.cash_back_set' => [
                            'name'      => '返现设置',
                            'url'       => 'plugin.group-purchase.admin.purchase-set.edit',
                            'permit'    => 1,
                            'menu'      => 0,
                            'parents'   => ['group_purchase', 'group_purchase_set'],
                        ]
                    ]
                ],

                'plugin.group-purchase.admin.group-purchase-orders' => [
                    'name'       => '拼团订单',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'url'        => 'plugin.group-purchase.admin.purchase-orders.index',
                    'url_params' => '',
                    'parents'    => ['group_purchase'],
                ],

                'plugin.group-purchase.admin.ip-whitelist' => [
                    'name'       => 'IP白名单',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'url'        => 'plugin.group-purchase.admin.IpWhiteList.index',
                    'url_params' => '',
                    'parents'    => ['group_purchase'],
                ]
            ]
        ]);

    }

    public function boot()
    {
        $events = app('events');

        //完成订单监听事件
        $events->subscribe(\Yunshop\GroupPurchase\listeners\OrderReceiveListener::class);

    }

}