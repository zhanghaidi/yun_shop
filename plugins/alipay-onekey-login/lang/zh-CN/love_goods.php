<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/28 上午9:13
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/


$love = \Yunshop\Love\Common\Services\SetService::getLoveName();

return [
    'title'                         => $love . '>>奖励设置',
    'subtitle'                      => '奖励设置',

    'love_name'                     => $love,

    'shopping-award_set'            => '购物奖励规则：',
    'activation_instructions'                              =>'公式：商品价格*单独设定比例=释放数量',

    'present'                       => '商品现价赠送',
    'actual'                        => '商品实际价赠送',

    'activation_love'               =>'加速激活'.$love,

    'love_accelerate'               =>'加速激活'.$love.'比例',

    'award_set'                     => '购物赠送设置：',

    'award_set_title'               => '购物赠送',
    'award_set_hint'                => '购物赠送比例',
    'award_set_introduce'           => '商品独立赠送比例权重高于统一赠送比例，商品独立赠送比例为0时走统一购物赠送比例',
    'award_off_hint'                => '亲~您还没有开启'.$love.'购物赠送哦~您可以前往'.$love.'设置处开启',
    'award_off_url'                 => '【点击前往'.$love.'设置】',

    'parent_award_set_title'                   => '购物上级赠送',
    'one_parent_award_set_hint'                => '购物上一级赠送比例',
    'one_parent_award_set_introduce'           => '商品独立赠送比例权重高于统一赠送比例，商品独立赠送比例为0时走统一上一级赠送比例',

    'two_parent_award_set_hint'                => '购物上二级赠送比例',
    'two_parent_award_set_introduce'           => '商品独立赠送比例权重高于统一赠送比例，商品独立赠送比例为0时走统一上二级赠送比例',

    'third_parent_award_set_hint'                => '购物上三级赠送比例',
    'third_parent_award_set_introduce'           => '商品独立设置权重高于统一设置，当比例为空或等于0，则使用固定规则。如果都为空或等于0则使用默认规则，关闭则不赠送',

    'parent_award_off_hint'                => '亲~您还没有开启'.$love.'购物上级赠送或分销商层级上级赠送哦~您可以前往'.$love.'设置处开启',
    'parent_award_off_url'                 => '【点击前往'.$love.'设置】',

    'off'                           => '关闭',
    'on'                            => '开启',

    'deduction_set'                 => '购物抵扣设置',

    'deduction_set_title'           => '购物抵扣',
    'deduction_set_hint'            => '商品最高抵扣比例',
    'deduction_set_introduce'       => '商品最高抵扣比例权重高于统一抵扣比例,商品独立抵扣比例为0时走统一抵扣比例',
    'deduction_off_hint'            => '亲~您还没有开启'.$love.'购物抵扣哦~您可以前往'.$love.'设置处开启',
    'deduction_off_url'             => '【点击前往'.$love.'设置】',

    'deduction_set'                 =>$love.'抵扣设置',
    'deduction_proportion_low'      =>$love.'最低抵扣比例',
    'deduction_proportion'          =>$love.'最高抵扣比例',

    'deduction'                     =>$love.'抵扣设置',
    'activation_off'            => '亲~您还没有开启'.$love.'加速激活哦~您可以前往'.$love.'设置处开启',
    'deduction_off'            => '亲~您还没有开启'.$love.'抵扣哦~您可以前往'.$love.'设置处开启',

    'submit'                        => '保存设置'
];