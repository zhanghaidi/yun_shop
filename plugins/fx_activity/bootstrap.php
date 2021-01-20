<?php

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

    \Config::set('plugins_menu.fx_activity', [
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

    //完成订单监听事件
    $events->subscribe(\Yunshop\FxActivity\listeners\OrderReceiveListener::class);

};
