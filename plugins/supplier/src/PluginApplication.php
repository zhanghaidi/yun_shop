<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\Supplier;

use app\backend\modules\menu\Menu;
use app\common\facades\Setting;
use app\common\models\MemberCart;
use app\common\modules\widget\Widget;
use Config;
use YunShop;
use Yunshop\Supplier\common\modules\order\OrderManager;

class PluginApplication extends \app\common\services\PluginApplication
{
    public function getTemplateItems()
    {
        return ['supplier_withdraw_apply' => [
            'title'    => '供应商(提现申请通知)',
            'subtitle' => '提现申请通知',
            'value'    => 'supplier_withdraw_apply',
            'param'    => [
                '提现单号', '提现金额', '昵称', '手机号', '申请时间'
            ]
        ], 'supplier_withdraw_examine'    => [
            'title'    => '供应商(提现申请审核通知)',
            'subtitle' => '提现申请审核通知',
            'value'    => 'supplier_withdraw_examine',
            'param'    => [
                '提现单号', '提现金额', '昵称', '手机号', '审核时间'
            ]
        ], 'supplier_withdraw_reject'     => [
            'title'    => '供应商(提现申请驳回通知)',
            'subtitle' => '提现申请驳回通知',
            'value'    => 'supplier_withdraw_reject',
            'param'    => [
                '提现单号', '提现金额', '昵称', '手机号', '驳回时间'
            ]
        ], 'supplier_withdraw_play'       => [
            'title'    => '供应商(提现申请打款通知)',
            'subtitle' => '提现申请打款通知',
            'value'    => 'supplier_withdraw_play',
            'param'    => [
                '提现单号', '提现金额', '昵称', '手机号', '打款时间'
            ]
        ], 'supplier_apply_reject'        => [
            'title'    => '供应商(供应商申请驳回通知)',
            'subtitle' => '供应商申请驳回通知',
            'value'    => 'supplier_apply_reject',
            'param'    => [
                '昵称', '时间'
            ]
        ], 'supplier_apply_pass'          => [
            'title'    => '供应商(供应商申请通过通知)',
            'subtitle' => '供应商申请通过通知',
            'value'    => 'supplier_apply_pass',
            'param'    => [
                '昵称', '时间'
            ]
        ], 'supplier_order_create'        => [
            'title'    => '供应商(供应商订单下单通知)',
            'subtitle' => '供应商订单下单通知',
            'value'    => 'supplier_order_create',
            'param'    => [
                '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）'
            ]
        ], 'supplier_order_pay'           => [
            'title'    => '供应商(供应商订单支付通知)',
            'subtitle' => '供应商订单支付通知',
            'value'    => 'supplier_order_pay',
            'param'    => [
                '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '支付方式', '支付时间'
            ]
        ], 'supplier_order_send'          => [
            'title'    => '供应商(供应商订单发货通知)',
            'subtitle' => '供应商订单发货通知',
            'value'    => 'supplier_order_send',
            'param'    => [
                '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '发货时间', '快递公司', '快递单号'
            ]
        ], 'supplier_order_finish'        => [
            'title'    => '供应商(供应商订单完成通知)',
            'subtitle' => '供应商订单完成通知',
            'value'    => 'supplier_order_finish',
            'param'    => [
                '商城名称', '粉丝昵称', '订单号', '下单时间', '订单金额', '运费', '商品详情（含规格）', '确认收货时间'
            ]
        ]];
    }

    protected function setConfig()
    {

        $supplier = YunShop::isRole();
        if ($supplier){
            \app\common\modules\shop\ShopConfig::current()->set('printer_owner', [
                'owner'    => \Yunshop\Supplier\common\models\Supplier::PLUGIN_ID,
                'owner_id' => $supplier['id']
            ]);
        }

        // 手动分红配置
        \app\common\modules\shop\ShopConfig::current()->set('manual_arr.supplier', [
            'uidColumn'   => 'member_id',
            'enableLevel' => false,
            'type_name'   => '供应商',
            'role_type'   => 'supplier'
        ]);
        \app\common\modules\shop\ShopConfig::current()->set('manual_arr_cfg.supplier', [
            'roleTableClass' => \Yunshop\Supplier\common\models\Supplier::class
        ]);

        \app\common\modules\shop\ShopConfig::current()->set('notice.supplier', [
            //'withdraw_ok_title', //审核通过通知
            //'withdraw_no_title', //审核驳回通知
            //'withdraw_pay_title', //提现打款通知
            //'apply_reject_title', //供应商申请驳回通知
        ]);

        \app\common\modules\shop\ShopConfig::current()->set('plugin_goods.supplier', [
            'class'    => \Yunshop\Supplier\common\services\withdraw\IsOwnerSupplier::class,
            'function' => 'verify'
        ]);

        \app\common\modules\shop\ShopConfig::current()->set('customer_service', [
            'class'    => 'Yunshop\Supplier\common\services\CustomerService',
            'function' => 'getCservice'
        ]);

        \app\common\modules\shop\ShopConfig::current()->set('goods_detail.supplier', [
            'class'    => \Yunshop\Supplier\common\models\SupplierGoods::class,
            'function' => 'getSupplierGoodsById'
        ]);

        //插入提现与订单关系关联表
        \app\common\modules\shop\ShopConfig::current()->set('observer.supplier.withdraw', [
            'class'         => 'Yunshop\Supplier\common\models\SupplierObserverMethods',
            'function_save' => 'addRelationOrder'
        ]);
        //操作提醒
        \app\common\modules\shop\ShopConfig::current()->set('observer.withdraw.operation', [
            'class'         => 'Yunshop\Supplier\common\models\SupplierObserverMethods',
            'function_save' => 'withdrawOpNotice'
        ]);
        //添加商品
        \app\common\modules\shop\ShopConfig::current()->set('observer.goods.supplier_add_goods', [
            'class'         => 'Yunshop\Supplier\common\models\SupplierObserverMethods',
            'function_save' => 'addSupplierGoods'
        ]);
        //删除商品
        \app\common\modules\shop\ShopConfig::current()->set('observer.goods.supplier_delete_goods', [
            'class'         => 'Yunshop\Supplier\common\models\SupplierObserverMethods',
            'function_save' => 'deleteSupplierGoods'
        ]);
        //添加运费模板
        \app\common\modules\shop\ShopConfig::current()->set('observer.dispatch.supplier_add_dispatch', [
            'class'         => 'Yunshop\Supplier\common\models\SupplierObserverMethods',
            'function_save' => 'addSupplierDispatch'
        ]);
        //删除运费模板
        \app\common\modules\shop\ShopConfig::current()->set('observer.dispatch.supplier_delete_dispatch', [
            'class'         => 'Yunshop\Supplier\common\models\SupplierObserverMethods',
            'function_save' => 'deleteSupplierDispatch'
        ]);
        //下单通知
        \app\common\modules\shop\ShopConfig::current()->set('observer.supplier.create_order', [
            'class'         => 'Yunshop\Supplier\common\models\SupplierObserverMethods',
            'function_save' => 'createOrderNotice'
        ]);
        //订单操作通知
        \app\common\modules\shop\ShopConfig::current()->set('observer.order.order_operation', [
            'class'         => 'Yunshop\Supplier\common\models\SupplierObserverMethods',
            'function_save' => 'opOrderNotice'
        ]);
        //修改供应商微信角色
        \app\common\modules\shop\ShopConfig::current()->set('observer.supplier.edit_supplier', [
            'class'         => 'Yunshop\Supplier\common\models\SupplierObserverMethods',
            'function_save' => 'editSupplier'
        ]);
        /*\app\common\modules\shop\ShopConfig::current()->set('observer.goods.sale',[
            'class'=>'app\backend\modules\goods\models\Sale',
            'function_validator'=>'relationValidator',
            'function_save'=>'relationSave'
        ]);*/


        /*
        $events->listen(\app\common\events\order\AfterOrderCreatedEvent::class, function($event) {
            //订单model
            $model = $event->getOrderModel();
            //做自己的操作
            \Log::error('AfterOrderReceivedEvent:123');
        });
            */

        \app\common\modules\shop\ShopConfig::current()->push('shop-foundation.member-cart.with', 'goods.supplierGoods');
        Config::push('shop-foundation.order.with', 'goods.supplierGoods');
        //供应商订单操作按钮
        \app\common\modules\shop\ShopConfig::current()->set('shop-foundation.order.supplier_order_operations', [
            'waitPay'     => [
                \Yunshop\Supplier\frontend\operations\supplier\Close::class,
            ],
            'waitSend'    => [
                \Yunshop\Supplier\frontend\operations\supplier\Send::class,
            ],
            'waitReceive' => [
                \Yunshop\Supplier\frontend\operations\supplier\CancelSend::class,
                \app\frontend\modules\order\operations\member\ExpressInfo::class,
            ],
            'complete'    => [
                \app\frontend\modules\order\operations\member\ExpressInfo::class,
            ],
//        'close' => [
//            \app\frontend\modules\order\operations\member\ExpressInfo::class,
//        ]
        ]);

    }

