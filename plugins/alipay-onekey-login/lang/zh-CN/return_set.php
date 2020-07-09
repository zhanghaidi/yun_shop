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
    'title'                         => $love . '>>返现设置',
    'subtitle'                      => '返现设置',

    'return_set_title'             => $love.'返现',
    'off'                           => '关闭',
    'on'                            => '开启',
    
    'return_rate_title'           => '返现比例',
    'return_rate_introduce'       => '每天按照' . $love . '数值*百分比定时返现到收入',

    'return_time_title'            => '返现时间',
    'return_time_introduce'        => $love . '返现时间',



    'submit'                        => '保存设置',



];