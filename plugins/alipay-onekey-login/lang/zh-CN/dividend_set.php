<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 下午3:04
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/


$love = \Yunshop\Love\Common\Services\SetService::getLoveName();

return [
    'title'                         => $love . '>>分红统计设置',
    'subtitle'                      => '分红统计设置',

    'dividend_set_title'             => $love.'分红统计',
    'off'                           => '关闭',
    'on'                            => '开启',
    
    'dividend_rate_title'           => '分红比例',
    'dividend_rate_introduce'       => '每天按照平台营业额数值*百分比定时统计数据',

    'dividend_time_title'           => '分红统计天数',
    'dividend_time_introduce'       => '分红统计间隔周期',

    'submit'                        => '保存设置',
];