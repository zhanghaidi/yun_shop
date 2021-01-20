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

    \Config::set('plugins_menu.help_user_buying', [
        'name' => '代客下单',
        'type' => 'industry',
        'url'  => 'plugin.help-user-buying.admin.home.select', //url 可以填写http 也可以直接写路由
        'url_params' => '',//如果是url填写的是路由则启用参数否则不启用
        'permit' => 1,//如果不设置则不会做权限检测
        'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
        'top_show'    => 0,
        'left_first_show'   => 0,
        'left_second_show'   => 0,
        'icon' => '',//菜单图标
        'list_icon' => 'help_user_buying',
        'parents' => [],
        'child' => [
            'help-user-buying-select' => [
                'name'      => '选择基地',
                'permit'    => 1,
                'menu'      => 1,
                'icon'      => '',
                'url'       => 'plugin.help-user-buying.admin.home.select',
                'url_params'=> '',
                'parents'   => ['help_user_buying'],
                'child'     => []
            ],
            'help-user-buying-home' => [
                'name'      => '代客下单',
                'permit'    => 1,
                'menu'      => 1,
                'icon'      => '',
                'url'       => 'plugin.help-user-buying.admin.home.index',
                'url_params'=> '',
                'parents'   => ['help_user_buying'],
                'child'     => []
            ],
            'help-user-buying-search-member' => [
                'name'      => '选择下单人',
                'permit'    => 1,
                'menu'      => 0,
                'icon'      => '',
                'url'       => 'member.member.get-search-member',
                'url_params'=> '',
                'parents'   => ['help_user_buying'],
                'child'     => []
            ],
            'help-user-buying-view-change' => [
                'name'      => '商城下单',
                'permit'    => 1,
                'menu'      => 0,
                'icon'      => '',
                'url'       => 'plugin.help-user-buying.admin.home.shopIndex',
                'url_params'=> '',
                'parents'   => ['help_user_buying'],
                'child'     => [

                    'help-user-buying-goods-buy' => [
                        'name'      => '预下单',
                        'permit'    => 1,
                        'menu'      => 0,
                        'icon'      => '',
                        'url'       => 'plugin.help-user-buying.shop.controller.goods-buy.index',
                        'url_params'=> '',
                        'parents'   => ['help_user_buying', 'help-user-buying-view-change'],
                        'child'     => []
                    ],
                    'help-user-buying-order-create' => [
                        'name'      => '下单',
                        'permit'    => 1,
                        'menu'      => 0,
                        'icon'      => '',
                        'url'       => 'plugin.help-user-buying.shop.controller.create.index',
                        'url_params'=> '',
                        'parents'   => ['help_user_buying', 'help-user-buying-view-change'],
                        'child'     => []
                    ],
                ]
            ],
           
            'help-user-buying-storeview-change' => [
                'name'      => '门店下单',
                'permit'    => 1,
                'menu'      => 0,
                'icon'      => '',
                'url'       => 'plugin.help-user-buying.admin.home.storeIndex',
                'url_params'=> '',
                'parents'   => ['help_user_buying'],
                'child'     => [
                    
                    'help-user-buying-storegoods-buy' => [
                        'name'      => '预下单',
                        'permit'    => 1,
                        'menu'      => 0,
                        'icon'      => '',
                        'url'       => 'plugin.help-user-buying.store.controller.goods-buy.index',
                        'url_params'=> '',
                        'parents'   => ['help_user_buying', 'help-user-buying-storeview-change'],
                        'child'     => []
                    ],
                    'help-user-buying-storeorder-create' => [
                        'name'      => '下单',
                        'permit'    => 1,
                        'menu'      => 0,
                        'icon'      => '',
                        'url'       => 'plugin.help-user-buying.store.controller.create.index',
                        'url_params'=> '',
                        'parents'   => ['help_user_buying', 'help-user-buying-storeview-change'],
                        'child'     => []
                    ],
                ]
            ],
            
           
            'help-user-buying-user-merge-pay' => [
                'name'      => '支付',
                'permit'    => 1,
                'menu'      => 0,
                'icon'      => '',
                'url'       => 'plugin.help-user-buying.admin.user-merge-pay.index',
                'url_params'=> '',
                'parents'   => ['help_user_buying'],
                'child'     => []
            ],
            'help-user-buying-goods-increase' => [
                'name'      => '添加商品',
                'permit'    => 1,
                'menu'      => 0,
                'icon'      => '',
                'url'       => 'plugin.help-user-buying.admin.home.goods-increase',
                'url_params'=> '',
                'parents'   => ['help_user_buying'],
                'child'     => []
            ],
            'help-user-buying-pay-credit' => [
                'name'      => '订单余额支付',
                'permit'    => 1,
                'menu'      => 0,
                'icon'      => '',
                'url'       => 'plugin.help-user-buying.admin.user-merge-pay.credit2',
                'url_params'=> '',
                'parents'   => ['help_user_buying'],
                'child'     => []
            ],
            'help-user-buying-pay-cod' => [
                'name'      => '订单货到付款',
                'permit'    => 1,
                'menu'      => 0,
                'icon'      => '',
                'url'       => 'plugin.help-user-buying.admin.user-merge-pay.COD',
                'url_params'=> '',
                'parents'   => ['help_user_buying'],
                'child'     => []
            ],
           
        ]
    ]);

};
