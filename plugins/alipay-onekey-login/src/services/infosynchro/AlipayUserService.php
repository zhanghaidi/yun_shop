<?php

namespace Yunshop\AlipayOnekeyLogin\services\infosynchro;

use app\frontend\modules\member\models\MemberUniqueModel;
use Illuminate\Support\Facades\DB;
use Yunshop\AlipayOnekeyLogin\models\MemberAlipay;
use Exception;
/**
* create 2018/6/18 
*/
class AlipayUserService extends MemberInfoService
{
    protected $wechat_sign = 'alipay';

	public function updateMember($old_member, $new_member)
	{

		$alipay_member = MemberAlipay::uniacid()->where('member_id', $new_member->uid)->first();

        if (empty($alipay_member)) {
            \Log::debug('支付宝同步会员数据未找到uid:'.$new_member->uid);
            return false;
        }

        if ($this->existMember($old_member->uid)) {
            \Log::debug('支付宝同步会员数据new_uid:'.$new_member->uid.'-old_uid:'.$old_member->uid.'都是支付宝用户无法同步');
            return false;
        }

        \Log::debug('支付宝同步会员数据new_uid:'.$new_member->uid.'-old_uid:'.$old_member->uid);

        try {
            DB::beginTransaction();
            $alipay_member->member_id = $old_member->uid;

            if(!$alipay_member->save()) {
                throw new Exception("yz_member_alipay表信息修改失败");
            }

            $status = $this->deleteMember($new_member);
            if (!$status) {
                \Log::debug('删除同步用户信息失败,oldID:'.$old_member->uid.'-newID:'.$new_member->uid);
                throw new Exception("删除new用户信息失败");
            }
            $this->setSession($old_member->uid);
            DB::commit();
            $this->insertMergeLog('支付宝', $old_member, $new_member);
            return true;
        } catch (Exception $e) {
        	DB::rollBack();
            $this->insertMergeLog('支付宝:'.$e->getMessage(), $old_member, $new_member, 1);
            return false;
        }
	}

    public function updateMemberOther($old_member, $new_member)
    {
        $alipay_member = MemberAlipay::uniacid()->where('member_id', $new_member->uid)->first();

        if (empty($alipay_member)) {
            \Log::debug('微信'.$this->wechat_sign.'同步会员数据未找到uid:'.$new_member->uid);
            return false;
        }

        if (!$this->existMember($old_member->uid)) {
            \Log::debug('微信'.$this->wechat_sign.'同步会员数据new_uid='.$new_member->uid.',  old_uid:'.$old_member->uid.'不是支付宝会员');
            return false;
        }

        $unique_info = MemberUniqueModel::uniacid()->where('member_id', $alipay_member->uid)->first();

        \Log::debug('微信'.$this->wechat_sign.'保留会员数据new_uid:'.$new_member->uid.'删除old_uid:'.$old_member->uid);

        try {
            DB::beginTransaction();
            $new_member->mobile = $old_member->mobile;
            MemberAlipay::uniacid()->where('member_id', $old_member->uid)->update([
                'member_id' => $new_member->uid,
            ]);

            if(!$new_member->save()) {
                throw new Exception("mc_members表手机号修改失败");
            }
            if ($unique_info) {
                \Log::debug('微信开放平台-保留newUID:'.$new_member->uid.'删除oldUID:'.$old_member->uid);
                $unique_info->member_id = $new_member->uid;
                $unique_info->save();
            }

            $status = $this->deleteMember($old_member);
            if (!$status) {
                \Log::debug('删除同步用户信息失败,oldID:'.$old_member->uid.'-newID:'.$new_member->uid);
                throw new Exception("删除old用户信息失败");
            }

            $this->setSession($new_member->uid);
            DB::commit();
//            $this->insertMergeLog('微信'.$this->wechat_sign, $old_member, $new_member);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
//            $this->insertMergeLog('微信'.$this->wechat_sign.':'.$e->getMessage(), $old_member, $new_member, 1);
//            \Log::debug('微信'.$this->wechat_sign.'同步会员数据uid:'.$new_member->uid.'失败');
            return false;
        }
    }

    //抛弃
    public function old($old_member, $new_member)
    {

        $alipay_member = MemberAlipay::uniacid()->where('member_id', $new_member->uid)->first();

        if (empty($alipay_member)) {
            \Log::debug('支付宝同步会员数据未找到uid:'.$new_member->uid);
            return false;
        }

        if (!is_null(MemberAlipay::uniacid()->where('member_id', $old_member->uid)->first())) {
            \Log::debug('支付宝同步会员数据new_uid:'.$new_member->uid.'-old_uid:'.$old_member->uid.'平台相同');
            return false;
        }

        \Log::debug('支付宝同步会员数据new_uid:'.$new_member->uid.'-old_uid:'.$old_member->uid);

        try {
            DB::beginTransaction();
            $alipay_member->member_id = $old_member->uid;

            if(!$alipay_member->save()) {
                throw new Exception("yz_member_alipay表信息修改失败");
            }

            $status = $this->deleteMember($new_member);
            if (!$status) {
                \Log::debug('删除同步用户信息失败,oldID:'.$old_member->uid.'-newID:'.$new_member->uid);
                throw new Exception("删除new用户信息失败");
            }
            $this->setSession($old_member->uid);
            DB::commit();
            $this->insertMergeLog('支付宝', $old_member, $new_member);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $this->insertMergeLog('支付宝:'.$e->getMessage(), $old_member, $new_member, 1);
            return false;
        }
    }
}