<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 下午2:09
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

$love = \Yunshop\Love\Common\Services\SetService::getLoveName();

return [
    'title'                         => $love . '>>基础设置',
    'subtitle'                      => '基础设置',

    'name_set'                      => $love . '名称设置：',
    'name_set_subtitle'             => '自定义名称',
    'name_set_hint'                 => '请输入' . $love . '名称',
    'name_set_introduce'            => '空白默认为爱心值',


    'off'                           => '关闭',
    'on'                            => '开启',
    'team_dividend_on'              => '经销商开启',


    'goods_detail_show'             => '商品详情页设置',
    'goods_detail_show_subtitle'    => '开启显示' . $love,

    'order_deduction'                 => '订单'.$love.'抵扣返还',
    'order_deduction_love'        => '抵扣返还',


    'transfer_set'                  => '转让设置',
    'transfer_set_subtitle'         => $love .'转让',

    'team_dividend_transfer'        => '经销团队转让',
    'team_dividend_transfer_range'  => '经销商团队上级转给下级，同等级不能进行转移',

    'transfer_poundage'             => '转让手续费',
    'transfer_poundage_introduce'   => '转让扣除手续费比例(允许两位小数)',
    'transfer_fetter'               => '转让最小额度',
    'transfer_fetter_introduce'     => '转让限制：最小额度，为0、为空则不限制(允许两位小数)',
    'transfer_multiple'             => '转让倍数',
    'transfer_multiple_introduce'   => '转让限制：转让倍数，为1、为空则不限制(不能小于1，允许两位小数)',


    'deduction_set'                 => '购物抵扣',
    'deduction_set_subtitle'        => '购物抵扣',
    'deduction_freight_title'       => '抵扣运费',

    'deduction_proportion_low'          => '商品最低抵扣比例',
    'deduction_proportion'          => '商品最高抵扣比例',
    'deduction_proportion_introduce'=> '商品最低抵扣比例-商品最高抵扣比例，商品独立的比例需要在【商品编辑--' . $love . '】中单独设置。',

    'deduction_exchange'            => '抵扣兑换比例',
    'deduction_exchange_introduce'  => '抵扣兑换比例：实际允许抵扣10元，抵扣兑换比例10%，则需要10／10%'.$love. ',为空、为0则默认1：1。',



    'explain_set'                   => $love . '说明',
    'explain_title'                 => '标题',
    'explain_content'               => '内容',


    'display_style'                 =>'显示样式',
    'daily_bargaining'              =>'天天兑价',
    'shopping_gift'                 =>'购物赠送',




    'submit'                        => '保存设置'


];