<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 下午4:22
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/


$love = \Yunshop\Love\Common\Services\SetService::getLoveName();

return [
    'title'                         => $love . '>>激活设置',
    'subtitle'                      => '激活设置',

    'accelerate'                    => '购买商品加速激活'.$love.'设置',
    'accelerate_state'              => $love.'加速激活状态设置',
    'on'                            =>'开启',
    'off'                           =>'关闭',
    'accelerate_proportion'         =>$love.'加速激活比例',
    'accelerated_description'       =>'加速激活规则：购买商品加速激活冻结'.$love.'比例，算法：商品实际支付金额*单独设定比例=释放数量',

    'submit'                        => '保存设置'
];