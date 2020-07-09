<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/11 上午11:20
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;



use Yunshop\Love\Common\Models\Member;
use Yunshop\Love\Common\Models\MemberLove;

class MemberService
{
    public static function getUsableLoveMember()
    {
        return MemberLove::select('member_id')->where('usable','>','0')->get();
    }

    public static function getFrozeLoveMember()
    {
        return MemberLove::select('member_id')->where('froze','>','0')->get();
    }

    public static function getLoveMemberModel($memberId)
    {
        return Member::ofUid($memberId)->withLove()->first();
    }

}
