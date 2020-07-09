<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 下午3:32
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

$love = \Yunshop\Love\Common\Services\SetService::getLoveName();

return [
    'title'                         => $love . '>>消息设置',
    'subtitle'                      => '消息设置',

    'hint'                          => '消息通知使用任务处理通知，请前往【系统管理-商城设置-消息通知设置】填写任务处理通知',
    'hint_url'                      => '【点击前往】',


    'change_title'                  => $love . '变动通知：',
    'change_title_hint'             => $love . '变动通知',
    'change_title_introduce'        => '标题，默认为"' .$love. '变动通知"',
    'change_content_introduce'      => '模版变量：[昵称] [时间] [变动值类型] [变动数量] [业务类型] [当前剩余值]',

    'activation_title'                  => $love . '激活通知：',
    'activation_title_hint'             => $love . '激活通知',
    'activation_title_introduce'        => '标题，默认为"' .$love. '激活通知"',
    'activation_content_introduce'      => '模版变量：[昵称] [时间] [激活值] [固定比例激活值] [上周一级下线激活值] [上周二级三级会员下线激活值]',


    'submit'                        => '保存设置'

];
