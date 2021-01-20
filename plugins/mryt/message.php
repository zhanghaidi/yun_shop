<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/28
 * Time: 4:02 PM
 */
$plugin_name = $set['name'] ?: 'MRYT';

\Config::set('template.mryt_upgrate_message', [
    'title' => $plugin_name.'(升级通知)',
    'subtitle' => '升级通知',
    'value' => 'mryt_upgrate_message',
    'param' => [
        '插件名称','会员昵称', '时间', '旧等级', '新等级', '旧等级提成比例', '新等级提成比例','旧等级直推奖','新等级直推奖','旧等级团队奖','新等级团队奖','旧等级感恩奖','新等级感恩奖','旧等级育人奖比例','新等级育人奖比例','旧等级平级奖','新等级平级奖'
    ]
]);
\Config::set('template.mryt_award_message', [
    'title' => $plugin_name.'(奖励通知)',
    'subtitle' => '奖励通知',
    'value' => 'mryt_award_message',
    'param' => [
        '插件名称','昵称', '时间', '类型-金额'
    ]
]);
\Config::set('notice-template.mryt_upgrate_message', [
    'template_id_short' => 'OPENTM207574677',
    'title' => "[插件名称]会员升级通知",
    'first_color' => '#000000',
    'remark_color' => '#000000',
    'first' => '恭喜您升级啦',
    'data' => [
        0 => [
            "keywords" => "keyword1",
            "value" => "[插件名称]会员等级",
            "color" => "#000000",
        ],
        1 => [
            "keywords" => "keyword2",
            "value" => "[插件名称]会员等级升级通知",
            "color" => "#000000",
        ],
        2 => [
            "keywords" => "keyword3",
            "value" => "尊敬的[会员昵称]，恭喜您于[时间]由[旧等级]升级为[新等级]，团队管理奖比例由[旧等级提成比例]%升级为[新等级提成比例]%。直推奖由[旧等级直推奖]元升级为[新等级直推奖]元，团队奖由[旧等级团队奖]元升级为[新等级团队奖]元，感恩奖由[旧等级感恩奖]元升级为[新等级感恩奖]元，育人奖比例由[旧等级育人奖比例]%升级为[新等级育人奖比例]%,平级奖由[旧等级平级奖]元升级为[新等级平级奖]元。",
            "color" => "#000000",
        ],
    ],
    'remark' => '请您再接再厉，再创辉煌！'
]);
\Config::set('notice-template.mryt_award_message', [
    'template_id_short' => 'OPENTM207574677',
    'title' => '团队奖励通知',
    'first_color' => '#000000',
    'remark_color' => '#000000',
    'first' => '团队奖励通知！',
    'data' => [
        0 => [
            "keywords" => "keyword1",
            "value" => "奖励",
            "color" => "#000000",
        ],
        1 => [
            "keywords" => "keyword2",
            "value" => "团队奖励通知",
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


