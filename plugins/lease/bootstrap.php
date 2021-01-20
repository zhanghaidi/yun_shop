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

    \Config::set('plugins_menu.lease_toy', [
        'name' => '租赁',
        'type' => 'industry',
        'url'  => 'plugin.lease-toy.admin.lease-toy-set.index', //url 可以填写http 也可以直接写路由
        'url_params' => '',//如果是url填写的是路由则启用参数否则不启用
        'permit' => 1,//如果不设置则不会做权限检测
        'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
        'top_show'    => 0,
        'left_first_show'   => 0,
        'left_second_show'   => 1,
        'icon' => 'fa-hourglass-2',//菜单图标
        'list_icon' => 'lease_toy',
        'parents' => [],
        'child' => [
            'lease_toy_set' => [
                'name' => '基础设置',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.lease-toy.admin.lease-toy-set.index',
                'url_params' => '',
                'parents' => ['lease_toy'],
                'child' => []
            ],
            'lease_toy_deposit_record' => [
                'name' => '押金管理',
                'url' => 'plugin.lease-toy.admin.deposit-record.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'ico' => '',
                'parents' => ['lease_toy'],
                'child' => [
                    'deposit_record_log' => [
                        'name' => '押金记录',
                        'url' => 'plugin.lease-toy.admin.deposit-record.detail',
                        'permit' => 1,
                        'parents' => ['lease_toy', 'lease_toy_deposit_record'],
                    ],
                    'deposit_record_export' => [
                        'name' => '记录导出',
                        'url' => 'plugin.lease-toy.admin.deposit-record.export',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['lease_toy', 'lease_toy_deposit_record'],
                    ]
                ]
            ],
            'lease_toy_return_address' => [
                'name' => '归还地址',
                'url' => 'plugin.lease-toy.admin.return-address.index',
                'url_params' => '',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'parents' => ['lease_toy'],
                'child' => [
                    'return_address_add' => [
                        'name' => '添加归还地址',
                        'url' => 'plugin.lease-toy.admin.return-address.add',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['lease_toy', 'lease_toy_return_address'],
                    ],
                    'return_address_edit' => [
                        'name' => '编辑归还地址',
                        'url' => 'plugin.lease-toy.admin.return-address.edit',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['lease_toy', 'lease_toy_return_address'],
                    ],
                    'return_address_del' => [
                        'name' => '删除归还地址',
                        'url' => 'plugin.lease-toy.admin.return-address.deleted',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['lease_toy', 'lease_toy_return_address'],
                    ]
                ]

            ],
            'lease_toy_term' => [
                'name' => '租期设置',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.lease-toy.admin.lease-term.index',
                'url_params' => '',
                'parents' => ['lease_toy'],
                'child' => [
                    'lease_term_add' => [
                        'name' => '添加租期',
                        'url' => 'plugin.lease-toy.admin.lease-term.add',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['lease_toy', 'lease_toy_term'],
                    ],
                    'lease_term_edit' => [
                        'name' => '编辑租期',
                        'url' => 'plugin.lease-toy.admin.lease-term.edit',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['lease_toy', 'lease_toy_term'],
                    ],
                    'lease_term_del' => [
                        'name' => '删除租期',
                        'url' => 'plugin.lease-toy.admin.lease-term.deleted',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['lease_toy', 'lease_toy_term'],
                    ]
                ]
            ],
            'lease_toy_level_rights' => [
                'name' => '等级权益',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.lease-toy.admin.level-rights.index',
                'url_params' => '',
                'parents' => ['lease_toy'],
                'child' => []
            ],
            'lease_toy_goods' => [
                'name' => '租赁商品',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.lease-toy.admin.goods.index',
                'url_params' => '',
                'parents' => ['lease_toy'],
                'child' => [
                    'lease_goods_add' => [
                        'name' => '发布商品',
                        'url' => 'plugin.lease-toy.admin.goods.create',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['lease_toy', 'lease_toy_goods'],
                    ],
                    'lease_goods_edit' => [
                        'name' => '编辑商品',
                        'url' => 'plugin.lease-toy.admin.goods.edit',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['lease_toy', 'lease_toy_goods'],
                    ],
                    'lease_goods_del' => [
                        'name' => '删除商品',
                        'url' => 'plugin.lease-toy.admin.goods.destroy',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['lease_toy', 'lease_toy_goods'],
                    ]
                ]
            ],
            'lease_toy_order' => [
                'name' => '订单管理',
                'permit' => '',
                'menu' => 1,
                'icon' => 'fa-clipboard',
                'url' => 'plugin.lease-toy.admin.order.index',
                'url_params' => '',
                'parents' => ['lease_toy'],
                'child' => [
                    'lease_toy_order_detail' => [
                        'name' => '订单详情',
                        'permit' => '',
                        'menu' => '',
                        'icon' => '',
                        'url' => 'plugin.lease-toy.admin.order.detail',
                        'url_params' => '',
                        'parents' => ['lease_toy', 'lease_toy_order'],
                        'child' => []

                    ],
                    'lease_toy_order_changePrice' => [
                        'name' => '订单改价',
                        'permit'=> '',
                        'menu' => 0,
                        'icon' => '',
                        'url' => 'order.change-order-price',
                        'url_params' => '',
                        'parents' => ['lease_toy','lease_toy_order'],
                        'child' => []
                    ],
                    'lease_toy_order_remark' => [
                        'name' => '修改备注',
                        'permit' => '',
                        'menu' => '',
                        'icon' => '',
                        'url' => 'order.remark.update-remark',
                        'url_params' => '',
                        'parents' => ['lease_toy', 'lease_toy_order'],
                        'child' => []
                    ],
                    'lease_toy_order_receive' => [
                        'name' => '确认收货',
                        'permit' => '',
                        'menu' => '',
                        'icon' => '',
                        'url' => 'order.operation.receive',
                        'url_params' => '',
                        'parents' => ['lease_toy', 'lease_toy_order'],
                        'child' => []
                    ],
                    'lease_toy_order_cancel-send' => [
                        'name' => '取消发货',
                        'permit' => '',
                        'menu' => '',
                        'icon' => '',
                        'url' => 'order.operation.cancel-send',
                        'url_params' => '',
                        'parents' => ['lease_toy', 'lease_toy_order'],
                        'child' => []
                    ],
                    'lease_toy_order_send' => [
                        'name' => '确认发货',
                        'permit' => '',
                        'menu' => '',
                        'icon' => '',
                        'url' => 'order.operation.send',
                        'url_params' => '',
                        'parents' => ['lease_toy', 'lease_toy_order'],
                        'child' => []
                    ],
                    'lease_toy_order_pay' => [
                        'name' => '确认付款',
                        'permit' => '',
                        'menu' => '',
                        'icon' => '',
                        'url' => 'order.operation.pay',
                        'url_params' => '',
                        'parents' => ['lease_toy', 'lease_toy_order'],
                        'child' => []
                    ]
                ]
            ],
            // 'goods_attribute' => [
            //     'name' => '商品搜索属性',
            //     'permit' => '',
            //     'menu' => 1,
            //     'icon' => 'fa-clipboard',
            //     'url' => 'plugin.lease-toy.admin.goods-attribute.index',
            //     'url_params' => '',
            //     'parents' => ['lease_toy'],
            //     'child' => [
            //         'goods_attribute_add' => [
            //             'name' => '添加搜索属性',
            //             'permit' => '',
            //             'menu' => '',
            //             'icon' => '',
            //             'url' => 'plugin.lease-toy.admin.goods-attribute.add',
            //             'url_params' => '',
            //             'parents' => ['lease_toy', 'goods_attribute'],
            //             'child' => []

            //         ],
            //         'goods_attribute_edit' => [
            //             'name' => '编辑搜索属性',
            //             'permit' => '',
            //             'menu' => '',
            //             'icon' => '',
            //             'url' => 'plugin.lease-toy.admin.goods-attribute.edit',
            //             'url_params' => '',
            //             'parents' => ['lease_toy', 'goods_attribute'],
            //             'child' => []

            //         ],
            //         'goods_attribute_del' => [
            //             'name' => '删除搜索属性',
            //             'permit' => '',
            //             'menu' => '',
            //             'icon' => '',
            //             'url' => 'plugin.lease-toy.admin.goods-attribute.del',
            //             'url_params' => '',
            //             'parents' => ['lease_toy', 'goods_attribute'],
            //             'child' => []

            //         ]
            //     ]
            // ]
        ]
    ]);

    /**
     * 商品挂件保存
     */
    \Config::set('observer.goods.lease_toy', [
        'class' => 'Yunshop\LeaseToy\widgets\LeaseToyWidget',
        'function_validator' => 'relationValidator',
        'function_save' => 'relationSave'
    ]);

    /**
     * 商品挂件
     */
    \Config::set('widget.goods.tab_lease_toy', [
        'title' => '租赁商品',
        'class' => 'Yunshop\LeaseToy\widgets\LeaseToyWidget'
    ]);
    \Config::set('template.lease_toy_return_goods', [
        'title' => '租赁(归还成功通知)',
        'subtitle' => '租赁商品退还通知',
        'value' => 'lease_toy_return_goods',
        'param' => [
            '昵称', '订单号', '商品详情','归还押金', '时间',
        ]
    ]);


    //创建订单
    $events->subscribe(Yunshop\LeaseToy\Listener\OrderCreatedListener::class);

    //订单支付完成
    $events->subscribe(Yunshop\LeaseToy\Listener\OrderPaidListener::class);

    //订单关闭
    $events->subscribe(Yunshop\LeaseToy\Listener\OrderCanceledListener::class);
};
