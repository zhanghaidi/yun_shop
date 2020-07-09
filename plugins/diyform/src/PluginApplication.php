<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\Diyform;


use app\common\modules\shop\ShopConfig;
use Yunshop\Diyform\models\PreOrderGoodsDiyForm;
use Yunshop\Diyform\widgets\DiyFormOrderWidget;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {
        //前后端订单详情显示
        ShopConfig::current()->set('shop-foundation.order.order_detail.diyform', [
            'class' => DiyFormOrderWidget::class,
        ]);

        ShopConfig::current()->push('shop-foundation.order-goods.relations', [
            'key' => 'diyForm',
            'class' => function ($attributes) {
                return new PreOrderGoodsDiyForm($attributes);
            }
        ]);
    }

    protected function setMenuConfig()
    {
        \app\backend\modules\menu\Menu::current()->setPluginMenu('diyform', [
            'name' => '自定义表单',
            'type' => 'tool',
            'url' => 'plugin.diyform.admin.diyform.manage',// url 可以填写http 也可以直接写路由
            'url_params' => '',//如果是url填写的是路由则启用参数否则不启用
            'permit' => 1,//如果不设置则不会做权限检测
            'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => 'fa-wpforms',//菜单图标
            'list_icon' => 'diyform',
            'parents' => [],
            'child' => [
                'plugin.diyform.admin.diyform' => [
                    'name' => '自定义表单管理',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.diyform.admin.diyform.manage',
                    'url_params' => [],
                    'parents' => ['diyform'],
                    'child' => [
                        'plugin.diyform.admin.diyform-manage' => [
                            'name' => '自定义表单管理',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.diyform.admin.diyform.manage',
                            'url_params' => [],
                            'parents' => ['diyform', 'plugin.diyform.admin.diyform']
                        ],
                        'plugin.diyform.admin.diyform-add-form' => [
                            'name' => '添加自定义表单',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.diyform.admin.diyform.add-form',
                            'url_params' => [],
                            'parents' => ['diyform', 'plugin.diyform.admin.diyform']
                        ],
                        'plugin.diyform.admin.diyform-get-form-tpl' => [
                            'name' => '添加字段',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.diyform.admin.diyform-tpl.get-form-tpl',
                            'url_params' => [],
                            'parents' => ['diyform', 'plugin.diyform.admin.diyform']
                        ],
                        'plugin.diyform.admin.diyform-edit-form' => [
                            'name' => '编辑自定义表单',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.diyform.admin.diyform.edit-form',
                            'url_params' => [],
                            'parents' => ['diyform', 'plugin.diyform.admin.diyform']
                        ],
                        'plugin.diyform.admin.diyform-del-form' => [
                            'name' => '删除自定义表单',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.diyform.admin.diyform.del-form',
                            'url_params' => [],
                            'parents' => ['diyform', 'plugin.diyform.admin.diyform']
                        ],
                        'plugin.diyform.admin.diyform-data' => [
                            'name' => '查看自定义表单数据',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.diyform.admin.diyform-data.get-form-data',
                            'url_params' => [],
                            'parents' => ['diyform', 'plugin.diyform.admin.diyform']
                        ],
                        'plugin.diyform.admin.diyform-data-detail' => [
                            'name' => '自定义表单数据详情',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.diyform.admin.diyform-data.get-form-data-detail',
                            'url_params' => [],
                            'parents' => ['diyform', 'plugin.diyform.admin.diyform']
                        ],
                        'plugin.diyform.admin.diyform-export' => [
                            'name' => '导出自定义表单数据',
                            'permit' => 1,
                            'menu' => 0,
                            'icon' => '',
                            'url' => 'plugin.diyform.admin.diyform-data.export',
                            'url_params' => [],
                            'parents' => ['diyform', 'plugin.diyform.admin.diyform']
                        ],
                        'diyform-order' => [
                            'name'              => '自定义表单订单详情',
                            'url'               => 'plugin.diyform.admin.diyform.getFormDataByOderId',
                            'url_params'        => '',
                            'permit'            => 1,
                            'menu'              => 0,
                            'icon'              => '',
                            'sort'              => 1,
                            'item'              => 'diyform-order',
                            'parents'           => ['diyform', 'plugin.diyform.admin.diyform'],
                        ],
                    ]
                ],

            ]
        ]);
    }

    public function getWidgetItems()
    {
        return ['goods.diyForm' => [
            'title' => trans('自定义表单管理'),
            'class' => 'Yunshop\Diyform\widgets\DiyformWidget'
        ]];
    }

    public function boot()
    {
        $events = app('events');
        /**
         * 创建订单
         * OrderCreatedListener
         */
        $events->subscribe(\Yunshop\Diyform\Listener\OrderCreatedListener::class);

        \app\common\modules\shop\ShopConfig::current()->set('observer.goods.diyform', [
            'class' => 'Yunshop\Diyform\models\DiyformOrderModel',
            'function_save' => 'relationSave'
        ]);

    }

}