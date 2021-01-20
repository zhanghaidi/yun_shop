<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/23
 * Time: 下午17:25
 */

use Illuminate\Contracts\Events\Dispatcher;

return function (Dispatcher $events) {
    \Config::set('plugins_menu.community', [
        'name' => '数据同步',
        'type' => 'api',
        'url' => 'plugin.community.admin.handle.index',// url 可以填写http 也可以直接写路由
        'urlParams' => '',//如果是url填写的是路由则启用参数否则不启用
        'permit' => 1,//如果不设置则不会做权限检测
        'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
        'top_show'    => 0,
        'left_first_show'   => 0,
        'left_second_show'   => 1,
        'icon' => 'fa-refresh',//菜单图标
        'list_icon' => 'community',
        'parents' => [],
        'child' => [
            'community.handle' => [
                'name' => '数据同步',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-refresh',
                'url' => 'plugin.community.admin.handle.index',
                'urlParams' => [],
                'parents'=>['community'],
                'child' => []
            ]
        ]
    ]);
};