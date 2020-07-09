<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/26 下午3:40
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;


//use Yunshop\Love\Common\Services\SetService;

use Yunshop\Love\Common\Models\Member;

class CommonService
{
    /**
     * 回去爱心值插件名称（自定义名称）
     * @return mixed|string
     */
    public static function getLoveName()
    {
        return SetService::getLoveName();
    }


    /**
     * 获取会员主信息
     * @param $memberId
     * @return mixed
     */
    public static function getMemberModel($memberId)
    {
        return Member::ofUid($memberId)->first();
    }

    /**
     * 获取爱心值会员及爱心值会员爱心值，（可拼接其他会员信息）
     * @param $memberId
     * @return mixed
     */
    public static function getLoveMemberModelById($memberId)
    {
        return Member::ofUid($memberId)->withLove()->first();
    }

    /**
     * 获取爱心值会员当前可用爱心值
     * @param $memberId
     * @return mixed
     */
    public static function getMemberUsableLove($memberId)
    {
        $memberModel = static::getLoveMemberModelById($memberId);
        return isset($memberModel->love->usable) ? $memberModel->love->usable : '0';
    }

    /**
     * 获取爱心值会员当前冻结爱心值
     * @param $memberId
     * @return mixed
     */
    public static function getMemberFrozeLove($memberId)
    {
        $memberModel = static::getLoveMemberModelById($memberId);
        return isset($memberModel->love->froze) ? $memberModel->love->froze : '0';
    }

    /**
     * 获取爱心值变动通知通知人 openid
     * @return mixed
     */
    public static function getNoticeMember($memberId)
    {
        return Member::getMemberByUid($memberId)->with('hasOneFans')->first();
    }

}
