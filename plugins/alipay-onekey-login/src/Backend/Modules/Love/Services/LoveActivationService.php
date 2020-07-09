<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/17 下午2:40
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Modules\Love\Services;


class LoveActivationService
{
    public static function activationTime()
    {
        return [
            '0'        => '关闭激活',
            '1'        => '每周一 1：00',
            '2'        => '每周二 1：00',
            '3'        => '每周三 1：00',
            '4'        => '每周四 1：00',
            '5'        => '每周五 1：00',
            '6'        => '每周六 1：00',
            '7'        => '每周日 1：00',
        ];
    }
}
