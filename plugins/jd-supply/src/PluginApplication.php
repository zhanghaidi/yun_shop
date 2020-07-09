<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 10:36
 */

namespace Yunshop\JdSupply;

use app\common\models\MemberCart;
use app\common\services\Plugin;
use app\frontend\modules\order\operations\member\ExpressInfo;
use Yunshop\JdSupply\frontend\order\OrderManager;


class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
        /**
         * 商品挂件保存
         */
        \app\common\modules\shop\ShopConfig::current()->set('observer.goods.jd_supply', [
            'class' => 'Yunshop\JdSupply\models\JdGoods',
            'function_save' => 'relationSave'
        ]);

        //前端插件商品显示
        \app\common\modules\shop\ShopConfig::current()->push('shop-foundation.goods.plugin', 44);


        $memberOrderOperations = \app\common\modules\shop\ShopConfig::current()->get('shop-foundation.order.member_order_operations');
        foreach ($memberOrderOperations as &$statusOperations) {
            foreach ($statusOperations as &$operation) {
                if ($operation == ExpressInfo::class) {
                    $operation = \Yunshop\JdSupply\common\order\operations\member\ExpressInfo::class;
                }
            }
        }

        \app\common\modules\shop\ShopConfig::current()->set('shop-foundation.order.member_order_operations', $memberOrderOperations);

    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('jd_supply', [
            'name' => '聚合供应链',
            'url' => 'plugin.jd-supply.admin.set.index',// url 可以填写http 也可以直接写路由
            'url_params' => '',//如果是url填写的是路由则启用参数否则不启用
            'permit' => 1,//如果不设置则不会做权限检测
            'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => '',
            'list_icon' => 'jd_supply',
            'parents' => [],
            'type' => 'industry',
            'child' => [
                'jd_supply_set' => [
                    'name' => '基础设置',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.jd-supply.admin.set.index',
                    'url_params' => '',
                    'parents' => ['jd_supply'],
                    'child' => []
                ],
                'jd_supply_goods_import' => [
                    'name' => '商品导入',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.jd-supply.admin.goods-import.index',
                    'url_params' => '',
                    'parents' => ['jd_supply'],
                    'child' => [
                        'jd_supply_goods_import_pagination' => [
                            'name' => '商品列表',
                            'url' => 'plugin.jd-supply.admin.goods-import.goods-pagination',
                            'permit' => 0,
                            'parents' => ['jd_supply', 'jd_supply_goods_import'],
                        ],
                        'jd_supply_goods_select' => [
                            'name' => '导入选中商品',
                            'url' => 'plugin.jd-supply.admin.goods-import.select',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_goods_import'],
                        ],
                        'jd_supply_goods_test' => [
                            'name' => '测试',
                            'url' => 'plugin.jd-supply.admin.goods-import.test',
                            'permit' => 0,
                            'parents' => ['jd_supply', 'jd_supply_goods_import'],
                        ],
                        'jd_supply_goods_category' => [
                            'name' => '搜索分类',
                            'url' => 'plugin.jd-supply.admin.goods-import.getChildrenCategory',
                            'permit' => 0,
                            'parents' => ['jd_supply', 'jd_supply_goods_import'],
                        ],
                    ]
                ],
                'jd_supply_goods_list' => [
                    'name' => '商品列表',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.jd-supply.admin.shop-goods.index',
                    'url_params' => '',
                    'parents' => ['jd_supply'],
                    'child' => [
                        'jd_supply_goods_list_search' => [
                            'name' => '列表搜索',
                            'url' => 'plugin.jd-supply.admin.shop-goods.goods-search',
                            'url_params' => '',
                            'permit' => 0,
                            'menu' => 0,
                            'icon' => '',
                            'item' => 'jd_supply_goods_list_search',
                            'parents' => ['jd_supply', 'jd_supply_goods_list'],
                        ],
                        'jd_supply_goods_list_see' => [
                            'name' => '浏览列表',
                            'url' => 'plugin.jd-supply.admin.shop-goods.goods-list',
                            'url_params' => '',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'item' => 'jd_supply_goods_list_see',
                            'parents' => ['jd_supply', 'jd_supply_goods_list'],
                        ],
                        'jd_supply_shop_goods-edit' => [
                            'name' => '编辑商品',
                            'url' => 'plugin.jd-supply.admin.shop-goods.edit',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_goods_list'],
                        ],
                        'jd_supply_shop_goods_batch-delete' => [
                            'name' => '批量删除商品',
                            'url' => 'plugin.jd-supply.admin.shop-goods.batch-delete',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_goods_list'],
                        ],
                        'jd_supply_shop_goods-delete' => [
                            'name' => '删除商品',
                            'url' => 'plugin.jd-supply.admin.shop-goods.delete',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_goods_list'],
                        ],
                        'jd_supply_shop_goods-displayorder' => [
                            'name' => '商品排序',
                            'url' => 'plugin.jd-supply.admin.shop-goods.displayorder',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_goods_list'],
                        ],
                        'jd_supply_shop_goods-update-jd-goods' => [
                            'name' => '同步更新商品',
                            'url' => 'plugin.jd-supply.admin.shop-goods.update-jd-goods',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_goods_list'],
                        ],
                        'jd_supply_shop_goods-batch_update' => [
                            'name' => '批量更新商品',
                            'url' => 'plugin.jd-supply.admin.shop-goods.batchUpdate',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_goods_list'],
                        ]
                    ]
                ],
                'jd_supply_order' => [
                    'name' => '订单列表',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.jd-supply.admin.order-list.index',
                    'url_params' => '',
                    'parents' => ['jd_supply'],
                    'child' => [
                        'jd_supply_order_detail' => [
                            'name' => '订单详情',
                            'url' => 'plugin.jd-supply.admin.order-list.detail',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_order'],
                        ],
                        'jd_supply_order_unlock_order' => [
                            'name' => '解锁订单',
                            'url' => 'plugin.jd-supply.admin.order-list.unlock-order',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_order'],
                        ],
                        'jd_supply_order_create_order' => [
                            'name' => '提交订单',
                            'url' => 'plugin.jd-supply.admin.order-list.create-order',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_order'],
                        ],
                    ]
                ],
                'jd_supply_create_order' => [
                    'name' => '提交订单到第三方',
                    'permit' => 1,
                    'menu' => 0,
                    'icon' => '',
                    'url' => 'plugin.jd-supply.admin.order-list.create_order',
                    'url_params' => '',
                    'parents' => ['jd_supply'],
                    'child' => []
                ],
                'jd_supply_push_message' => [
                    'name' => '推送消息',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.jd-supply.admin.push-message.index',
                    'url_params' => '',
                    'parents' => ['jd_supply'],
                    'child' => []
                ],'jd_supply_goods_control' => [
                    'name' => '单品风控',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.jd-supply.admin.goods-control.index',
                    'url_params' => '',
                    'parents' => ['jd_supply'],
                    'child' => [
                        'jd_supply_goods_control_add' => [
                            'name' => '单品风控添加',
                            'url' => 'plugin.jd-supply.admin.goods-control.add',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_goods_control'],
                        ],'jd_supply_goods_control_goods_search' => [
                            'name' => '单品风控商品搜索',
                            'url' => 'plugin.jd-supply.admin.goods-control.goods-search',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_goods_control'],
                        ],'jd_supply_goods_control_delete' => [
                            'name' => '单品风控删除',
                            'url' => 'plugin.jd-supply.admin.goods-control.delete',
                            'permit' => 1,
                            'parents' => ['jd_supply', 'jd_supply_goods_control'],
                        ],
                    ]
                ],
            ],
        ]);
    }

    public function boot()
    {

        $events = app('events');
        $this->singleton('OrderManager', OrderManager::class);
        $this->bind(MemberCart::class, function (PluginApplication $pluginApp, array $params) {
            return new \Yunshop\JdSupply\frontend\order\MemberCart($params[0]);
        });

        /**
         * 购物车分组
         */
//        $events->subscribe(\Yunshop\Supplier\Listener\GroupCartListener::class);
//        $events->subscribe(\Yunshop\Supplier\Listener\ShowPreGenerateOrderListener::class);

        /**
         * 购物车id分组
         */
//        $events->subscribe(\Yunshop\Supplier\Listener\GroupCartIdListener::class);


        /**
         * 订单支付
         */
        $events->subscribe(\Yunshop\JdSupply\Listener\AfterOrderPaidImmediatelyListener::class);

        /**
         * 订单支付前
         */
        $events->subscribe(\Yunshop\JdSupply\Listener\BeforeOrderValidateListener::class);

        //推送消息定时任务
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Jd-Supply-Message-Get', '*/1 * * * *', function () {
                (new \Yunshop\JdSupply\services\TimedTaskService())->handle();
                return;
            });
        });
    }
}