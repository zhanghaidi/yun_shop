<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\Exhelper;


class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
    }
    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('exhelper',[
            'name'          => '快递助手',
            'type'          => 'tool',
            'url'           => 'plugin.exhelper.admin.print-once.search',          // url 可以填写http 也可以直接写路由
            'url_params'    => '',          //如果是url填写的是路由则启用参数否则不启用
            'permit'        => 1,           //如果不设置则不会做权限检测
            'menu'          => 1,           //如果不设置则不显示菜单，子菜单也将不显示
            'top_show'      => 0,
            'left_first_show'   => 0,
            'left_second_show'   => 1,
            // 'icon'          => 'fa-gavel',
            'icon'         => 'fa-pencil-square-o',   //菜单图标
            'list_icon'     => 'exhelper',
            'parents'       =>[],
            'child'         => [
                //应用到商城的一些白名单
                'exhelper_print' => [
                    'name'              => '快递及面单打印',
                    'permit'            => 1,
                    'menu'              => 1,
                    'icon'              => 'fa-magic',
                    'url'               => 'plugin.exhelper.admin.print-once.search',
                    'url_params'        => '',
                    'item'              => 'exhelper_print',
                    'parents'           =>['exhelper'],
                    'child'                 => [
                        'designer_list_preview' => [
                            'name'              => '预览页面',
                            'url'               => 'plugin.designer.admin.list.preview',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'designer_list_preview',
                            'parents'           => ['exhelper', 'designer_list'],
                        ],

                        'designer_order_search' => [
                            'name'              => '订单查询',
                            'url'               => 'plugin.exhelper.admin.print-once.search',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'designer_order_search',
                            'parents'           => ['exhelper', 'designer_list'],
                        ],

                        'exhelper_print_detail' => [
                            'name'              => '订单详情',
                            'url'               => 'plugin.exhelper.admin.print-once.detail',
                            'url_params'        => '',
                            'permit'            => 0,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_print_detail',
                            'parents'           => ['exhelper', 'designer_list'],
                        ],

                        'exhelper_print_get_print_temp' => [
                            'name'              => '打印发货单快递单',
                            'url'               => 'plugin.exhelper.admin.print-once.getPrintTemp',
                            'url_params'        => '',
                            'permit'            => 0,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_print_get_print_temp',
                            'parents'           => ['exhelper', 'designer_list'],
                        ],

                        'exhelper_print_get_print_panel' => [
                            'name'              => '打印电子面单',
                            'url'               => 'plugin.exhelper.admin.panel.test',
                            'url_params'        => '',
                            'permit'            => 0,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_print_get_print_panel',
                            'parents'           => ['exhelper', 'designer_list'],
                        ],

                        'exhelper_print_save_address' => [
                            'name'              => '',
                            'url'               => 'plugin.exhelper.admin.print-once.saveAddress',
                            'url_params'        => '',
                            'permit'            => 0,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_print_save_address',
                            'parents'           => ['exhelper', 'designer_list'],
                        ],

                        'exhelper_print_short_title' => [
                            'name'              => '',
                            'url'               => 'plugin.exhelper.admin.print-once.shortTitle',
                            'url_params'        => '',
                            'permit'            => 0,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_print_short_title',
                            'parents'           => ['exhelper', 'designer_list'],
                        ],

                        'exhelper_print_status_edit' => [
                            'name'              => '',
                            'url'               => 'plugin.exhelper.admin.print-once.editOrderPrintStatus',
                            'url_params'        => '',
                            'permit'            => 0,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_print_status_edit',
                            'parents'           => ['exhelper', 'designer_list'],
                        ],
                    ],
                ],
                'exhelper_express'  => [
                    'name'              => '快递单模版管理',
                    'permit'            => 1,
                    'menu'              => 1,
                    'icon'              => '',
                    'url'               => 'plugin.exhelper.admin.express.index',
                    'url_params'        => '',
                    'item'              => 'exhelper_express',
                    'parents'           =>['exhelper'],
                    'child'                 => [

                        'exhelper_express_add' => [
                            'name'              => '添加快递单',
                            'url'               => 'plugin.exhelper.admin.express.add',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_express_add',
                            'parents'           => ['exhelper', 'exhelper_express'],
                        ],

                        'exhelper_express_edit' => [
                            'name'              => '修改快递单',
                            'url'               => 'plugin.exhelper.admin.express.edit',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_express_edit',
                            'parents'           => ['exhelper', 'exhelper_express'],
                        ],

                        'exhelper_express_delete' => [
                            'name'              => '删除快递单',
                            'url'               => 'plugin.exhelper.admin.express.delete',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_express_delete',
                            'parents'           => ['exhelper', 'exhelper_express'],
                        ],

                        'exhelper_express_isDefault' => [
                            'name'              => '设置默认',
                            'url'               => 'plugin.exhelper.admin.express.isDefault',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_express_isDefault',
                            'parents'           => ['exhelper', 'exhelper_express'],
                        ],
                    ],
                ],

                'send_manager' => [
                    'name'                  => '发货单模版管理',
                    'permit'                => 1,
                    'menu'                  => 1,
                    'icon'                  => 'fa-pencil-square-o',
                    'url'                   => 'plugin.exhelper.admin.send.index',
                    'url_params'            => '',
                    'item'                  => 'send_manager',
                    'parents'               =>['exhelper'],
                    'child'                 => [
                        'exhelper_send_add' => [
                            'name'              => '添加发货单',
                            'url'               => 'plugin.exhelper.admin.send.add',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_send_add',
                            'parents'           => ['exhelper', 'sendusers_manager'],
                        ],

                        'exhelper_send_edit' => [
                            'name'              => '修改发货单',
                            'url'               => 'plugin.exhelper.admin.send.edit',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_send_delete',
                            'parents'           => ['exhelper', 'sendusers_manager'],
                        ],

                        'exhelper_send_delete' => [
                            'name'              => '删除发货单',
                            'url'               => 'plugin.exhelper.admin.send.delete',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_send_delete',
                            'parents'           => ['exhelper', 'sendusers_manager'],
                        ],

                        'exhelper_send_isDefault' => [
                            'name'              => '设置默认',
                            'url'               => 'plugin.exhelper.admin.send.isDefault',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_send_isDefault',
                            'parents'           => ['exhelper', 'sendusers_manager'],
                        ],
                    ],
                ],

                'sendusers_manager' => [
                    'name'                  => '发件人信息管理',
                    'permit'                => 1,
                    'menu'                  => 1,
                    'icon'                  => 'fa-pencil-square-o',
                    'url'                   => 'plugin.exhelper.admin.send-user.index',
                    'url_params'            => '',
                    'item'                  => 'sendusers_manager',
                    'parents'               =>['exhelper'],
                    'child'                 => [
                        'sendusers_manager_add' => [
                            'name'              => '添加模版',
                            'url'               => 'plugin.exhelper.admin.send-user.add',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'sendusers_manager_add',
                            'parents'           => ['exhelper', 'send_manager'],
                        ],
                        'sendusers_manager_edit' => [
                            'name'              => '修改模版',
                            'url'               => 'plugin.exhelper.admin.send-user.edit',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'sendusers_manager_edit',
                            'parents'           => ['exhelper', 'send_manager'],
                        ],

                        'sendusers_manager_delete' => [
                            'name'              => '删除模版',
                            'url'               => 'plugin.exhelper.admin.send-user.delete',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'sendusers_manager_delete',
                            'parents'           => ['exhelper', 'send_manager'],
                        ],

                        'sendusers_manager_isDefault' => [
                            'name'              => '设置默认',
                            'url'               => 'plugin.exhelper.admin.send-user.isDefault',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'sendusers_manager_isDefault',
                            'parents'           => ['exhelper', 'send_manager'],
                        ],
                    ],
                ],

                'exhelper_panel' => [
                    'name'                  => '电子面单管理',
                    'permit'                => 1,
                    'menu'                  => 1,
                    'icon'                  => 'fa-pencil-square-o',
                    'url'                   => 'plugin.exhelper.admin.panel.index',
                    'url_params'            => '',
                    'item'                  => 'panel',
                    'parents'               =>['exhelper'],
                    'child'                 => [
                        'exhelper_panel_edit' => [
                            'name'              => '修改',
                            'url'               => 'plugin.exhelper.admin.panel.edit',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_panel_edit',
                            'parents'           => ['exhelper', 'exhelper_panel'],
                        ],
                        'exhelper_panel_add' => [
                            'name'              => '添加',
                            'url'               => 'plugin.exhelper.admin.panel.add',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_panel_add',
                            'parents'           => ['exhelper', 'exhelper_panel'],
                        ],
                        'exhelper_panel_delete' => [
                            'name'              => '删除',
                            'url'               => 'plugin.exhelper.admin.panel.destory',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_panel_delete',
                            'parents'           => ['exhelper', 'exhelper_panel'],
                        ],
                        'exhelper_panel_default' => [
                            'name'              => '设置默认',
                            'url'               => 'plugin.exhelper.admin.panel.isDefault',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_panel_default',
                            'parents'           => ['exhelper', 'exhelper_panel'],
                        ],
                    ],
                ],

                'exhelper_short' => [
                    'name'                  => '商品简称',
                    'permit'                => 1,
                    'menu'                  => 1,
                    'icon'                  => 'fa-pencil-square-o',
                    'url'                   => 'plugin.exhelper.admin.short.index',
                    'url_params'            => '',
                    'item'                  => 'short',
                    'parents'               =>['exhelper'],
                    'child'                 => [

                        'exhelper_short_add' => [
                            'name'              => '批量修改',
                            'url'               => 'plugin.exhelper.admin.short.edit',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'exhelper_short_add',
                            'parents'           => ['exhelper', 'exhelper_short'],
                        ],
                    ],
                ],

                'exhelper_short' => [
                    'name'                  => '查询邮政编码',
                    'permit'                => 1,
                    'menu'                  => 1,
                    'icon'                  => 'fa-pencil-square-o',
                    'url'                   => 'plugin.exhelper.admin.print-once.zip-code-query',
                    'url_params'            => '',
                    'item'                  => 'short',
                    'parents'               =>['exhelper'],
                    'child'                 => []
                ],

                // 'exhelper_panel_print' => [
                //     'name'                  => '电子面单打印',
                //     'permit'                => 1,
                //     'menu'                  => 1,
                //     'icon'                  => 'fa-pencil-square-o',
                //     'url'                   => 'plugin.exhelper.admin.panelPrint.search',
                //     'url_params'            => '',
                //     'item'                  => 'panelPrint',
                //     'parents'               =>['exhelper'],
                //     'child'                 => [
                //         'exhelper_panel_print_add' => [
                //             'name'              => '批量修改',
                //             'url'               => 'plugin.exhelper.admin.panelPrint.edit',
                //             'url_params'        => '',
                //             'permit'            => 1,
                //             'menu'              => 0,
                //             'icon'              => '',
                //             'sort'              => 1,
                //             'item'              => 'exhelper_panel_print_add',
                //             'parents'           => ['exhelper', 'exhelper_panel_print'],
                //         ],
                //     ],
                // ],
                'set_print' => [
                    'name'                  => '打印设置',
                    'permit'                => 1,
                    'menu'                  => 1,
                    'icon'                  => 'fa-pencil-square-o',
                    'url'                   => 'plugin.exhelper.admin.set.index',
                    'url_params'            => '',
                    'item'                  => 'set_print',
                    'parents'               =>['exhelper'],
                    'child'                 => [
                        'designer_menu_store' => [
                            'name'              => '添加修改菜单',
                            'url'               => 'plugin.designer.admin.menu.store',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'designer_menu_store',
                            'parents'           => ['designer', 'designer_menu'],
                        ],
                    ],
                ],
            ]
        ]);

    }

    public function boot()
    {

    }

}