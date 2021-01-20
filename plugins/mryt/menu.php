<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/4/24
 * Time: 9:54
 */

$store = Yunshop\Mryt\admin\model\MrytMemberModel::select()->whereUserUid(YunShop::app()->uid)->first();
if ($store) {
    \Config::set('menu.order', []);
    \Config::set('menu.printer', []);
    \Config::set('menu.mryt_store_menu', [
        'name' => '门店',
        'url' => 'plugin.mryt.store.admin.store.index',// url 可以填写http 也可以直接写路由
        'urlParams' => '',//如果是url填写的是路由则启用参数否则不启用
        'permit' => 0,//如果不设置则不会做权限检测
        'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
        'icon' => 'fa-home',//菜单图标
        'parents' => [],
        'top_show' => 0,
        'left_first_show' => 1,
        'left_second_show' => 1,
        'child' => [
            'mryt_store' => [
                'name' => '门店列表',
                'url' => 'plugin.mryt.store.admin.store.index',
                'url_params' => '',
                'permit' => 0,
                'menu' => 1,
                'icon' => 'fa-list-ul',
                'item' => 'mryt_store',
                'parents' => ['mryt_store_menu'],
                'child' => [
                    'admin.mryt.excel' => ['name' => '导出EXCEL', 'url' => 'plugin.mryt.admin.store.export', 'permit' => 0, 'menu' => '', 'parents' => ['mryt', 'admin.mryt.excel']],
                    'admin.mryt.sale' => ['name' => '选择通知人', 'url' => 'member.member.get-search-member', 'permit' => 0, 'menu' => '', 'parents' => ['mryt', 'admin.mryt.sale']],
//                    'admin.mryt.boss' => ['name' => '选择老板', 'url' => 'plugin.mryt.admin.member.bossQuery.index', 'permit' => 0, 'menu' => '', 'parents' => ['mryt', 'admin.mryt.boss']],
                    'admin.mryt.edit' => ['name' => '修改', 'url' => 'plugin.mryt.store.admin.store.edit', 'permit' => 0, 'menu' => '', 'parents' => ['mryt_store_menu', 'mryt_store']],
                ]
            ],
            'mryt_store_apply' => [
                'name' => '门店申请',
                'permit' => 0,
                'menu' => 1,
                'icon' => 'fa-list-ul',
                'url' => 'plugin.mryt.store.admin.apply.index',
                'url_params' => '',
                'parents' => ['mryt_store_menu'],
                'child' => [
                    'admin.store_apply.examine' => ['name' => '审核', 'url' => 'plugin.mryt.store.admin.apply.examine', 'permit' => 0, 'menu' => '', 'parents' => ['mryt_store_menu', 'mryt_store_apply']],
                    'admin.store_apply.detail' => ['name' => '查看详情', 'url' => 'plugin.mryt.store.admin.apply.detail', 'permit' => 0, 'menu' => '', 'parents' => ['mryt_store_menu', 'mryt_store_apply']],
                ]
            ],
            'mryt_store_goods' => [
                'name' => '门店商品',
                'permit' => 0,
                'menu' => 1,
                'icon' => 'fa-cubes',
                'url' => 'plugin.mryt.store.admin.goods.index',
                'url_params' => '',
                'parents' => ['mryt_store_menu'],
                'child' => [
                    'admin.store_goods.setProperty' => ['name' => '上下架热卖', 'url' => 'goods.goods.batchSetProperty', 'permit' => 0, 'menu' => '', 'parents' => ['mryt_store_menu', 'mryt_store_goods']],
                ]
            ],
            'mryt_cashier' => [
                'name' => '门店-收银台统计',
                'permit' => 0,
                'menu' => 1,
                'icon' => 'fa-list-ul',
                'url' => 'plugin.mryt.store.admin.cashier.index',
                'url_params' => '',
                'parents' => ['mryt_store_menu'],
                'child' => [
                    'admin.mryt.excel' => [
                        'name' => '导出excel',
                        'url' => 'plugin.mryt.admin.cashier.export',
                        'permit' => 0,
                        'menu' => '',
                        'parents' => ['mryt_store_menu', 'mryt_cashier']
                    ],
                    'mryt_cashier_order' => [
                        'name' => '收银台订单',
                        'url' => 'plugin.mryt.store.admin.order.index',
                        'url_params' => '',
                        'permit' => 0,
                        'menu' => 0,
                        'icon' => '',
                        'sort' => 1,
                        'parents' => ['mryt_store_menu', 'mryt_cashier'],
                    ],
                    'mryt_store_order' => [
                        'name' => '门店订单',
                        'url' => 'plugin.mryt.store.admin.store-order.index',
                        'url_params' => '',
                        'permit' => 0,
                        'menu' => 0,
                        'icon' => '',
                        'sort' => 1,
                        'parents' => ['mryt_store_menu', 'mryt_cashier'],
                    ],
                    'mryt_store_client' => [
                        'name' => '客户',
                        'url' => 'plugin.mryt.store.admin.cashier.client',
                        'url_params' => '',
                        'permit' => 0,
                        'menu' => 0,
                        'icon' => '',
                        'sort' => 1,
                        'parents' => ['mryt_store_menu', 'mryt_cashier'],
                    ],

                ]
            ],
        ]
    ]);
}