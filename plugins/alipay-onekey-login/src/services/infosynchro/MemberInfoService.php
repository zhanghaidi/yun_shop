<?php

namespace Yunshop\AlipayOnekeyLogin\services\infosynchro;

use app\common\models\MemberShopInfo;
use app\common\models\Member;
use app\common\services\Session;
use Yunshop\AlipayOnekeyLogin\models\MemberSynchroLog;
use Yunshop\AlipayOnekeyLogin\models\MemberAlipay;

/**
* create 2018/6/18 
*/
class MemberInfoService
{
    //判断支付宝用户是否存在
    public function existMember($uid)
    {
        if (is_null(MemberAlipay::uniacid()->where('member_id', $uid)->first())) {
            return false;
        }
        return true;
    }

    //删除新绑定手机号的会员数据，保留旧的
	public function deleteMember($new_member)
	{
		//删除yz_member 表信息
		$bool = MemberShopInfo::where('member_id', $new_member->uid)->delete();
		//删除mc_members 表信息
        $status = Member::where('uid', $new_member->uid)->delete();

		return ($bool && $status);
	}


	public function setSession($member_id)
	{
		Session::set('member_id', $member_id);
	}

	//记录日志
	public function insertMergeLog($type, $old_member, $new_member, $status = 0)
	{
		$data = [
			'type' => $type,
			'old_member' => $old_member->uid,
			'new_member' => $new_member->uid,
			'status' => $status,
			'desc' => $type.'会员Id='.$old_member->uid.'同步会员Id='.$new_member->uid.'的信息',
		];

		MemberSynchroLog::create($data);
	}
}