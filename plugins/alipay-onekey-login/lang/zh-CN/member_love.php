<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/28 下午5:36
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

$love = \Yunshop\Love\Common\Services\SetService::getLoveName();

return [
    'title'                         => $love . '管理',
    'subtitle'                      => $love . '管理',

    'search'                        => '筛选',

    'search_member'                 => '会员ID／会员姓名／呢称／手机号',
    'search_member_level'           => '会员等级',
    'search_member_group'           => '会员分组',
    'search_section'                => $love . '区间',
    'search_section_min'            => '最小',
    'search_section_max'            => '最大',

    'search_select'                 => '不限',

    'total'                         => '总人数',
    'usable_total'                 => '可用' . $love,
    'froze_total'                  => '冻结' . $love,


    'menu'      => [
        'menu_one'                  => '会员ID',
        'menu_two'                  => '粉丝',
        'menu_three'                => '姓名/手机号',
        'menu_four'                 => '会员等级',
        'menu_five'                 => '会员分组',
        'menu_six'                  => '当前' . $love,
        'menu_seven'                => '操作'
    ],

    'button'    => [
        'search'            => '搜索',
        'export'            => '导出 Excel',
        'operation'         => '查看详情'
    ]



];