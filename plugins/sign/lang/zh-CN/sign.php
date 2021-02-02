<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/5 下午1:48
 * Email: livsyitian@163.com
 */

$sign = \Yunshop\Sign\Common\Services\SetService::getSignSet('sign_name') ?:'签到';
$love_name = \Setting::get('love.name');

return [
    'plugin_name' => $sign,


//签到插件 菜单
    'sign_records'              => $sign . '记录',
    'sign_set'                  => $sign . '设置',


    'off'                       => '关闭',
    'on'                        => '开启',
    'choose_link'               => '选择连接',
    //'choose_img'                => '选择图片',  //选择图片按钮在 ImageHelper.php 中
    'choose_notice_template'    => '请选择消息模板',
    'choose_coupon'             => '选择优惠券',

    'point'                     => '积分',
    'coupon'                    => '优惠券',
    'button_submit'             => '保存设置',



//基础设置
    'base_set_title'            => $sign . '设置',
    'sign_on_off'               => $sign . '开启',
    'custom_name'               => '自定义名称',
    'custom_name_hint'          => '请输入自定义名称',
    'custom_name_introduce'     => $sign . '插件自定义名称，为空，默认为《签到》',
    'award_set'                 => '奖励设置',
    'success_link'              => '推送连接',
    'success_link_hint'         => '请填写或选择指向的连接 (请以https://开头)',
    'success_link_introduce'    => $sign . '成功跳转制定连接页面',

    'every_award'               => '日常奖励',
    'point_award'               => '积分奖励',
    'point_unit'                => '积分',
    'point_award_introduce'     => '每日' . $sign . '奖励积分值（允许两位小数）',

    'coupon_award'              => '优惠券奖励',
    'coupon_unit'               => '张',
    'coupon_award_introduce'    => '每日' . $sign . '奖励优惠券',

    // 'love_award'                => '爱心值奖励',
    'love_award'                => $love_name.'奖励',
    'to'                        => '至',
    // 'love'                      => '爱心值',
    'love'                      => $love_name ?: '爱心值',
    // 'love_award_introduce'    => '每日' . $sign . '奖励爱心值',
    'love_award_introduce'    => '每日' . $sign . '奖励'.$love_name,

    'cumulative_award'          => '连签奖励',
    'cumulative_sign'           => '连续' . $sign,

    'add_cumulative_award'      => '添加连签奖励规则',






//分享设置
    'share_set_title'           => '分享设置',
    'share_title'               => '分享标题',
    'share_title_introduce'     => '不填写默认商城名称',
    'share_img'                 => '分享图片',
    'share_describe'            => '分享描述',

//签到规则
    'explain_set_title'         => $sign . '规则',
    'explain_content'           => $sign . '规则',


//通知设置
    'notice_set_title'          => '通知设置',
    'sign_notice'               => $sign . '通知',
    'sign_remind_template'      => '每日签到提醒模板消息',
    'sign_remind_wechat'        => '公众号提醒',
    'sign_remind_minapp'        => '小程序提醒',


//权限操作
    'see_set'                   => '查看设置',
    'update_set'                => '修改设置',
    'see_records'               => '浏览记录',
    'export_records'            => '导出EXCEL',
    'see_detail'                => '查看详情',



//签到记录
    'filter'                    => '筛选',
    'member_id'                 => '会员ID',
    'member_info'               => '会员昵称/姓名/手机号',
    'member_level'              => '会员等级',
    'member_group'              => '会员分组',
    'search_time_on'            => '搜索时间',
    'search_time_off'           => '不搜索时间',
    'button_search'             => '搜索',
    'button_export'             => '导出 EXCEL',
    'button_detail'             => '详情',

    'sign_column_one'           => '会员ID',
    'sign_column_two'           => '会员',
    'sign_column_three'         => '姓名/手机号',
    'sign_column_four'          => '最新' . $sign . '时间',
    'sign_column_five'          => '今日' . $sign . '状态',
    'sign_column_six'           => '连续' . $sign . '状态',
    'sign_column_seven'         => '累计奖励',
    'sign_column_eight'         => '操作',

    'sign_unit'                 => '天',
    'sign_unit_hint'            => '暂无',


//签到详情
    'record_detail'             => $sign . '详情',






];

