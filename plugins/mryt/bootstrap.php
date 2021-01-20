<?php

use app\common\services\Hook;
use Illuminate\Contracts\Events\Dispatcher;

return function (Dispatcher $events) {
    $set = \Yunshop\Mryt\services\CommonService::getset();

    // 消息通知
    require_once 'message.php';

    \Config::set([
        'income_page.mryt_income' => [
            'class' => \Yunshop\Mryt\services\IncomePageService::class
        ]
    ]);

    \Config::set('income.mryt_referral', [
        'title' => $set['referral_name'],
        'type' => 'mryt_referral_withdraw',
        'class' => \Yunshop\Mryt\common\models\MemberReferralAward::class,
        'order_class' => '',
    ]);
    \Config::set('income.mryt_member_team', [
        'title' => $set['team_name'].'/' .$set['thanksgiving_name'],
        'type' => 'mryt_member_team_withdraw',
        'class' => \Yunshop\Mryt\common\models\MemberTeamAward::class,
        'order_class' => '',
    ]);
    \Config::set('income.mryt_parenting', [
        'title' => $set['parenting_name'],
        'type' => 'mryt_parenting_withdraw',
        'class' => \Yunshop\Mryt\common\models\OrderParentingAward::class,
        'order_class' => '',
    ]);
    \Config::set('income.mryt_order_team', [
        'title' => $set['teammanage_name'],
        'type' => 'mryt_order_team_withdraw',
        'class' => \Yunshop\Mryt\common\models\OrderTeamAward::class,
        'order_class' => '',
    ]);
    \Config::set('income.mryt_tier', [
        'title' => $set['tier_name'],
        'type' => 'mryt_tier_withdraw',
        'class' => \Yunshop\Mryt\common\models\TierAward::class,
        'order_class' => '',
    ]);

    \Config::set('plugins_menu.mryt',[
        'name' => $set['name'],
        'type' => 'dividend',
        'url' => 'plugin.mryt.admin.set.index',// url 可以填写http 也可以直接写路由
        'urlParams' => '',//如果是url填写的是路由则启用参数否则不启用
        'permit' => 1,//如果不设置则不会做权限检测
        'menu' => 1,//如果不设置则不显示菜单，子菜单也将不显示
        'icon' => 'fa-credit-card',//菜单图标
        'top_show'    => 0,
        'left_first_show'   => 0,
        'left_second_show'   => 1,
        'list_icon' => 'mryt',
        'parents'=>[],
        'child' => [
            'mryt.set' => [
                'name' => '基础设置',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.mryt.admin.set.index',
                'urlParams' => [],
                'parents'=>['mryt'],
                'child' => []
            ],
            'mryt.level' => [
                'name' => '等级管理',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.mryt.admin.level.index',
                'urlParams' => [],
                'parents'=>['mryt'],
                'child' => [
                    'mryt.leveladd' => [
                        'name' => '添加等级',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => '',
                        'url' => 'plugin.mryt.admin.level.add',
                        'urlParams' => [],
                        'parents'=>['mryt', 'mryt.level'],
                        'child' => [

                        ]
                    ],
                    'mryt.leveledit' => [
                        'name' => '编辑等级',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => '',
                        'url' => 'plugin.mryt.admin.level.edit',
                        'urlParams' => [],
                        'parents'=>['mryt', 'mryt.level'],
                        'child' => [

                        ]
                    ],
                ]
            ],
            'mryt.member' => [
                'name' => '会员管理',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.mryt.admin.member.index',
                'urlParams' => [],
                'parents'=>['mryt'],
                'child' => [
                    'mryt.memberadd' => [
                        'name' => '添加会员',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => '',
                        'url' => 'plugin.mryt.admin.member.add',
                        'urlParams' => [],
                        'parents'=>['mryt', 'mryt.member'],
                        'child' => [

                        ]
                    ],
                    'mryt.member_username' => [
                        'name' => '账户信息',
                        'permit' => 1,
                        'menu' => 1,
                        'icon' => '',
                        'url' => 'plugin.mryt.admin.member.edit-password',
                        'urlParams' => [],
                        'parents'=>['mryt', 'mryt.member'],
                        'child' => [

                        ]
                    ],
                ]
            ],
            'mryt.plush' => [
                'name' => $set['referral_name'].'记录',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.mryt.admin.plush.index',
                'urlParams' => [],
                'parents'=>['mryt'],
                'child' => []
            ],
            'mryt.teammange' => [
                'name' => $set['teammanage_name'].'记录',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.mryt.admin.teammanage.index',
                'urlParams' => [],
                'parents'=>['mryt'],
                'child' => []
            ],
            'mryt.team' => [
                'name' => $set['team_name'].'/' .$set['thanksgiving_name'].'记录',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.mryt.admin.team.index',
                'urlParams' => [],
                'parents'=>['mryt'],
                'child' => []
            ],
            'mryt.train' => [
                'name' => $set['parenting_name'].'记录',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.mryt.admin.train.index',
                'urlParams' => [],
                'parents'=>['mryt'],
                'child' => []
            ],
            'mryt.tier' => [
                'name' => $set['tier_name'].'记录',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.mryt.admin.tier.index',
                'urlParams' => [],
                'parents'=>['mryt'],
                'child' => []
            ],
            'mryt.teamnew' => [
                'name' => '团队新进人员统计',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.mryt.admin.teamnew.index',
                'urlParams' => [],
                'parents'=>['mryt'],
                'child' => []
            ],
            'mryt.log' => [
                'name' => '记录',
                'permit' => 1,
                'menu' => 1,
                'icon' => '',
                'url' => 'plugin.mryt.admin.log.index',
                'urlParams' => [],
                'parents'=>['mryt'],
                'child' => []
            ],
        ]
    ]);

    if (env('APP_Framework') != 'platform') {
        if (YunShop::isWeb()) {
            require_once 'menu.php';
        }
    }

    \Config::set('template.mryt_award', [
        'title' => "奖励通知",
        'subtitle' => '奖励通知',
        'value' => 'mryt_award',
        'param' => [
            '会员昵称', '时间', '团队管理奖金额', '团队奖金额', '感恩奖金额', '育人奖金额'
        ]
    ]);

    \Event::listen('cron.collectJobs', function () {
        \Cron::add('VipAddUp', '* * 1 * * *', function () {
            (new \Yunshop\Mryt\services\TimedTaskService)->handle();
            return;
        });
    });

    \Event::listen('cron.collectJobs', function () {
        \Cron::add('AutoWithdraw', '*/10 * * * * *', function () {
            (new \Yunshop\Mryt\services\AutoWithdrawService())->handle();
            return;
        });
    });

    $events->subscribe(Yunshop\Mryt\listeners\MemberRelationEventListener::class);
    $events->subscribe(Yunshop\Mryt\listeners\OrderCreatedListener::class);
};
