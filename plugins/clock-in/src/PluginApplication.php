<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\ClockIn;

use app\common\models\MemberCart;
use Config;
use YunShop;
use Yunshop\AreaDividend\Listener\OrderCreatedListener;
use Yunshop\AreaDividend\Listener\OrderFailureListener;
use Yunshop\AreaDividend\Listener\OrderReceiveListener;
use Yunshop\AreaDividend\services\TimedTaskService;
use Yunshop\ClockIn\jobs\addClockInRewardJob;
use Yunshop\ClockIn\Listener\ChargeComplatedListener;
use Yunshop\ClockIn\services\ClockInService;
use Yunshop\Supplier\common\modules\order\OrderManager;

class PluginApplication extends \app\common\services\PluginApplication
{
    public function getIncomePageItems()
    {
        return ['clockIn' => ['class' => 'Yunshop\ClockIn\services\IncomePageService']];
    }

    protected function setConfig()
    {

    }

    protected function setMenuConfig()
    {
        $clockInService = new ClockInService();

        \app\backend\modules\menu\Menu::current()->setPluginMenu('clock_in', [
            'name' => $clockInService->get('plugin_name'),
            'type' => 'marketing',
            'url' => 'plugin.clock-in.admin.clock-in-set.index',// url 可以填写http 也可以直接写路由
            'url_params' => '',//如果是url填写的是路由则启用参数否则不启用
            'permit' => 1,//如果不设置则不会做权限检测
            'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
            'top_show' => 0,
            'left_first_show' => 0,
            'left_second_show' => 1,
            'icon' => '',//菜单图标
            'list_icon' => 'clock_in',
            'parents' => [],
            'child' => [
                'clock_in_set' => [
                    'name' => $clockInService->get('plugin_name') . '设置',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.clock-in.admin.clock-in-set.index',
                    'url_params' => '',
                    'parents' => ['clock_in'],
                    'child' => [
                        'set.index' => [
                            'name' => $clockInService->get('plugin_name') . '设置',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.clock-in.admin.clock-in-set.index',
                            'parents' => ['clock_in', 'fclock_in_set'],
                        ],
                    ]
                ],
                'clock_queue' => [
                    'name' => $clockInService->get('plugin_name') . '队列',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.clock-in.admin.clock-in-queue.index',
                    'url_params' => '',
                    'parents' => ['clock_in'],
                    'child' => [
                        'clock_queue_index' => [
                            'name' => '查看' . $clockInService->get('plugin_name') . '队列',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.clock-in.admin.clock-in-queue.index',
                            'parents' => ['clock_in', 'clock_queue'],
                        ],
                        'clock_queue_export' => [
                            'name' => $clockInService->get('plugin_name') . '队列导出',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.clock-in.admin.clock-in-queue.export',
                            'parents' => ['clock_in', 'clock_queue'],
                        ],
                    ]
                ],
                'clock_pay_log' => [
                    'name' => $clockInService->get('plugin_name') . '支付记录',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.clock-in.admin.clock-in-pay-log.index',
                    'url_params' => '',
                    'parents' => ['clock_in'],
                    'child' => [
                        'clock_pay_log_index' => [
                            'name' => '查看' . $clockInService->get('plugin_name') . '支付记录',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.clock-in.admin.clock-in-pay-log.index',
                            'parents' => ['clock_in', 'clock_pay_log'],
                        ],
                        'clock_pay_log_export' => [
                            'name' => $clockInService->get('plugin_name') . '支付记录导出',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.clock-in.admin.clock-in-pay-log.export',
                            'parents' => ['clock_in', 'clock_pay_log'],
                        ],
                    ]
                ],
                'clock_reward_log' => [
                    'name' => $clockInService->get('plugin_name') . '奖励记录',
                    'permit' => 1,
                    'menu' => 1,
                    'icon' => '',
                    'url' => 'plugin.clock-in.admin.clock-in-reward-log.index',
                    'url_params' => '',
                    'parents' => ['clock_in'],
                    'child' => [
                        'clock_reward_log_index' => [
                            'name' => '查看' . $clockInService->get('plugin_name') . '奖励记录',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.clock-in.admin.clock-in-reward-log.index',
                            'parents' => ['clock_in', 'clock_reward_log'],
                        ],
                        'clock_reward_log_export' => [
                            'name' => $clockInService->get('plugin_name') . '奖励记录导出',
                            'permit' => 1,
                            'menu' => 0,
                            'url' => 'plugin.clock-in.admin.clock-in-reward-log.export',
                            'parents' => ['clock_in', 'clock_reward_log'],
                        ],
                    ]
                ]

            ]
        ]);
    }

    public function getIncomeItems()
    {
        $clockInService = new ClockInService();

        return ['clockIn' => [
            'title' => $clockInService->get('plugin_name'),
            'type' => 'clockIn',
            'class' => 'Yunshop\ClockIn\models\ClockRewardLogModel',
            'order_class' => '',
        ]];
    }

    public function getWidgetItems()
    {
        $clockInService = new ClockInService();

        return ['withdraw.tab_clock_in' => [
            'title' => $clockInService->get('plugin_name') . '收入提现',
            'class' => 'Yunshop\ClockIn\widgets\ClockInWithdrawWidget',
        ]];
    }

    public function boot()
    {
        $clockInService = new ClockInService();


        //插件
        \app\common\modules\shop\ShopConfig::current()->set('plugin.clockIn', [
            'title' => $clockInService->get('plugin_name'),
            'ico' => 'icon-daka01',
            'type' => 'clockIn',
            'class' => 'Yunshop\ClockIn\models\ClockRewardLogModel',
            'order_class' => '',
            'agent_class' => '',
            'agent_name' => '',
            'agent_status' => '',
        ]); 

        $events = app('events');


        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Clock-in', '*/10 * * * *', function () {
                (new \Yunshop\ClockIn\services\TimedTaskRewardService)->handle();
                return;
            });
        });

        /**
         * 支付回调
         */

        $events->subscribe('Yunshop\ClockIn\Listener\ChargeCompletedListener');
    }
}