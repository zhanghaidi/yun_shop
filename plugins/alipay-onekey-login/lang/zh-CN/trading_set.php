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
    'title'                         => $love . '>>交易设置',
    'subtitle'                      => '交易设置',

    'trading_set_title'             => $love.'交易',
    'off'                           => '关闭',
    'on'                            => '开启',
    
    'trading_limit_title'           => '交易限制（最小额度）',
    'trading_limit_introduce'       => '交易' . $love . '最小额度',

    'trading_fold_title'            => '交易限制（倍数）',
    'trading_fold_introduce'        => '交易' . $love . '的倍数',
    'trading_fold_unit'             => '倍数',

    'poundage_title'            => '交易手续费',
    'poundage_introduce'        => '交易' . $love . '平台收取手续费',
    'poundage_unit'             => '%',

    'trading_money_title'            => '交易比例',
    'trading_money_hint'            => '一个'. $love .'等于',
    'trading_money_introduce'        => '交易' . $love . '比例设置',
    'trading_money_unit'             => '元',

    'recycl_title'            => '公司代购时间',
    'recycl_introduce'        => '交易' . $love . '后，超过多少小时公司代购',
    'recycl_unit'             => '小时',

    'submit'                        => '保存设置',



];