<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/3
 * Time: 15:53
 */

namespace Yunshop\AlipayOnekeyLogin\services\infosynchro;


use Illuminate\Support\Facades\DB;
use app\common\models\MemberMiniAppModel;
use app\frontend\modules\member\models\MemberUniqueModel;
use Exception;
use Yunshop\AlipayOnekeyLogin\models\MemberAlipay;

class MinUserService extends MemberInfoService
{
    protected $wechat_sign = 'min';

    public function updateMember($old_member, $new_member)
    {

        $wechat_user = MemberMiniAppModel::uniacid()->where('member_id', $new_member->uid)->first();


        if (empty($wechat_user)) {
            \Log::debug('微信'.$this->wechat_sign.'同步会员数据未找到uid:'.$new_member->uid);
            return false;
        }


        if (!$this->existMember($old_member->uid)) {
            \Log::debug('微信'.$this->wechat_sign.'同步会员数据new_uid='.$new_member->uid.',  old_uid:'.$old_member->uid.'不是支付宝会员');
            return false;
        }


        $unique_info = MemberUniqueModel::uniacid()->where('member_id', $wechat_user->member_id)->first();

        \Log::debug('微信'.$this->wechat_sign.'同步会员数据new_uid:'.$new_member->uid.'-old_uid:'.$old_member->uid);

        try {
            DB::beginTransaction();

            $wechat_user->member_id = $old_member->uid;

            if(!$wechat_user->save()) {
                throw new Exception("yz_member_mini_app表信息修改失败");
            }

            if ($unique_info) {
                \Log::debug('微信开放平台-newUID:'.$new_member->uid.'-oldUID:'.$old_member->uid);
                $unique_info->member_id = $old_member->uid;
                $unique_info->save();
            }

            $status = $this->deleteMember($new_member);
            if (!$status) {
                \Log::debug('删除同步用户信息失败,oldID:'.$old_member->uid.'-newID:'.$new_member->uid);
                throw new Exception("删除new用户信息失败");
            }
            //Session::set('member_id', $old_member->uid);
            $this->setSession($old_member->uid);
            DB::commit();
            $this->insertMergeLog('微信'.$this->wechat_sign, $old_member, $new_member);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $this->insertMergeLog('微信'.$this->wechat_sign.':'.$e->getMessage(), $old_member, $new_member, 1);
            \Log::debug('微信'.$this->wechat_sign.'同步会员数据uid:'.$new_member->uid.'失败');
            return false;
        }
    }

    public function updateMemberOther($old_member, $new_member)
    {
        $mini_user = MemberMiniAppModel::uniacid()->where('member_id', $new_member->uid)->first();

        if (empty($mini_user)) {
            \Log::debug('微信'.$this->wechat_sign.'同步会员数据未找到uid:'.$new_member->uid);
            return false;
        }

        if (!$this->existMember($old_member->uid)) {
            \Log::debug('微信'.$this->wechat_sign.'同步会员数据new_uid='.$new_member->uid.',  old_uid:'.$old_member->uid.'不是支付宝会员');
            return false;
        }

        $unique_info = MemberUniqueModel::uniacid()->where('member_id', $mini_user->uid)->first();

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

    public function old($old_member, $new_member)
    {

        $wechat_user = MemberMiniAppModel::uniacid()->where('member_id', $new_member->uid)->first();


        if (empty($wechat_user)) {
            \Log::debug('微信'.$this->wechat_sign.'同步会员数据未找到uid:'.$new_member->uid);
            return false;
        }


        if (!is_null(MemberMiniAppModel::uniacid()->where('member_id', $old_member->uid)->first())) {
            \Log::debug('微信'.$this->wechat_sign.'同步会员数据new_uid:'.$new_member->uid.'-old_uid:'.$old_member->uid.'平台相同');
            return false;
        }

        $unique_info = MemberUniqueModel::uniacid()->where('member_id', $wechat_user->member_id)->first();

        \Log::debug('微信'.$this->wechat_sign.'同步会员数据new_uid:'.$new_member->uid.'-old_uid:'.$old_member->uid);

        try {
            DB::beginTransaction();

            $wechat_user->member_id = $old_member->uid;

            if(!$wechat_user->save()) {
                throw new Exception("yz_member_mini_app表信息修改失败");
            }

            if ($unique_info) {
                \Log::debug('微信开放平台-newUID:'.$new_member->uid.'-oldUID:'.$old_member->uid);
                $unique_info->member_id = $old_member->uid;
                $unique_info->save();
            }

            $status = $this->deleteMember($new_member);
            if (!$status) {
                \Log::debug('删除同步用户信息失败,oldID:'.$old_member->uid.'-newID:'.$new_member->uid);
                throw new Exception("删除new用户信息失败");
            }
            //Session::set('member_id', $old_member->uid);
            $this->setSession($old_member->uid);
            DB::commit();
            $this->insertMergeLog('微信'.$this->wechat_sign, $old_member, $new_member);
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $this->insertMergeLog('微信'.$this->wechat_sign.':'.$e->getMessage(), $old_member, $new_member, 1);
            \Log::debug('微信'.$this->wechat_sign.'同步会员数据uid:'.$new_member->uid.'失败');
            return false;
        }
    }
}