<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/9
 * Time: 上午11:52
 */
use app\common\services\Hook;
use Illuminate\Contracts\Events\Dispatcher;

return function (Dispatcher $events) {

    // 手动分红配置
    Config::set('manual_arr.micro_and_level', [
        'uidColumn' => 'member_id',
        'levelNameColumn' => 'level_name',
        'relationsColumn' => 'level_id',
        'enableLevel' => true,
        'type_name' => '微店',
        'role_type' => 'micro_and_level'
    ]);

    Config::set('manual_arr_cfg.micro_and_level', [
        'roleTableClass' => \Yunshop\Micro\common\models\MicroShop::class,
        'levelTableClass' => \Yunshop\Micro\common\models\MicroShopLevel::class
    ]);

    /**
     * 收入页面信息配置
     */
    \Config::set('income_page.micro', ['class' => 'Yunshop\Micro\frontend\services\IncomePageService',]);


    \Config::set('template.micro_become_micro', [
        'title' => '微店(成为微店通知)',
        'subtitle' => '成为微店通知',
        'value' => 'micro_become_micro',
        'param' => [
            '商城名称', '昵称', '时间', '店主等级', '分红比例'
        ]
    ]);

    \Config::set('template.micro_micro_upgrade', [
        'title' => '微店(微店升级通知)',
        'subtitle' => '微店升级通知',
        'value' => 'micro_micro_upgrade',
        'param' => [
            '昵称', '时间', '店主等级', '分红比例'
        ]
    ]);

    \Config::set('template.micro_order_bonus', [
        'title' => '微店(分红订单通知)',
        'subtitle' => '分红订单通知',
        'value' => 'micro_order_bonus',
        'param' => [
            '昵称', '时间', '分红金额'
        ]
    ]);

    \Config::set('template.micro_lower_bonus', [
        'title' => '微店(下级微店分红通知)',
        'subtitle' => '下级微店分红通知',
        'value' => 'micro_lower_bonus',
        'param' => [
            '昵称', '时间', '下级昵称', '分红金额'
        ]
    ]);

    \Config::set('template.micro_bonus_settlement', [
        'title' => '微店(微店分红结算通知)',
        'subtitle' => '微店分红结算通知',
        'value' => 'micro_bonus_settlement',
        'param' => [
            '昵称', '时间', '分红金额'
        ]
    ]);

    \Config::set('template.micro_agent_bonus_settlement', [
        'title' => '微店(上级微店分红结算通知)',
        'subtitle' => '上级微店分红结算通知',
        'value' => 'micro_agent_bonus_settlement',
        'param' => [
            '昵称', '时间', '分红金额'
        ]
    ]);

    \Config::set('template.micro_agent_gold', [
        'title' => '微店(上级店主金币奖励通知)',
        'subtitle' => '上级店主金币奖励通知',
        'value' => 'micro_agent_gold',
        'param' => [
            '昵称', '时间', '金币数量'
        ]
    ]);

    \Config::set('notice.micro', [
        //'upgrade_micro_title', //微店升级通知
        //'bonus_order_title', //分红订单通知
        //'lower_bonus_order_title', //下级微店分红通知
    ]);

    //删除商品
    \Config::set('observer.goods.micro_delete_goods',[
        'class'=>'Yunshop\Micro\observer\MicroDelGoods',
        'function_save'=>'deleteMicroGoods'
    ]);

    \Config::set('widget.withdraw.tab_micro_income', [
        'title' => trans('Yunshop\Micro::pack.micro_bonus_withdraw'),
        'class' => 'Yunshop\Micro\widgets\MicroWithdrawWidget'
    ]);

    \Config::set('widget.goods.micro', [
        'title' => trans('Yunshop\Micro::pack.micro_bonus'),
        'class' => 'Yunshop\Micro\widgets\MicroGoodsWidget'
    ]);

    \Config::set('observer.goods.micro',[
        'class'=>'Yunshop\Micro\widgets\MicroGoodsWidget',
        'function_save'=>'relationSave'
    ]);

    \Config::set('income.micro', [
        'title' => trans('Yunshop\Micro::pack.micro_bonus'),
        'type' => 'micro',
        'type_name' => trans('Yunshop\Micro::pack.micro_bonus'),
        'class' => \Yunshop\Micro\common\models\MicroShopBonusLog::class
    ]);

    \Config::set('plugin.micro', [
        'title' => '微店',
        'ico' => 'icon-weidian01',
        'type' => 'micro',
        'type_name' => trans('Yunshop\Micro::pack.micro_bonus'),
        'class' => \Yunshop\Micro\common\models\MicroShopBonusLog::class,
        'order_class' => \app\common\models\finance\IncomeOrder::class,
        'agent_class' => \Yunshop\Micro\common\models\MicroShop::class,
        'agent_name' => 'getMicroShopByMemberId',
    ]);

    \Config::set('plugins_menu.micro',[
        'name' => '微店',
        'type' => 'dividend',
        'url' => 'plugin.micro.backend.controllers.MicroShop.list.index',// url 可以填写http 也可以直接写路由
        'urlParams' => '',//如果是url填写的是路由则启用参数否则不启用
        'permit' => 1,//如果不设置则不会做权限检测
        'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
        'top_show'    => 0,
        'left_first_show'   => 0,
        'left_second_show'  => 1,
        'icon'              => 'fa-maxcdn',
        'list_icon'         => 'micro',
        'item'              => 'micro',
        'parents'           =>[],
        'child'             => [

            'micro_list'   => [
                'name'          => '微店管理',
                'url'           => 'plugin.micro.backend.controllers.MicroShop.list.index',
                'url_params'    => '',
                'permit'        => 1,
                'menu'          => 1,
                'icon'          => 'fa-list-ul',
                'item'          => 'micro',
                'parents'       =>['micro'],
                'child'         => [

                    'micro_list_index'   => [
                        'name'          => '浏览列表',
                        'url'           => 'plugin.micro.backend.controllers.MicroShop.list.index',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_list_index',
                        'parents'       =>['micro','micro_list'],
                    ],

                    'micro_list_export'   => [
                        'name'          => '导出 EXCEL',
                        'url'           => 'plugin.micro.backend.controllers.MicroShop.list.export',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_list_export',
                        'parents'       =>['micro','micro_list'],
                    ]

                ]
            ],

            'micro_level'  => [
                'name'          => '微店等级',
                'url'           => 'plugin.micro.backend.controllers.MicroShopLevel.list.index',
                'url_params'    => '',
                'permit'        => 1,
                'menu'          => 1,
                'icon'          => 'fa-align-left',
                'item'          => 'micro_level',
                'parents'       =>['micro'],
                'child'         => [

                    'micro_level_index'   => [
                        'name'          => '浏览列表',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopLevel.list.index',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_level_index',
                        'parents'       =>['micro','micro_level'],
                    ],

                    'micro_level_add'   => [
                        'name'          => '添加等级',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopLevel.operation.add',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_level_add',
                        'parents'       =>['micro','micro_level'],
                    ],

                    'micro_level_edit'   => [
                        'name'          => '修改等级',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopLevel.operation.edit',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_level_edit',
                        'parents'       =>['micro','micro_level'],
                    ],

                    'micro_level_del'   => [
                        'name'          => '删除等级',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopLevel.operation.delete',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_level_del',
                        'parents'       =>['micro','micro_level'],
                    ],
                ]
            ],

            'micro_bonus'  => [
                'name'          => '分红记录',
                'url'           => 'plugin.micro.backend.controllers.MicroShopBonusLog.list.index',
                'url_params'    => '',
                'permit'        => 1,
                'menu'          => 1,
                'icon'          => 'fa-clock-o',
                'item'          => 'micro_bonus',
                'parents'       =>['micro'],
                'child'         => [

                    'micro_bonus_index'   => [
                        'name'          => '浏览列表',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopBonusLog.list.index',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_bonus_index',
                        'parents'       =>['micro','micro_bonus'],
                    ],

                    'micro_bonus_detail'   => [
                        'name'          => '查看详情',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopBonusLog.detail.index',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_bonus_detail',
                        'parents'       =>['micro','micro_bonus'],
                    ],

                    'micro_bonus_export'   => [
                        'name'          => '导出 EXCEL',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopBonusLog.list.export',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_bonus_export',
                        'parents'       =>['micro','micro_bonus'],
                    ]
                ]
            ],


            'micro_carousel'  => [
                'name'          => '图片轮播',
                'url'           => 'plugin.micro.backend.controllers.MicroShopCarousel.list.index',
                'url_params'    => '',
                'permit'        => 1,
                'menu'          => 1,
                'icon'          => 'fa-image',
                'item'          => 'micro_carousel',
                'parents'       =>['micro'],
                'child'         => [

                    'micro_carousel_index'   => [
                        'name'          => '浏览列表',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopCarousel.list.index',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_carousel_index',
                        'parents'       =>['micro','micro_carousel'],
                    ],

                    'micro_carousel_add'   => [
                        'name'          => '添加轮播',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopCarousel.list.add',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_carousel_add',
                        'parents'       =>['micro','micro_carousel'],
                    ],

                    'micro_carousel_edit'   => [
                        'name'          => '修改轮播',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopCarousel.list.edit',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_carousel_edit',
                        'parents'       =>['micro','micro_carousel'],
                    ],

                    'micro_carousel_del'   => [
                        'name'          => '删除轮播',
                        'url'           => 'plugin.micro.backend.controllers.MicroShopCarousel.list.delete',
                        'url_params'    => '',
                        'permit'        => 1,
                        'menu'          => 0,
                        'icon'          => '',
                        'item'          => 'micro_carousel_del',
                        'parents'       =>['micro','micro_carousel'],
                    ]
                ]
            ],
            /*'micro_advertise' => [
                'name' => '广告位',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => \app\common\helpers\Url::absoluteWeb('plugin.micro.backend.controllers.MicroShopAdvertise.list'),
                'urlParams' => [],
                'parents'=>['micro'],
                'child' => [

                ]
            ],*/
            'micro_set' => [
                'name' => '基础设置',
                'permit' => 1,
                'menu' => 1,
                'icon' => 'fa-cogs',
                'url' => 'plugin.micro.backend.controllers.MicroShopSet.set.index',
                'urlParams' => [],
                'parents'=>['micro'],
                'child' => [

                ]
            ],
        ]
    ]);

    // todo 定时任务
    \Event::listen('cron.collectJobs', function() {
        \Log::info("--微店start--");
        \Cron::add('Micro', '*/10 * * * * *', function() {
            (new \Yunshop\Micro\common\services\TimedTaskService())->handle();
            return ;
        });
    });

    // todo 成为微店
    $events->subscribe(\Yunshop\Micro\Listener\ChangeMicroListener::class);

    // todo 分红记录
    $events->subscribe(\Yunshop\Micro\Listener\CreateBonusLogListener::class);

    // todo 更改分红记录的支付相关信息
    $events->subscribe(\Yunshop\Micro\Listener\EditBonusLogByPayListener::class);

    // todo 监听金币充值
    $events->subscribe(\Yunshop\Micro\Listener\AgentMicroBonusListener::class);

    // todo 监听订单退款事件
    $events->subscribe(\Yunshop\Micro\Listener\RefundBonusLogListener::class);

    // todo 监听订单关闭事件
    $events->subscribe(\Yunshop\Micro\Listener\CanceledBonusLogListener::class);

    $events->subscribe(\Yunshop\Micro\Listener\EditBonusLogByCompleteListener::class);
};