    public function boot()
    {
        $events = app('events');
        $this->singleton('OrderManager', OrderManager::class);
        $this->bind(MemberCart::class, function (PluginApplication $pluginApp, array $params) {
            return new \Yunshop\Supplier\common\models\MemberCart($params[0]);
        });

        /**
         * 购物车分组
         */
        $events->subscribe(\Yunshop\Supplier\Listener\GroupCartListener::class);
        $events->subscribe(\Yunshop\Supplier\Listener\ShowPreGenerateOrderListener::class);

        /**
         * 购物车id分组
         */
        $events->subscribe(\Yunshop\Supplier\Listener\GroupCartIdListener::class);

        /**
         * 创建订单
         */
        $events->subscribe(\Yunshop\Supplier\Listener\CreatedOrderListener::class);

        $events->subscribe(\Yunshop\Supplier\Listener\PayedOrderListener::class);

        $events->subscribe(\Yunshop\Supplier\Listener\WithdrawResultListener::class);

        //\Yunshop\Supplier\common\services\CheckUsername::update();

        /**
         * 保单支付监听
         */
        $events->subscribe(\Yunshop\Supplier\Listener\InsOrderPaidListener::class);

        /**
         * 自动提现
         */
        $events->subscribe(\Yunshop\Supplier\Listener\AutomaticWithdrawListener::class);


    }

    public function getWidgetItems()
    {
        if (YunShop::isRole()) {
            Widget::current()->clearItems();
            $result = ['goods.tab_supplier_dispatch' => [
                'title' => '供应商配送',
                'class' => 'Yunshop\Supplier\supplier\controllers\goods\GoodsWidget'
            ], 'goods.tab_supplier_notice'           => [
                'title' => '消息通知',
                'class' => 'Yunshop\Supplier\supplier\controllers\goods\NoticeWidget'
            ], 'goods.tab_supplier_limitbuy'         => [
                'title' => '限时购',
                'class' => 'app\backend\widgets\goods\LimitBuyWidget'
            ]];
            if (app('plugins')->isEnabled('commission')) {
                $result[] = [
                    'goods.tab_supplier_commission' => [
                        'title' => '分销',
                        'class' => 'Yunshop\Commission\widgets\CommissionWidget'
                    ]
                ];
            }
            if (app('plugins')->isEnabled('team-dividend')) {
                $result[] = [
                    'goods.tab_supplier_team_dividend' => [
                        'title' => '经销商提成',
                        'class' => 'Yunshop\TeamDividend\widgets\DividendWidget'
                    ]
                ];
            }
            if (app('plugins')->isEnabled('area-dividend')) {
                $result[] = [
                    'goods.tab_supplier_area_dividend' => [
                        'title' => '区域分红',
                        'class' => 'Yunshop\AreaDividend\widgets\DividendWidget'
                    ]
                ];
            }
            return $result;
        }
        return [];
    }

