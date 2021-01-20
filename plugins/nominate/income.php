<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/24
 * Time: 10:47 AM
 */
\Config::set([
    'income_page.nominate_income' => [
        'class' => \Yunshop\Nominate\services\IncomePageService::class
    ]
]);

$set = \Setting::get('plugin.mryt_set');

\Config::set('income.nominate_prize', [
    'title' => $set['plugin_name']?:'推荐奖励',
    'type' => 'nominate_prize_withdraw',
    'class' => \Yunshop\Nominate\models\NominateBonus::class,
    'order_class' => '',
]);
\Config::set('income.nominate_team_manage_prize', [
    'title' => $set['nominate_prize_name']?:'团队业绩奖',
    'type' => 'nominate_team_manage_prize_withdraw',
    'class' => \Yunshop\Nominate\models\TeamPrize::class,
    'order_class' => '',
]);