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

    /**
     * 收入页面信息配置
     */
    \Config::set('income_page.videoDemand', ['class' => 'Yunshop\VideoDemand\services\IncomePageService']);

    \Config::set('plugins_menu.video_demand', [
        'name' => '视频点播',
        'type' => 'industry',
        'url' => 'plugin.video-demand.admin.video-demand-set.index',// url 可以填写http 也可以直接写路由
        'url_params' => '',//如果是url填写的是路由则启用参数否则不启用
        'permit' => 1,//如果不设置则不会做权限检测
        'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
        'top_show'    => 0,
        'left_first_show'   => 0,
        'left_second_show'   => 1,
        'icon' => 'fa-bars',//菜单图标
        'list_icon' => 'video_demand',//菜单图标
        'parents' => [],
        'child' => [
            'video_demand_set' => [
                'name' => '基础设置',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.video-demand.admin.video-demand-set.index',
                'url_params' => '',
                'parents'=>['video_demand'],
                'child' => []
            ],
            'slide' => [
                'name' => '幻灯片管理',
                'url' => 'plugin.video-demand.admin.video-slide.index',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url_params' => '',
                'parents'=>['video_demand'],
                'child' => [
                    'slide_add' => [
                        'name' => '添加幻灯片',
                        'url' => 'plugin.video-demand.admin.video-slide.add',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['video_demand', 'slide'],
                    ],
                    'slide_edit' => [
                        'name' => '编辑幻灯片',
                        'url' => 'plugin.video-demand.admin.video-slide.edit',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['video_demand', 'slide'],
                    ],
                    'slide_del' => [
                        'name' => '删除幻灯片',
                        'url' => 'plugin.video-demand.admin.video-slide.deleted',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['video_demand', 'slide'],
                    ]
                ]
            ],
            'video_demand_lecturer' => [
                'name' => '讲师管理',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.video-demand.admin.lecturer.index',
                'url_params' => '',
                'parents'=>['video_demand'],
                'child' => [
                    'lecturer_add' => [
                        'name' => '添加讲师',
                        'url' => 'plugin.video-demand.admin.lecturer.add',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['video_demand', 'video_demand_lecturer'],
                    ],
                    'lecturer_export' => [
                        'name' => '讲师导出',
                        'url' => 'plugin.video-demand.admin.lecturer.export',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['video_demand', 'video_demand_lecturer'],
                    ]

                ]
            ],
            'video_demand_lecturer_reward' => [
                'name' => '讲师分红',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.video-demand.admin.lecturer-reward.index',
                'url_params' => '',
                'parents'=>['video_demand'],
                'child' => [
                    'reward_log' => [
                        'name' => '查看记录',
                        'url' => 'plugin.video-demand.admin.lecturer-reward.index',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['video_demand', 'video_demand_lecturer_reward'],
                    ],
                    'reward_export' => [
                        'name' => '记录导出',
                        'url' => 'plugin.video-demand.admin.lecturer-reward.export',
                        'permit' => 1,
                        'menu' => 0,
                        'parents' => ['video_demand', 'video_demand_lecturer_reward'],
                    ]

                ]
            ]
        ]
    ]);

    /**
     * 商品挂件保存
     */
    \Config::set('observer.goods.video_demand',[
        'class'=>'Yunshop\VideoDemand\models\CourseGoodsModel',
        'function_validator'=>'relationValidator',
        'function_save'=>'relationSave'
    ]);
    /**
     * 商品挂件
     */
    \Config::set('widget.goods.tab_video_demand', [
        'title' => '课程管理',
        'class' => 'Yunshop\VideoDemand\widgets\CourseWidget'
    ]);

    \Config::set('income.videoDemand', [
        'title' => '讲师分红',
        'type' => 'videoDemand',
        'type_name' => '讲师分红',
        'class' => 'Yunshop\VideoDemand\models\LecturerRewardLogModel',

    ]);
    \Config::set('plugin.videoDemand', [
        'title' => '讲师分红',
        'ico' => 'icon-lecturer01',
        'type' => 'videoDemand',
        'class' => 'Yunshop\VideoDemand\models\LecturerRewardLogModel',
        'order_class' => '',
        'agent_class' => '',
        'agent_name' => '',
        'agent_status' => '',
    ]);

    \Config::set('widget.withdraw.tab_video_demand', [
        'title' => '讲师分红提现',
        'class' => 'Yunshop\VideoDemand\widgets\VideoDemandWithdrawWidget'
    ]);

    \Config::set('template.video_demand_order_reward', [
        'title' => '讲师分红(讲师分红订单通知)',
        'subtitle' => '讲师分红订单通知',
        'value' => 'video_demand_order_reward',
        'param' => [
            '昵称', '时间', '商品名称', '订单金额', '分红金额'
        ]
    ]);
    \Config::set('template.video_demand_order_reward_settle', [
        'title' => '讲师分红(讲师分红订单结算通知)',
        'subtitle' => '讲师分红订单结算通知',
        'value' => 'video_demand_order_reward_settle',
        'param' => [
            '昵称', '时间', '商品名称', '订单金额', '分红金额'
        ]
    ]);
    \Config::set('template.video_demand_lecturer_reward', [
        'title' => '讲师分红(会员打赏通知)',
        'subtitle' => '会员打赏通知',
        'value' => 'video_demand_lecturer_reward',
        'param' => [
            '昵称', '时间', '打赏金额'
        ]
    ]);
    /**
     * 支付回调
     *
     */
    $events->subscribe(Yunshop\VideoDemand\Listener\ChargeComplatedListener::class);

    /**
     * 订单收货
     * OrderReceiveListener
     *
     */
    $events->subscribe(Yunshop\VideoDemand\Listener\OrderReceiveListener::class);

    /*
 * 定时任务处理
 *
 */
    \Event::listen('cron.collectJobs', function () {
        \Cron::add('Video-demand', '*/10 * * * * *', function () {
            (new \Yunshop\VideoDemand\services\TimedTaskService)->handle();
            return;
        });
    });

};