    protected function setMenuConfig()
    {
        if (!YunShop::isRole()) {
            $supplier = [
                'name'             => '供应商管理',
                'type'             => 'industry',
                'url'              => 'plugin.supplier.admin.controllers.supplier.supplier-list.index',
                'url_params'       => '',
                'permit'           => 1,
                'menu'             => 1,
                'icon'             => 'fa-skype',
                'list_icon'        => 'supplier',
                'parents'          => [],
                'top_show'         => 0,
                'left_first_show'  => 0,
                'left_second_show' => 1,
                'child'            => [
                    'supplier_admin_supplier' => [
                        'name'       => '供应商管理',
                        'url'        => 'plugin.supplier.admin.controllers.supplier.supplier-list.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'supplier_admin_supplier',
                        'parents'    => ['supplier'],
                        'child'      => [

                            'admin_supplier_add' => [
                                'name'       => '添加供应商',
                                'url'        => 'plugin.supplier.admin.controllers.supplier.supplier-detail.add',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'admin_supplier_add',
                                'parents'    => ['supplier', 'supplier_admin_supplier'],
                            ],

                            'admin_supplier_detail' => [
                                'name'       => '供应商信息',
                                'url'        => 'plugin.supplier.admin.controllers.supplier.supplier-detail.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'admin_supplier_detail',
                                'parents'    => ['supplier', 'supplier_admin_supplier'],
                            ],

                            'admin_supplier_edit' => [
                                'name'       => '修改密码',
                                'url'        => 'plugin.supplier.admin.controllers.supplier.supplier-edit-pwd.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'admin_supplier_edit',
                                'parents'    => ['supplier', 'supplier_admin_supplier'],
                            ],

                            'admin_supplier_member' => [
                                'name'       => '选择微信角色',
                                'url'        => 'member.query.index',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'item'       => 'admin_supplier_member',
                                'parents'    => ['supplier', 'supplier_admin_supplier'],
                            ]
                        ]
                    ],

                    'supplier_apply' => [
                        'name'       => '供应商申请',
                        'url'        => 'plugin.supplier.admin.controllers.apply.supplier-apply.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'supplier_apply',
                        'parents'    => ['supplier'],
                        'child'      => [

                            'supplier_apply_index' => [
                                'name'       => '浏览列表',
                                'url'        => 'plugin.supplier.admin.controllers.apply.supplier-apply.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'supplier_apply_index',
                                'parents'    => ['supplier', 'supplier_apply']
                            ],

                            'supplier_apply_detail' => [
                                'name'       => '查看申请信息',
                                'url'        => 'plugin.supplier.admin.controllers.apply.supplier-apply.detail',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'supplier_apply_detail',
                                'parents'    => ['supplier', 'supplier_apply']
                            ],

                            'supplier_apply_div_detail' => [
                                'name'       => '查看表单信息',
                                'url'        => 'plugin.supplier.admin.controllers.apply.information.get-info-by-form-id',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'supplier_apply_div_detail',
                                'parents'    => ['supplier', 'supplier_apply']
                            ],

                            'supplier_apply_operation' => [
                                'name'       => '申请审核',
                                'url'        => 'plugin.supplier.admin.controllers.apply.apply-operation.apply-operation',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'supplier_apply_operation',
                                'parents'    => ['supplier', 'supplier_apply']
                            ]
                        ]
                    ],

                    'supplier_admin_order' => [
                        'name'       => '全部订单',
                        'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'supplier_admin_order',
                        'parents'    => ['supplier'],
                        'child'      => [

                            'admin_order_list' => [
                                'name'       => '全部订单',
                                'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_order_list',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],

                            'admin_order_waitPay' => [
                                'name'       => '待支付订单',
                                'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.wait-pay',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_order_waitPay',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],

                            'admin_order_waitSend' => [
                                'name'       => '待发货订单',
                                'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.wait-send',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_order_waitSend',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],

                            'admin_order_waitReceive' => [
                                'name'       => '待收货订单',
                                'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.wait-receive',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_order_waitReceive',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],

                            'admin_order_completed' => [
                                'name'       => '已完成订单',
                                'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.completed',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_order_completed',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],

                            'admin_order_cancelled' => [
                                'name'       => '已关闭订单',
                                'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.cancelled',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_order_cancelled',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],

                            'admin_order_refund' => [
                                'name'       => '退款订单',
                                'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.refund',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_order_refund',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],

                            'admin_order_detail' => [
                                'name'       => '订单详情',
                                'url'        => 'plugin.supplier.admin.controllers.order.supplier-order-detail.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_order_detail',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],

                            'admin_order_excel' => [
                                'name'       => '导出EXCEL',
                                'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_order_excel',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],

                            'admin_order_revoice' => [
                                'name'       => '上传发票',
                                'url'        => 'order.operation.remark',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'admin_order_revoice',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],

                            'admin_order_supplier_remark' => [
                                'name'       => '商户备注',
                                'url'        => 'order.operation.remark',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'admin_order_supplier_remark',
                                'parents'    => ['supplier', 'supplier_admin_order']
                            ],


                            /*'admin_order_remark' => ['name' => '保存订单备注', 'url' => 'order.remark.update-remark', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_remark']],
                            'admin_order_pay' => ['name' => '确认付款', 'url' => 'order.operation.pay', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_pay']],
                            'admin_order_send' => ['name' => '确认发货', 'url' => 'order.operation.send', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_send']],
                            'admin_order_cancelsend' => ['name' => '取消发货', 'url' => 'order.operation.cancel-send', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_cancelsend']],
                            'admin_order_receive' => ['name' => '确认收货', 'url' => 'order.operation.receive', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_receive']],
                            'admin_order_refundpay' => ['name' => '同意退款', 'url' => 'refund.pay.index', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_refundpay']],

                            'admin_order_reject' => ['name' => '驳回申请', 'url' => 'refund.operation.reject', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_reject']],
                            'admin_order_consensus' => ['name' => '手动退款', 'url' => 'refund.operation.consensus', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_consensus']],

                            'admin_order_pass' => ['name' => '退货退款;需要通过申请(需客户寄回商品) ', 'url' => 'refund.operation.pass', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_pass']],
                            'admin_order_resend' => ['name' => '确认退货 (无需客户寄回商品)', 'url' => 'refund.operation.resend', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_resend']],

                            'admin_order_changePrice' => ['name' => '订单改价', 'url' => 'order.change-order-price', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_changePrice']],
                            'admin_order_storeChangePrice' => ['name' => '提交订单改价', 'url' => 'order.change-order-price.store', 'permit' => 1, 'menu' => 0, 'parents' => ['supplier_admin_order', 'admin_order_storeChangePrice']],*/
                        ]
                    ],

                    'supplierAdminOrderOperate' => [
                        'name'       => '订单操作',
                        'url'        => '',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'item'       => 'supplierAdminOrderOperate',
                        'parents'    => ['supplier'],
                        'child'      => [
                            'supplierAdminOrderOperateSend' => [
                                'name'       => '确认发货',
                                'url'        => 'plugin.supplier.common.order.operation.send',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'supplierAdminOrderOperateSend',
                                'parents'    => ['supplier', 'supplierAdminOrderOperate'],
                            ],
                        ]
                    ],

                    'admin_order_waitSend' => [
                        'name'       => '待发货订单',
                        'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.wait-send',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'admin_order_waitSend',
                        'parents'    => ['supplier'],
                        'child'      => []
                    ],

                    'admin_order_waitPay' => [
                        'name'       => '待支付订单',
                        'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.wait-pay',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'admin_order_waitPay',
                        'parents'    => ['supplier'],
                        'child'      => []
                    ],

                    'admin_order_waitReceive' => [
                        'name'       => '待收货订单',
                        'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.wait-receive',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'admin_order_waitReceive',
                        'parents'    => ['supplier'],
                        'child'      => []
                    ],

                    'admin_order_completed' => [
                        'name'       => '已完成订单',
                        'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.completed',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'admin_order_completed',
                        'parents'    => ['supplier'],
                        'child'      => []
                    ],

                    'admin_order_cancelled' => [
                        'name'       => '已关闭订单',
                        'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.cancelled',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'admin_order_cancelled',
                        'parents'    => ['supplier'],
                        'child'      => []
                    ],

                    'admin_order_refund' => [
                        'name'       => '退换货订单',
                        'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.refund',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'admin_order_refund',
                        'parents'    => ['supplier'],
                        'child'      => []
                    ],

                    'admin_order_refunded' => [
                        'name'       => '已退款订单',
                        'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.refunded',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'admin_order_refunded',
                        'parents'    => ['supplier'],
                        'child'      => []
                    ],


                    'supplier_admin_goods' => [
                        'name'       => '供应商商品',
                        'url'        => 'plugin.supplier.admin.controllers.goods.supplier-goods-list.index',//plugin.supplier.admin.controllers.goods.supplier-goods-list.goods-list
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'supplier_admin_goods',
                        'parents'    => ['supplier'],
                        'child'      => [

                            'admin_goods_load_goods' => [
                                'name'       => '加载商品',
                                'url'        => 'plugin.supplier.admin.controllers.goods.supplier-goods-list.goods-list',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_goods_sort',
                                'parents'    => ['supplier', 'supplier_admin_goods'],
                            ],

                            'admin_goods_load_page_goods' => [
                                'name'       => '加载商品分页',
                                'url'        => 'plugin.supplier.admin.controllers.goods.supplier-goods-list.goods-search',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_goods_sort',
                                'parents'    => ['supplier', 'supplier_admin_goods'],
                            ],

                            'admin_goods_sort' => [
                                'name'       => '排序',
                                'url'        => 'plugin.supplier.supplier.controllers.goods.goods-operation.sort',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_goods_sort',
                                'parents'    => ['supplier', 'supplier_admin_goods'],
                            ],

                            //main-menu 中已经存在
                            /*'admin_goods_edit_title'    => [
                                'name'          => '快捷修改名称',
                                'url'           => 'goods.goods.change',
                                'url_params'    => '',
                                'permit'        => 1,
                                'menu'          => 0,
                                'icon'          => '',
                                'item'          => 'admin_goods_edit_title',
                                'parents'       => ['supplier','supplier_admin_goods'],
                            ],

                            'admin_goods_getSpecTpl'    => [
                                'name'          => '添加规格',
                                'url'           => 'goods.goods.getSpecTpl',
                                'url_params'    => '',
                                'permit'        => 1,
                                'menu'          => 0,
                                'icon'          => '',
                                'item'          => 'admin_goods_getSpecTpl',
                                'parents'       => ['supplier','supplier_admin_goods'],
                            ],

                            'admin_goods_getSpecItemTpl'    => [
                                'name'          => '添加规格项',
                                'url'           => 'goods.goods.getSpecItemTpl',
                                'url_params'    => '',
                                'permit'        => 1,
                                'menu'          => 0,
                                'icon'          => '',
                                'item'          => 'admin_goods_getSpecItemTpl',
                                'parents'       => ['supplier','supplier_admin_goods'],
                            ],

                            'admin_goods_select-city'    => [
                                'name'          => '不包邮区域',
                                'url'           => 'area.area.select-city',
                                'url_params'    => '',
                                'permit'        => 1,
                                'menu'          => 0,
                                'icon'          => '',
                                'item'          => 'admin_goods_select',
                                'parents'       => ['supplier','supplier_admin_goods'],
                            ],

                            'admin_goods_coupons'    => [
                                'name'          => '选择优惠券',
                                'url'           => 'coupon.coupon.get-search-coupons',
                                'url_params'    => '',
                                'permit'        => 1,
                                'menu'          => 0,
                                'icon'          => '',
                                'item'          => 'admin_goods_coupons',
                                'parents'       => ['supplier','supplier_admin_goods'],
                            ],*/

//                            'admin_goods_setProperty' => [
//                                'name'       => '上下架热卖等',
//                                'url'        => 'goods.goods.setProperty',
//                                'url_params' => '',
//                                'permit'     => 1,
//                                'menu'       => 0,
//                                'icon'       => '',
//                                'item'       => 'admin_goods_setProperty',
//                                'parents'    => ['supplier', 'supplier_admin_goods'],
//                            ],

                            'admin_goods_edit' => [
                                'name'       => '修改商品',
                                'url'        => 'plugin.supplier.admin.controllers.goods.goods-operation.edit',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_goods_edit',
                                'parents'    => ['supplier', 'supplier_admin_goods'],
                            ]
                        ]
                    ],

                    'supplier_admin_withdraw' => [
                        'name'       => '供应商提现',
                        'url'        => 'plugin.supplier.admin.controllers.withdraw.supplier-withdraw.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'supplier_admin_withdraw',
                        'parents'    => ['supplier'],
                        'child'      => [

                            'admin_withdraw_detail' => [
                                'name'       => '详情',
                                'url'        => 'plugin.supplier.admin.controllers.withdraw.supplier-withdraw.detail',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_withdraw_detail',
                                'parents'    => ['supplier', 'supplier_admin_withdraw'],
                            ],

                            'admin_withdraw_operation' => [
                                'name'       => '审核',
                                'url'        => 'plugin.supplier.admin.controllers.withdraw.withdraw-operation.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_withdraw_operation',
                                'parents'    => ['supplier', 'supplier_admin_withdraw'],
                            ],

                            'admin_withdraw_pay' => [
                                'name'       => '打款',
                                'url'        => 'plugin.supplier.admin.controllers.withdraw.withdraw-operation.pay',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_withdraw_pay',
                                'parents'    => ['supplier', 'supplier_admin_withdraw'],
                            ],

                            'admin_withdraw_export' => [
                                'name'       => '导出 EXCEL',
                                'url'        => 'plugin.supplier.admin.controllers.withdraw.supplier-withdraw.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'admin_withdraw_export',
                                'parents'    => ['supplier', 'supplier_admin_withdraw'],
                            ]
                        ]
                    ],
                    'supplier_admin_list' => [
                        'name'       => '提成明细',
                        'url'        => 'plugin.supplier.admin.controllers.income.list.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'supplier_admin_list',
                        'parents'    => ['supplier'],
                        'child'      => []
                    ],
                    'supplier_admin_set' => [
                        'name'       => '基础设置',
                        'url'        => 'plugin.supplier.admin.controllers.set.set.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => 'supplier_admin_set',
                        'parents'    => ['supplier'],
                        'child'      => []
                    ],
                ]
            ];
            $supplier_setting = Setting::get('plugin.supplier');
            if (!empty($supplier_setting['insurance_policy']) && $supplier_setting['insurance_policy']) {//开启状态

                $supplier['child']['insurance'] = [
                    'name'       => '供应商保单管理',
                    'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.index',
                    'url_params' => '',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'item'       => 'supplier_admin_set',
                    'parents'    => ['supplier'],
                    'child'      => [
                        'insurance_export' => [
                            'name'       => '导出 EXCEL',
                            'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.export',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'supplier_admin_set',
                            'parents'    => ['supplier'],
                        ],
                        'insurance_upload' => [
                            'name'       => '上传保单',
                            'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.upload',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'supplier_admin_set',
                            'parents'    => ['supplier'],
                        ],
                        'insurance_supplier_search' => [
                            'name'       => '供应商搜索',
                            'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.searchSupplierByName',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'supplier_admin_set',
                            'parents'    => ['supplier'],
                        ],
                        'insurance_upload_pdf' => [
                            'name'       => '上传pdf',
                            'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.uploadPdf',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'supplier_admin_set',
                            'parents'    => ['supplier'],
                        ],
                    ]
                ];

                $supplier['child']['insurance_company'] = [
                    'name'       => '保险公司',
                    'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.company-list',
                    'url_params' => '',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => '',
                    'item'       => 'supplier_admin_set',
                    'parents'    => ['supplier'],
                    'child'      => [
                        'insurance_company_add' => [
                            'name'       => '添加保险公司',
                            'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.company-add',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'supplier_admin_set',
                            'parents'    => ['supplier'],
                        ],
                        'insurance_company_sort' => [
                            'name'       => '保险公司排序',
                            'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.company-sort',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'supplier_admin_set',
                            'parents'    => ['supplier'],
                        ],
                        'insurance_company_edit' => [
                            'name'       => '编辑保险公司',
                            'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.company-edit',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'supplier_admin_set',
                            'parents'    => ['supplier'],
                        ],
                        'insurance_company_del' => [
                            'name'       => '删除保险公司',
                            'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.company-del',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'supplier_admin_set',
                            'parents'    => ['supplier'],
                        ],
                        'insurance_upload' => [
                            'name'       => '保单上传',
                            'url'        => 'plugin.supplier.admin.controllers.insurance.insurance.upload',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'item'       => 'supplier_admin_set',
                            'parents'    => ['supplier'],
                        ]
                    ]
                ];


            }
            Menu::current()->setPluginMenu('supplier', $supplier);

        } else {

            $supplier = YunShop::isRole();

            //这个不应该写在这里，应该写在setConfig()里面
//            \app\common\modules\shop\ShopConfig::current()->set('printer_owner', [
//                'owner'    => \Yunshop\Supplier\common\models\Supplier::PLUGIN_ID,
//                'owner_id' => $supplier['id']
//            ]);

            Config::set('menu', []);
            $menu = [
                'name'             => '供应商',
                'url'              => 'plugin.supplier.supplier.controllers.order.supplier-order.index',// url 可以填写http 也可以直接写路由
                'url_params'       => '',//如果是url填写的是路由则启用参数否则不启用
                'permit'           => '',//如果不设置则不会做权限检测
                'menu'             => 1,//如果不设置则不显示菜单，子菜单也将不显示
                'icon'             => 'fa-skype',//菜单图标
                'parents'          => [],
                'top_show'         => 0,
                'left_first_show'  => 1,
                'left_second_show' => 1,
                'child'            => [
                    'supplier.supplier.order' => [
                        'name'       => '全部订单',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.order.supplier-order.index',
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => [
                            'supplier.order.member.detail'         => [
                                'name'       => '会员详情',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'member.member.detail',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                            'supplier.order.order.detail'          => [
                                'name'       => '订单详情',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.order.order-detail.index',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => []
                            ],
                            'supplier.order.order.remark'          => [
                                'name'       => '保存备注',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.order.remark.index',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                            'supplier.order.order.invoice'         => [
                                'name'       => '上传发票',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.order.remark.set',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                            'supplier.order.order.supplier_remark' => [
                                'name'       => '商户备注',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.order.remark.set',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                            'supplier.order.order.send'            => [
                                'name'       => '发货',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.common.order.operation.send',//plugin.supplier.supplier.controllers.order.operation.send
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                            'supplier.order.order.cancelsend'      => [
                                'name'       => '取消发货',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.order.operation.cancel-send',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                            'supplier.order.order.close'           => [
                                'name'       => '关闭订单',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.order.operation.close',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                            'supplier.order.order.export'          => [
                                'name'       => '订单导出',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.admin.controllers.order.supplier-order.export',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                            'supplier.order.order.info'            => [
                                'name'       => '个人信息',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.info.index',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                        ]
                    ],

                    'supplier.supplier.waitPay' => [
                        'name'       => '待支付订单',
                        'url'        => 'plugin.supplier.supplier.controllers.order.supplier-order.wait-pay',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => []
                    ],

                    'supplier.supplier.waitSend' => [
                        'name'       => '待发货订单',
                        'url'        => 'plugin.supplier.supplier.controllers.order.supplier-order.wait-send',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => []
                    ],

                    'supplier.supplier.waitReceive' => [
                        'name'       => '待收货订单',
                        'url'        => 'plugin.supplier.supplier.controllers.order.supplier-order.wait-receive',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => []
                    ],

                    'supplier.supplier.completed' => [
                        'name'       => '已完成订单',
                        'url'        => 'plugin.supplier.supplier.controllers.order.supplier-order.completed',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => []
                    ],

                    'supplier.supplier.cancelled' => [
                        'name'       => '已关闭订单',
                        'url'        => 'plugin.supplier.supplier.controllers.order.supplier-order.cancelled',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => []
                    ],

                    'supplier.supplier.refund' => [
                        'name'       => '退换货订单',
                        'url'        => 'plugin.supplier.supplier.controllers.order.supplier-order.refund',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => []
                    ],

                    'supplier.supplier.refunded' => [
                        'name'       => '已退款订单',
                        'url'        => 'plugin.supplier.supplier.controllers.order.supplier-order.refunded',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => []
                    ],

                    'supplier.supplier.batchsend' => [
                        'name'       => '批量发货',
                        'url'        => 'plugin.supplier.supplier.controllers.order.batchsend.index',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'item'       => '',
                        'parents'    => ['supplier_supplier_menu'],
                    ],

                    'supplier.supplier.getexample' => [
                        'name'       => '下载模版',
                        'url'        => 'plugin.supplier.supplier.controllers.order.batchsend.getexample',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 0,
                        'icon'       => '',
                        'item'       => '',
                        'parents'    => ['supplier_supplier_menu'],
                    ],

                    'supplier.supplier.goods'          => [
                        'name'       => '商品',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.goods.supplier-goods-list.index',//plugin.supplier.admin.controllers.goods.supplier-goods-list.goods-list
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => [
                            'supplier.goods.add'            => [
                                'name'       => '添加商品',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.goods.goods-operation.add',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu', 'supplier.supplier.goods'],
                            ],
                            'supplier.goods.search'         => [
                                'name'       => '搜索商品',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.goods.supplier-goods-list.goods-search',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu', 'supplier.supplier.goods'],
                            ],
                            'supplier.goods.list'           => [
                                'name'       => '商品列表',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.goods.supplier-goods-list.goods-list',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                            ],
                            'supplier.goods.edit'           => [
                                'name'       => '修改商品',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.goods.goods-operation.edit',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu', 'supplier.supplier.goods'],
                            ],
                            'supplier.goods.change'         => [
                                'name'       => '更改属性',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.goods.goods-operation.change',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                            ],
                            'supplier.goods.delete'         => [
                                'name'       => '删除商品',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.goods.goods-operation.delete',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                            ],
                            'supplier.goods.batch_delete'   => [
                                'name'       => '批量删除商品',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.goods.goods-operation.batchDelete',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                            ],
                            'supplier.goods.copy'           => [
                                'name'       => '复制商品',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.goods.goods-operation.copy',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                            ],
                            'supplier.goods.sort'           => [
                                'name'       => '商品排序',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.goods.goods-operation.sort',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                            'supplier.goods.getspectpl'     => [
                                'name'       => '添加规格',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'goods.goods.getSpecTpl',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                            'supplier.goods.getspecitemtpl' => [
                                'name'       => '添加规格项',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'goods.goods.getSpecItemTpl',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                            'supplier.goods.set'            => [
                                'name'       => '热卖',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'goods.goods.setProperty',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                            'supplier.goods.search-member'  => [
                                'name'       => '选择通知人',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.member.query.index',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                        ]
                    ],
                    'supplier.supplier.withdraw'       => [
                        'name'       => '提现',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.withdraw.supplier-withdraw-log.index',
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => [
                            'withdraw.apply'  => [
                                'name'       => '手动提现',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.withdraw.supplier-withdraw.apply',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                            'withdraw.detail' => [
                                'name'       => '详情',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.withdraw.supplier-withdraw.detail',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ]
                        ]
                    ],
                    'supplier.supplier.dispatch'       => [
                        'name'       => '配送模板',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.dispatch.supplier-dispatch-list.index',
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => [
                            'dispatch.add'    => [
                                'name'       => '添加配送模板',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'goods.dispatch.add',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                            'dispatch.edit'   => [
                                'name'       => '修改配送模板',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'goods.dispatch.edit',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                            'dispatch.delete' => [
                                'name'       => '删除配送模板',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'goods.dispatch.delete',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                            'dispatch.sort'   => [
                                'name'       => '提交排序',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'goods.dispatch.sort',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                            'dispatch.area'   => [
                                'name'       => '区域选择',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'area.area.select-city',
                                'url_params' => [],
                                'parents'    => ['supplier'],
                                'child'      => [

                                ]
                            ],
                        ]
                    ],
                    'supplier.supplier.return-address' => [
                        'name'       => '退货地址设置',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.address.return-address.index',
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => [
                            'dispatch.add'    => [
                                'name'       => '添加退货地址',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.address.return-address.add',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                            'dispatch.edit'   => [
                                'name'       => '修改退货地址',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.address.return-address.edit',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                            'dispatch.delete' => [
                                'name'       => '删除退货地址',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.address.return-address.delete',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => [

                                ]
                            ],
                        ]
                    ],
                    'supplier.supplier.meiqia'         => [
                        'name'       => '客服链接',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.meiqia.set.index',
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => [

                        ]
                    ],
                    'supplier.supplier.address.apply'  => [
                        'name'       => '地址更改申请',
                        'permit'     => 0,
                        'menu'       => app('plugins')->isEnabled('region-mgt') ? 1 : 0,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.address.apply.index',
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => [

                        ]
                    ],
                    'supplier.supplier.info'           => [
                        'name'       => '基础设置',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.info.index',
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => [

                        ]
                    ],

                    'supplier.supplier.slide' => [
                        'name'       => '幻灯片',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.slide.index',
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => [
                            'supplier.supplier.slide.index' => [
                                'name'       => '幻灯片',
                                'permit'     => 0,
                                'menu'       => 1,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.slide.index',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => []
                            ],

                            'supplier.supplier.slide.create' => [
                                'name'       => '',
                                'permit'     => 0,
                                'menu'       => 1,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.slide.create',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => []
                            ],

                            'supplier.supplier.slide.edit' => [
                                'name'       => '',
                                'permit'     => 0,
                                'menu'       => 1,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.slide.edit',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => []
                            ],

                            'supplier.supplier.slide.deleted' => [
                                'name'       => '',
                                'permit'     => 0,
                                'menu'       => 1,
                                'icon'       => '',
                                'url'        => 'plugin.supplier.supplier.controllers.slide.deleted',
                                'url_params' => [],
                                'parents'    => ['supplier_supplier_menu'],
                                'child'      => []
                            ]
                        ]
                    ],

                    'supplier.supplier.adv' => [
                        'name'       => '广告位',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.supplier-advs.index',
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => []
                    ],
                    'supplier.supplier.list' => [
                        'name'       => '提成明细',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'url'        => 'plugin.supplier.supplier.controllers.income.list.index',
                        'url_params' => [],
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => []
                    ],
                    /*'supplier.supplier.taobao' => [
                        'name' => '淘宝商品导入',
                        'permit' => '',
                        'menu' => 1,
                        'icon' => '',
                        'url' => 'plugin.goods-assistant.admin.import.taobao',
                        'url_params' => [],
                        'parents'=>['supplier_supplier_menu'],
                    ],
                    'supplier.supplier.jingdong' => [
                        'name' => '京东商品导入',
                        'permit' => '',
                        'menu' => 1,
                        'icon' => '',
                        'url' => 'plugin.goods-assistant.admin.import.jingdong',
                        'url_params' => [],
                        'parents'=>['supplier_supplier_menu'],
                    ]*/
                ]
            ];
            if (app('plugins')->isEnabled('goods-assistant')) {
                $menu['child']['supplier.supplier.taobao'] = [
                    'name'       => '淘宝助手',
                    'url'        => 'plugin.supplier.supplier.controllers.taobao.import.taobao',
                    'url_params' => '',
                    'permit'     => 0,
                    'menu'       => 1,
                    'icon'       => '',
                    'parents'    => ['supplier_supplier_menu'],
                    'child'      => []
                ];
                $menu['child']['supplier.supplier.csv'] = [
                    'name'       => '淘宝CSV上传',
                    'url'        => 'plugin.supplier.supplier.controllers.taobao.import.taobaoCSV',
                    'url_params' => '',
                    'permit'     => 0,
                    'menu'       => 1,
                    'icon'       => '',
                    'parents'    => ['supplier_supplier_menu'],
                    'child'      => []
                ];
            }

            $supplier_setting = Setting::get('plugin.supplier');

            if (!empty($supplier_setting['insurance_policy']) && $supplier_setting['insurance_policy']) {//开启状态
                $menu['child']['supplier.supplier.insurance'] = [
                    'name'       => '保单管理',
                    'permit'     => 0,
                    'menu'       => 1,
                    'icon'       => '',
                    'url'        => 'plugin.supplier.supplier.controllers.insurance.insurance.index',
                    'url_params' => [],
                    'parents'    => ['supplier_supplier_menu'],
                    'child'      => [
                        'supplier.supplier.insurance_edit' => [
                            'name'       => '保单编辑',
                            'permit'     => 0,
                            'menu'       => 1,
                            'icon'       => '',
                            'url'        => 'plugin.supplier.supplier.controllers.insurance.insurance.insuranceEdit',
                            'url_params' => [],
                            'parents'    => ['supplier_supplier_menu', 'insurance'],
                        ],
                        'supplier.supplier.insurance_ddd' => [
                            'name'       => '保单添加',
                            'permit'     => 0,
                            'menu'       => 1,
                            'icon'       => '',
                            'url'        => 'plugin.supplier.supplier.controllers.insurance.insurance.insuranceAdd',
                            'url_params' => [],
                            'parents'    => ['supplier_supplier_menu', 'insurance'],
                        ],
                        'supplier.supplier.policy-template' => [
                            'name'       => '保单数据模板下载',
                            'permit'     => 0,
                            'menu'       => 1,
                            'icon'       => '',
                            'url'        => 'plugin.supplier.supplier.controllers.insurance.insurance.getExample',
                            'url_params' => [],
                            'parents'    => ['supplier_supplier_menu', 'insurance'],
                        ],
                        'supplier.supplier.import-excel' => [
                            'name'       => '导入Excel',
                            'permit'     => 0,
                            'menu'       => 1,
                            'icon'       => '',
                            'url'        => 'plugin.supplier.supplier.controllers.insurance.batchsend.index',
                            'url_params' => [],
                            'parents'    => ['supplier_supplier_menu', 'insurance'],
                        ],
                        'supplier.supplier.export-example' => [
                            'name'       => '导出表格',
                            'permit'     => 0,
                            'menu'       => 1,
                            'icon'       => '',
                            'url'        => 'plugin.supplier.supplier.controllers.insurance.insurance.exportExample',
                            'url_params' => [],
                            'parents'    => ['supplier_supplier_menu', 'insurance'],
                        ],
                        'supplier.supplier.insurance-del' => [
                            'name'       => '删除保单',
                            'permit'     => 0,
                            'menu'       => 1,
                            'icon'       => '',
                            'url'        => 'plugin.supplier.supplier.controllers.insurance.insurance.insuranceDel',
                            'url_params' => [],
                            'parents'    => ['supplier_supplier_menu', 'insurance'],
                        ],
                        'supplier.supplier.insurance-note' => [
                            'name'       => '导出表格',
                            'permit'     => 0,
                            'menu'       => 1,
                            'icon'       => '',
                            'url'        => 'plugin.supplier.supplier.controllers.insurance.insurance.insuranceNote',
                            'url_params' => [],
                            'parents'    => ['supplier_supplier_menu', 'insurance'],
                        ],
                        'supplier.supplier.search-insurance-company' => [
                            'name'       => '搜索保险公司',
                            'permit'     => 0,
                            'menu'       => 1,
                            'icon'       => '',
                            'url'        => 'plugin.supplier.supplier.controllers.insurance.insurance.getSearchInsCompany',
                            'url_params' => [],
                            'parents'    => ['supplier_supplier_menu', 'insurance'],
                        ],
                        'supplier.supplier.create-ins-code' => [
                            'name'       => '生成二维码',
                            'permit'     => 0,
                            'menu'       => 1,
                            'icon'       => '',
                            'url'        => 'plugin.supplier.supplier.controllers.insurance.insurance.createInsCode',
                            'url_params' => [],
                            'parents'    => ['supplier_supplier_menu', 'insurance'],
                        ],
                        'supplier.supplier.pdf-list' => [
                            'name'       => '文件列表',
                            'permit'     => 0,
                            'menu'       => 1,
                            'icon'       => '',
                            'url'        => 'plugin.supplier.supplier.controllers.insurance.insurance.pdfList',
                            'url_params' => [],
                            'parents'    => ['supplier_supplier_menu', 'insurance'],
                        ],
                    ]
                ];

            }

            //独立后台订单退款权限
            if (!empty($supplier_setting['supplier_order_refund_right'] && $supplier_setting['supplier_order_refund_right'])) {
                $menu['child']['refund_list_refund'] = [
                    'name'       => '退换货订单',
                    'url'        => 'refund.list.index',
                    'url_params' => '',
                    'permit'     => 1,
                    'menu'       => 1,
                    'icon'       => 'fa-refresh',
                    'sort'       => '6',
                    'item'       => 'refund_list_refund',
                    'parents'    => ['Order'],
                    'child'      => [
                        'refund_order_handel' => [
                            'name'       => '退换货操作',
                            'url'        => 'order.handel',
                            'url_params' => '',
                            'permit'     => 0,
                            'menu'       => 0,
                            'icon'       => '',
                            'sort'       => 1,
                            'item'       => 'refund_order_handel',
                            'parents'    => ['Order', 'refund_list_refund'],
                            'child'      => [
                                'refund_detail_index' => [
                                    'name'       => '查看详情',
                                    'url'        => 'order.detail.index',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => 'fa-file-text',
                                    'sort'       => 1,
                                    'item'       => 'order_list_index',
                                    'parents'    => ['Order', 'refund_list_refund'],
                                ],

                                'refund_operation_reject'               => [
                                    'name'       => '驳回申请',
                                    'url'        => 'refund.operation.reject',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => '4',
                                    'item'       => 'refund_operation_reject',
                                    'parents'    => ['Order', 'refund_list_refund'],
                                    'child'      => []
                                ],
                                'refund_pay_index'                      => [
                                    'name'       => '同意退款',
                                    'url'        => 'refund.pay.index',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => '4',
                                    'item'       => 'refund_pay_index',
                                    'parents'    => ['Order', 'refund_list_refund'],
                                    'child'      => []
                                ],
                                'refund_operation_consensus'            => [
                                    'name'       => '手动退款',
                                    'url'        => 'refund.operation.consensus',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => '4',
                                    'item'       => 'refund_operation_consensus',
                                    'parents'    => ['Order', 'refund_list_refund'],
                                    'child'      => []
                                ],
                                'refund_operation_pass'                 => [
                                    'name'       => '通过申请(需要客户寄回商品)',
                                    'url'        => 'refund.operation.pass',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => 'fa-circle-o',
                                    'sort'       => '4',
                                    'item'       => 'refund_operation_pass',
                                    'parents'    => ['Order', 'refund_list_refund'],
                                    'child'      => []
                                ],
                                'refund_operation_receive_return_goods' => [
                                    'name'       => '商家确认收货',
                                    'url'        => 'refund.operation.receiveReturnGoods',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => 'fa-circle-o',
                                    'sort'       => '4',
                                    'item'       => 'refund_operation_receive_return_goods',
                                    'parents'    => ['Order', 'refund_list_refund'],
                                    'child'      => []
                                ],
                                'refund_operation_resend'               => [
                                    'name'       => '商家重新发货',
                                    'url'        => 'refund.operation.resend',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => 'fa-circle-o',
                                    'sort'       => '4',
                                    'item'       => 'refund_operation_resend',
                                    'parents'    => ['Order', 'refund_list_refund'],
                                    'child'      => []
                                ],
                                'refund_operation_close'                => [
                                    'name'       => '关闭申请(换货完成)',
                                    'url'        => 'refund.operation.close',
                                    'url_params' => '',
                                    'permit'     => 0,
                                    'menu'       => 0,
                                    'icon'       => '',
                                    'sort'       => '4',
                                    'item'       => 'refund_operation_close',
                                    'parents'    => ['Order', 'refund_list_refund'],
                                    'child'      => []
                                ],
                            ],
                        ],

                    ],
                ];
            }
            if (app('plugins')->isEnabled('customer-service')) {
                if(\Setting::get('customer-service.is_open') == 1)
                {
                    $menu['child']['supplier.supplier.customer-service'] = [
                        'name'       => '客服设置',
                        'url'        => 'plugin.supplier.supplier.controllers.customer.set.index',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => '',
                        'parents'    => ['supplier_supplier_menu'],
                        'child'      => []
                    ];
                }
            }
            Menu::current()->setMainMenu('supplier_supplier_menu', $menu);

            if (app('plugins')->isEnabled('more-printer')) {
                $printer_menu = [
                    'supplier_more_printer_list' => [
                        'name'       => '打印机管理',
                        'url'        => 'plugin.supplier.supplier.modules.moreprinter.printer.list.index',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => 'fa-list',
                        'item'       => 'supplier_more_printer_list',
                        'parents'    => ['supplier_more_printer'],
                        'child'      => [

                            'supplier_more_printer_add' => [
                                'name'       => '添加',
                                'url'        => 'plugin.supplier.supplier.modules.moreprinter.printer.operation.add',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'supplier_more_printer_add',
                                'parents'    => ['supplier_more_printer', 'supplier_more_printer_list'],
                            ],

                            'supplier_more_printer_edit' => [
                                'name'       => '修改',
                                'url'        => 'plugin.supplier.supplier.modules.moreprinter.printer.operation.edit',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'supplier_more_printer_edit',
                                'parents'    => ['supplier_more_printer', 'supplier_more_printer_list'],
                            ]
                        ]
                    ],

                    'supplier_more_temp_list' => [
                        'name'       => '模板库管理',
                        'url'        => 'plugin.supplier.supplier.modules.moreprinter.temp.list.index',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => 'fa-list',
                        'item'       => 'supplier_more_temp_list',
                        'parents'    => ['supplier_more_printer'],
                        'child'      => [

                            'supplier_more_temp_list_add' => [
                                'name'       => '添加',
                                'url'        => 'plugin.supplier.supplier.modules.moreprinter.temp.operation.add',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => 'fa-clipboard',
                                'item'       => 'supplier_more_temp_list_add',
                                'parents'    => ['supplier_more_printer', 'supplier_more_temp_list'],
                            ],

                            'supplier_more_temp_list_edit' => [
                                'name'       => '修改',
                                'url'        => 'plugin.supplier.supplier.modules.moreprinter.temp.operation.edit',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => 'fa-clipboard',
                                'item'       => 'supplier_more_temp_list_edit',
                                'parents'    => ['supplier_more_printer', 'supplier_more_temp_list'],
                            ],

                            'supplier_more_temp_list_temp' => [
                                'name'       => '添加建',
                                'url'        => 'plugin.more-printer.admin.temp.operation.tpl',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'supplier_more_temp_list_temp',
                                'parents'    => ['supplier_more_printer', 'supplier_more_temp_list'],
                            ]
                        ]
                    ],

                    'supplier_more_printer_set' => [
                        'name'       => '打印机设置',
                        'url'        => 'plugin.supplier.supplier.modules.moreprinter.set.index.index',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => 'fa-cogs',
                        'item'       => 'supplier_more_printer_set',
                        'parents'    => ['supplier_more_printer'],
                        'child'      => [
                            'supplier_more_printer_set_sub' => [
                                'name'       => '提交',
                                'url'        => 'plugin.supplier.supplier.modules.moreprinter.set.sub.index',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'supplier_more_printer_set_sub',
                                'parents'    => [],
                            ]
                        ]
                    ]
                ];


                \app\backend\modules\menu\Menu::current()->setPluginMenu('supplier_more_printer', [
                    'name'             => '多打印机',
                    'type'             => 'tool',
                    'url'              => 'plugin.supplier.supplier.modules.moreprinter.printer.list.index',// url 可以填写http 也可以直接写路由
                    'urlParams'        => '',//如果是url填写的是路由则启用参数否则不启用
                    'permit'           => 0,//如果不设置则不会做权限检测
                    'menu'             => 1,//如果不设置则不显示菜单，子菜单也将不显示
                    'top_show'         => 1,
                    'left_first_show'  => 0,
                    'left_second_show' => 1,
                    'icon'             => 'fa-print',//菜单图标
                    'list_icon'        => 'printer',
                    'parents'          => [],
                    'child'            => $printer_menu
                ]);
            }
            if (app('plugins')->isEnabled('printer')) {//判断打印机插件置顶则不加载
                $printer_menus = [
                    'printer_list' => [
                        'name'       => '打印机管理',
                        'url'        => 'plugin.printer.admin.list.index',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => 'fa-list',
                        'item'       => 'printer_list',
                        'parents'    => ['printer'],
                        'child'      => [

                            'printer_add' => [
                                'name'       => '添加',
                                'url'        => 'plugin.printer.admin.list.add',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'printer_add',
                                'parents'    => ['printer', 'printer_list'],
                            ],

                            'printer_edit' => [
                                'name'       => '修改',
                                'url'        => 'plugin.printer.admin.list.edit',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'printer_edit',
                                'parents'    => ['printer', 'printer_list'],
                            ],

                            'printer_del' => [
                                'name'       => '删除',
                                'url'        => 'plugin.printer.admin.list.del',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'printer_del',
                                'parents'    => ['printer', 'printer_list'],
                            ],

                            'printer_change_status' => [
                                'name'       => '更改状态',
                                'url'        => 'plugin.printer.admin.list.change-status',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'printer_change_status',
                                'parents'    => ['printer', 'printer_list'],
                            ]
                        ]
                    ],

                    'temp_list' => [
                        'name'       => '模板库管理',
                        'url'        => 'plugin.printer.admin.temp.index',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => 'fa-list',
                        'item'       => 'temp_list',
                        'parents'    => ['printer'],
                        'child'      => [

                            'temp_list_add' => [
                                'name'       => '添加',
                                'url'        => 'plugin.printer.admin.temp.add',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => 'fa-clipboard',
                                'item'       => 'temp_list_add',
                                'parents'    => ['printer', 'temp_list'],
                            ],

                            'temp_list_edit' => [
                                'name'       => '修改',
                                'url'        => 'plugin.printer.admin.temp.edit',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => 'fa-clipboard',
                                'item'       => 'temp_list_edit',
                                'parents'    => ['printer', 'temp_list'],
                            ],

                            'temp_list_del' => [
                                'name'       => '删除',
                                'url'        => 'plugin.printer.admin.temp.del',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'temp_list_del',
                                'parents'    => ['printer', 'temp_list'],
                            ],


                            'temp_list_tpl' => [
                                'name'       => '添加建',
                                'url'        => 'plugin.printer.admin.temp.tpl',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'temp_list_tpl',
                                'parents'    => ['printer', 'temp_list'],
                            ]
                        ]
                    ],

                    'printer_set' => [
                        'name'       => '打印机设置',
                        'url'        => 'plugin.printer.admin.set.index',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => 'fa-cogs',
                        'item'       => 'printer_set',
                        'parents'    => ['printer'],
                        'child'      => []
                    ]
                ];

                \app\backend\modules\menu\Menu::current()->setPluginMenu('printer', [
                    'name'             => '打印机',
                    'type'             => 'tool',
                    'url'              => 'plugin.printer.admin.list.index',// url 可以填写http 也可以直接写路由
                    'urlParams'        => '',//如果是url填写的是路由则启用参数否则不启用
                    'permit'           => 0,//如果不设置则不会做权限检测
                    'menu'             => 1,//如果不设置则不显示菜单，子菜单也将不显示
                    'top_show'         => 1,
                    'left_first_show'  => 0,
                    'left_second_show' => 1,
                    'icon'             => 'fa-print',//菜单图标
                    'list_icon'        => 'printer',
                    'parents'          => [],
                    'child'            => $printer_menus
                ]);
            }
        }
    }
}