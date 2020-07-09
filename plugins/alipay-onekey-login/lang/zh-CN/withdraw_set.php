<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/28 上午10:46
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

$love = \Yunshop\Love\Common\Services\SetService::getLoveName();

$credit1 = Setting::get('shop.shop')['credit1'] ?: "积分";

$integralName = "消费积分";
if (app('plugins')->isEnabled('integral')) {
    $integralName = \Yunshop\Integral\Common\Services\SetService::getIntegralName();
}


return [
    'pointName'                                   => $credit1,
    'integralName'                                => $integralName,
    'title'                                       => $love . '>>提现设置',
    'subtitle'                                    => '提现设置',
    'off'                                         => '关闭',
    'on'                                          => '开启',
    'submit'                                      => '保存设置',
    'withdraw_set'                                => '提现设置',
    'withdraw_set_title'                          => '开启提现到收入',
    'withdraw_multiple_hint'                      => '提现倍数限制',
    'withdraw_multiple_introduce'                 => '提现限制：提现倍数，为1、为空则不限制(不能小于1，允许两位小数',
    'withdraw_poundage_hint'                      => '提现到收入手续费',
    'withdraw_poundage_introduce'                 => '提现金额手续费：提现手续费统一在收入提现设置处设置，',
    'withdraw_scale_hint'                         => '提现到收入比例',
    'withdraw_scale_introduce'                    => '提现比例：' . $love . ' x 比例 = 转入值，为空则默认为1(允许两位小数)',
    'withdraw_in_consumption_integral'            => '提现到' . $integralName,
    'withdraw_in_consumption_integral_proportion' => '提现到' . $integralName . '比例',
    'withdraw_poundage_integral_hint'             => '手续费比例',
    'withdraw_handling_fee_deduction'             => '提现手续费扣除',
    'withdraw_amount_of_money'                    => '提现金额',
    'withdraw_scale_introduce_integral'           => '扣除手续费比例',
    'proportion_switch'                           => '提现扣除' . $credit1,
];



