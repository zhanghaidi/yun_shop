<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/30 上午9:36
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

$love = \Yunshop\Love\Common\Services\SetService::getLoveName();
return [
    'title'                         => $love . '明细',
    'subtitle'                      => $love . '明细',


    'search_member'                 => '昵称／姓名／手机号',
    'search_member_id'              => '会员ID',
    'search_member_level'           => '会员等级',
    'search_member_group'           => '会员分组',
    'search_source'                 => '业务类型',
    'search_type'                   => '收入／支出',
    'search_type_income'            => '收入',
    'search_type_expend'            => '支出',
    'search_relation'               => '订单号',
    'search_time_off'               => '不搜索时间',
    'search_time_on'                => '搜索时间',

    'total'                         => '总数',

    'menu'      => [
        'menu_one'                  => '时间',
        'menu_two'                  => '订单号',
        'menu_three'                => '粉丝',
        'menu_four'                 => '等级／分组',
        'menu_five'                 => '业务类型',
        'menu_six'                  => '收入／支出',
        'menu_seven'                => '剩余' . $love,
        'menu_eight'                => '变动类型'
    ],

    'button'    => [
        'search'            => '搜索',
        'export'            => '导出 Excel',
        'operation'         => '查看详情'
    ]



];