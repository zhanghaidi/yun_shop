<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/15
 * Time: 3:40 PM
 */

$set = \Setting::get('plugin.nominate');

\Config::set('plugins_menu.nominate', [
    'name'              => $set['plugin_name']?:'推荐奖励',
    'type'              => 'marketing',
    'url'               => 'plugin.nominate.admin.set.index',
    'url_params'        => '',
    'permit'            => 1,
    'menu'              => 1,
    'top_show'          => 0,
    'left_first_show'   => 0,
    'left_second_show'  => 1,
    'icon'              => 'fa-retweet',
    'list_icon'         => 'full_return',
    'item'              => 'full_return',
    'parents'           => [],
    'child'             => [
        'nominate_set' => [
            'name'              => '基础设置',
            'url'               => 'plugin.nominate.admin.set.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'icon'              => '',
            'item'              => 'nominate_set',
            'parents'           => ['nominate'],
            'child'             => []
        ],
        'nominate_level_list' => [
            'name'              => '会员等级',
            'url'               => 'plugin.nominate.admin.level.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'icon'              => '',
            'item'              => 'nominate_level_list',
            'parents'           => ['nominate'],
            'child'             => [
                'nominate_level_detail' => [
                    'name' => '等级详情',
                    'url' => 'plugin.nominate.admin.level.detail',
                    'url_params' => '',
                    'permit' => 1,
                    'menu' => 0,
                    'icon' => '',
                    'item' => 'nominate_level_detail',
                    'parents' => ['nominate', 'nominate_level_list'],
                    'child' => []
                ],
            ]
        ],
        'nominate_member' => [
            'name'              => '会员管理',
            'url'               => 'plugin.nominate.admin.member.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'icon'              => '',
            'item'              => 'nominate_member',
            'parents'           => ['nominate'],
            'child'             => []
        ],
        'nominate_prize' => [
            'name'              => $set['nominate_prize_name']?:'直推奖',
            'url'               => 'plugin.nominate.admin.prize.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'icon'              => '',
            'item'              => 'nominate_prize',
            'parents'           => ['nominate'],
            'child'             => []
        ],
        'nominate_poor_prize' => [
            'name'              => $set['nominate_poor_prize_name']?:'直推极差奖',
            'url'               => 'plugin.nominate.admin.poor-prize.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'icon'              => '',
            'item'              => 'nominate_poor_prize',
            'parents'           => ['nominate'],
            'child'             => []
        ],
        'nominate_team_prize' => [
            'name'              => $set['team_prize_name']?:'团队奖',
            'url'               => 'plugin.nominate.admin.team-prize.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'icon'              => '',
            'item'              => 'nominate_team_prize',
            'parents'           => ['nominate'],
            'child'             => []
        ],
        'nominate_team_manage_prize' => [
            'name'              => $set['team_manage_prize_name']?:'团队业绩奖',
            'url'               => 'plugin.nominate.admin.team-manage-prize.index',
            'url_params'        => '',
            'permit'            => 1,
            'menu'              => 1,
            'icon'              => '',
            'item'              => 'nominate_team_manage_prize',
            'parents'           => ['nominate'],
            'child'             => []
        ],
    ]
]);

\Config::set('observer.goods.nominate', [
    'class' => \Yunshop\Nominate\models\NominateGoods::class,
    'function_validator' => 'relationValidator',
    'function_save' => 'relationSave'
]);

\Config::set('widget.goods.tab_nominate', [
    'title' => $set['plugin_name']?:'推荐奖励',
    'class' => \Yunshop\Nominate\widget\NominateWidget::class
]);