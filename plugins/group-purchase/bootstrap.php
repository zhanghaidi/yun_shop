<?php

use app\common\services\Hook;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * 你可以在这个闭包的参数列表中使用类型提示
 * Laravel 会自动从容器中解析出对应的依赖并自动注入
 * 使用依赖注入之前你首先需要了解 Laravel 的服务容器机制
 *
 * 在这个闭包里你可以做任何准备工作，所有的代码都会在请求被处理之前执行
 * 包括不限于动态修改 config、修改 option、监听事件、绑定对象至服务容器等
 *
 * @see  https://laravel-china.org/docs/5.1/container
 */
return function (Dispatcher $events) {

    \Config::set('plugins_menu.group_purchase', [
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

    //完成订单监听事件
    $events->subscribe(\Yunshop\GroupPurchase\listeners\OrderReceiveListener::class);

};
