<?php

namespace Yunshop\AlipayOnekeyLogin\services;

use Yunshop\AlipayOnekeyLogin\services\infosynchro\AlipayUserService;
use Yunshop\AlipayOnekeyLogin\services\infosynchro\MinUserService;
use Yunshop\AlipayOnekeyLogin\services\infosynchro\WeChatUserService;
use Yunshop\AlipayOnekeyLogin\services\infosynchro\YdbAppUserService;

/**
 *
 * todo 2019/07/03 以前写的是只有不是相同的平台登录都是可以相互同步会员，现在修改为其他平台只能同步支付宝会员，相同平台还是不能同步
 *
 * Class SynchronousUserInfo
 * @package Yunshop\AlipayOnekeyLogin\services
 */
class SynchronousUserInfo
{
	const LOGIN_OFFICE_ACCOUNT = 1;
    const LOGIN_MINI_APP = 2;
    const LOGIN_APP_YDB = 7;
    const LOGIN_ALIPAY = 8;

    public static function create($type = null)
    {
        switch($type)
        {
            //公众号
            case self::LOGIN_OFFICE_ACCOUNT:
                $className = new WeChatUserService();
                break;
            //微信app 
            case self::LOGIN_APP_YDB:
                $className = new YdbAppUserService();
                break;
            //小程序
            case self::LOGIN_MINI_APP:
                $className = new MinUserService();
                break;
            //支付宝
            case self::LOGIN_ALIPAY:
                $className = new AlipayUserService();
                break;
            default:
                $className = null;
        }
        return $className;
    }
}