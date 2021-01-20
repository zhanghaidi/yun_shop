<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/16
 * Time: 4:33 PM
 */

$set = \Setting::get('plugin.nominate');
$plugin_name = $set['plugin_name'];

\Config::set('template.nominate_award_message', [
    'title' => $plugin_name.'(奖励通知)',
    'subtitle' => '奖励通知',
    'value' => 'nominate_award_message',
    'param' => [
        '插件名称', '昵称', '时间', '类型-金额'
    ]
]);

\Config::set('notice-template.nominate_award_message', [
    'template_id_short' => 'OPENTM207574677',
    'title' => '奖励通知',
    'first_color' => '#000000',
    'remark_color' => '#000000',
    'first' => '奖励通知！',
    'data' => [
        0 => [
            "keywords" => "keyword1",
            "value" => "奖励",
            "color" => "#000000",
        ],
        1 => [
            "keywords" => "keyword2",
            "value" => "奖励通知",
            "color" => "#000000",
        ],
        2 => [
            "keywords" => "keyword3",
            "value" => "尊敬的[昵称]，您于[时间]获得一笔[插件名称]会员奖励，获得[类型-金额]元",
            "color" => "#000000",
        ],
    ],
    'remark' => '感谢您的支持！'
]);