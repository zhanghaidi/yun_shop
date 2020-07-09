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
    'title'                         => $love . '>>奖励设置',
    'subtitle'                      => '奖励设置',

    'award_type'                    => '奖励类型',
    'award_type_title'              => '奖励类型',
    'award_type_usable'             => '奖励可用',
    'award_type_froze'              => '奖励冻结',
    'award_type_introduce'          => '注意：此页面所有奖励都受此处选择类型控制',

    'shopping-award_set'            => '购物奖励规则：',

    'present'                       => '商品现价',
    'actual'                        => '商品实际支付金额',
    'cost'                          =>'商品成本价',
    'profit'                        => '定制',
    'profit_reward_proportion_title'      => '定制赠送百分比',
    'profit_reward_proportion_explain'    => '[(商品现价-成本)-(商品现价*定制赠送百分比)]*'.$love.'购物赠送比例',
    'actual_order'                  =>'订单金额',
    'award_rule'                    =>$love .'奖励规则,此规则应用，购物赠送，购物上级赠送',


    'award_set'                     => '奖励设置：',

    'award_set_title'               => '购物赠送',
    'award_set_hint'                => '购物赠送比例',
    'award_set_introduce'           => '购物赠送' . $love . '比例，商品独立的赠送比例需要在【商品编辑--' . $love . '】中单独设置',

    'withdraw_award_title'               => '提现赠送',
    'withdraw_award_introduce'           => '收入提现：收入提现奖励提现手续费等值可用'. $love . "【比例 1：1】",

    'parent_award_set_title'        => '购物上级赠送',
    'one_parent_award_set_hint'         => '一级赠送比例',
    'one_parent_award_set_introduce'    => '购物上一级赠送' . $love . '比例，商品独立的赠送比例需要在【商品编辑--' . $love . '】中单独设置',

    'two_parent_award_set_hint'         => '二级赠送比例',
    'two_parent_award_set_introduce'    => '购物上二级赠送' . $love . '比例，商品独立的赠送比例需要在【商品编辑--' . $love . '】中单独设置',

    'third_parent_award_set_hint'         => '三级赠送比例',
    'third_parent_award_set_introduce'    => '购物上三级赠送' . $love . '比例，商品独立的赠送比例需要在【商品编辑--' . $love . '】中单独设置',

    'commission_level_superior_give'      => '分销商层级上级赠送',
    'level_name'                          => '等级名称',
    'first_level_commission'              => '一级赠送比例',
    'second_level_commission'             => '二级赠送比例',
    'third_level_commission'              => '三级赠送比例',
    'default_level'                       => '默认等级',

    'commission_award_set'          => '分销奖励设置：',
    'commission_award_set_title'    => '分销下线赠送：',
    'commission_award_proportion_title'    => '分销下线赠送：',
    'commission_award_set_introduce'=> '每天每个分销商直接下线可获得的金额',
    'commission_award_times'        => '分销下线获得时间：',
    'commission_every_day'          => '每天',


    'off'                           => '关闭',
    'on'                            => '开启',


    'withdraw_poundage'             => '提现手续费',
    'withdraw_poundage_introduce'   => '可前往财务管理->提现设置->押金提现修改,',
    'withdraw_poundage_url'         => '【点我去修改】',


    'submit'                        => '保存设置',



];