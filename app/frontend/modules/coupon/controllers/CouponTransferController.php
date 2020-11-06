<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/26 下午1:44
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\coupon\controllers;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\frontend\models\Member;
use app\frontend\modules\coupon\models\MemberCoupon;
use app\frontend\modules\coupon\services\CouponSendService;
use app\backend\modules\coupon\services\MessageNotice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CouponTransferController extends ApiController
{
    public $memberModel;
    protected $transferInfo = [];

    public function index()
    {
        $recipient = trim(\YunShop::request()->recipient);
        if (!$this->getMemberInfo()) {
            return  $this->errorJson('未获取到会员信息');
        }
        if (!Member::uniacid()->select('uid')->where('uid',$recipient)->first()) {
            return  $this->errorJson('被转让者不存在');
        }
        if ($this->memberModel->uid == $recipient) {
            return  $this->errorJson('转让者不能是自己');
        }


        $record_id = trim(\YunShop::request()->record_id);
        $_model = MemberCoupon::select('id','coupon_id')->where('id',$record_id)->with(['belongsToCoupon'])->first();
        if (!$_model) {
            return $this->errorJson('未获取到该优惠券记录ID');
        }

        if($_model->belongsToCoupon->get_type == 1 && $_model->belongsToCoupon->get_max != -1)
        {
            $person = MemberCoupon::uniacid()
                    ->where(["coupon_id"=>$_model->coupon_id,"uid"=>$recipient])
                    ->count();//会员已有数量
            if($person + 1 > $_model->belongsToCoupon->get_max)
            {
                return  $this->errorJson('被转让者已达该优惠券领取上限');
            }
        }

        $couponService = new CouponSendService();
        $result = $couponService->sendCouponsToMember($recipient,[$_model->coupon_id],'5','',$this->memberModel->uid);
        if (!$result) {
            return $this->errorJson('转让失败：(写入出错)');
        }

        $result = MemberCoupon::where('id',$_model->id)->update(['used' => 1,'use_time' => time(),'deleted_at' => time()]);
        if (!$result) {
            return $this->errorJson('转让失败：(记录修改出错)');
        }
//        '.$this->memberModel->uid.''.[$_model->coupon_id].'

        //发送获取通知
        //MessageNotice::couponNotice($_model->coupon_id,$recipient);

        return $this->successJson('转让成功,');
    }

    //fixby-zlt-coupontransfer 2020-10-15 10:15 发起转让优惠券
    public function trans()
    {
        if (!$this->getMemberInfo()) {
            return  $this->errorJson('未获取到会员信息');
        }

        $record_id = trim(\YunShop::request()->record_id);
        $_model = MemberCoupon::select('id','coupon_id', 'uid', 'lock_expire_time', 'get_time', 'transfer_times')->where('id',$record_id)->with(['belongsToCoupon'])->first();
        if (!$_model) {
            return $this->errorJson('未获取到该优惠券记录ID');
        }

        if(!$_model->belongsToCoupon->transfer){
            return $this->errorJson('该优惠券不允许转让');
        }

        if($_model->lock_expire_time){
            return $this->errorJson('该优惠券暂时已锁定，请等待',$_model->toArray());
        }

        if($_model->transfer_times){
            return $this->errorJson('该优惠券无法再次转让');
        }

        $result = MemberCoupon::lockMemberCoupon($_model);
        if (!$result) {
            return $this->errorJson('发起转让优惠券失败：(写入出错)');
        }

        return $this->successJson('发起转让优惠券成功！');
    }

    //fixby-zlt-coupontransfer 2020-10-14 17:15 接收转让优惠券
    public function receive()
    {

        if (!$this->getMemberInfo()) {
            return  $this->errorJson('未获取到会员信息');
        }

        $record_id = trim(\YunShop::request()->record_id);
        $_model = MemberCoupon::select('id','coupon_id', 'uid', 'lock_expire_time', 'get_time')->where('id',$record_id)->with(['belongsToCoupon'])->first();
        if (!$_model) {
            return $this->errorJson('未获取到该优惠券记录ID');
        }

        if($_model->used){
            return $this->errorJson('优惠券已经被使用');
        }

        if(!$_model->lock_expire_time){
            return $this->errorJson('该优惠券已经自动取消转让');
        }

        if ($this->memberModel->uid == $_model->uid) {
            return  $this->errorJson('接收者不能是自己');
        }

        if($_model->belongsToCoupon->get_type == 1 && $_model->belongsToCoupon->get_max != -1)
        {
            $person = MemberCoupon::uniacid()->where(["coupon_id"=>$_model->coupon_id,"uid"=>$this->memberModel->uid])->count();//会员已有数量
            if($person + 1 > $_model->belongsToCoupon->get_max)
            {
                return  $this->errorJson('已达该优惠券领取上限');
            }
        }

        $cache_key = 'coupon:trans:' . $record_id;
        $lock_uid = Cache::get($cache_key);
        if(!empty($lock_uid) && $lock_uid != $this->memberModel->uid){
            return $this->errorJson('该优惠券暂不可领取，请等候');
        }
        Cache::put($cache_key, $this->memberModel->uid, 1);
        $couponService = new CouponSendService();
        $result = $couponService->receiveTransferCoupon($this->memberModel->uid,[$_model->coupon_id],'5','', $record_id, strtotime($_model->get_time));
        if (!$result) {
            Cache::forget($cache_key);
            return $this->errorJson('领取优惠券失败：(写入出错)');
        }

        $result = MemberCoupon::where('id',$_model->id)->update(['used' => 1,'use_time' => time(),'deleted_at' => time(), 'lock_time' => null, 'lock_expire_time' => null, 'transfer_times' => 1]);
        if (!$result) {
            Cache::forget($cache_key);
            return $this->errorJson('领取优惠券失败：(记录修改出错)');
        }
        Cache::forget($cache_key);
        return $this->successJson('领取优惠券成功');
    }

    public function getCouponInfo()
    {

        $record_id = trim(\YunShop::request()->record_id);

        if (!$this->getMemberInfo()) {
            return  $this->transJson('未获取到会员信息');
        }

        $_model = MemberCoupon::uniacid()->withTrashed()->where('id',$record_id)->with(['belongsToCoupon'])->first();
        if (!$_model) {
            return $this->transJson('未获取到该优惠券记录ID');
        }

        //接收到的优惠券不允许再次转让
        if($_model->trans_from && $_model->transfer_times){
            return $this->transJson('接收到的优惠券不能转让');
        }

        if($this->memberModel->uid != $_model->uid){
            $is_self = false;
        }else{
            $is_self = true;
        }

        $this->transferInfo = DB::table('diagnostic_service_user')->select('ajy_uid as uid','nickname','avatar','avatarUrl')->where('ajy_uid',$_model->uid)->first();

        if($is_self){
            if($_model->lock_expire_time){
                return $this->transJson('优惠券转让中', 1, $is_self,2, $_model->toArray());
            }else if($_model->transfer_times){
                $receive_coupon = MemberCoupon::uniacid()->where('trans_from', $record_id)->first()->toArray();
                $receiver_info = DB::table('diagnostic_service_user')->select('ajy_uid as uid','nickname','avatar','avatarUrl')->where('ajy_uid',$receive_coupon['uid'])->first();
                $receiver_info['receive_time'] = $receive_coupon['created_at'];
                return $this->transJson('优惠券已经转让', 1, $is_self, 1, $_model->toArray(), $receiver_info);
            }else {
                if($_model->has_transfered){
                    return $this->transJson('优惠券转让过但未被领取', 1, $is_self, 4, $_model->toArray());
                }else{
                    return $this->transJson('优惠券转让已失效', 1, $is_self, 3, $_model->toArray());
                }
            }
        }else{
            if($_model->lock_expire_time){
                return $this->transJson('优惠券转让中', 1, $is_self, 2, $_model->toArray());
            }else if($_model->transfer_times){
                $receive_coupon = MemberCoupon::uniacid()->where('trans_from', $record_id)->first()->toArray();
                $receiver_info = DB::table('diagnostic_service_user')->select('ajy_uid as uid','nickname','avatar','avatarUrl')->where('ajy_uid',$receive_coupon['uid'])->first();
                $receiver_info['receive_time'] = $receive_coupon['created_at'];
                return $this->transJson('该优惠券已经转让', 1, $is_self, 1, $_model->toArray(), $receiver_info, $receive_coupon['uid']== $this->memberModel->uid ? true : false);
            }else {
                return $this->transJson('优惠券转让已失效', 1, $is_self, 3, $_model->toArray());
            }
        }

    }

    public function transJson($message = '失败', $err_code = 0, $is_self = false, $status = 0, $coupon_info = [], $receiver_info = [], $is_receiver = false)
    {
        if(!empty($receiver_info)){
            $receiver_info['avatar'] = tomedia($receiver_info['avatar']);
        }
        if(!empty($this->transferInfo)){
            $this->transferInfo['avatar'] = tomedia($this->transferInfo['avatar']);
        }
        response()->json([
            'result' => $err_code,
            'msg' => $message,
            'data' => [
                'is_self'     => $is_self,
                'status'     => $status,
                'is_receiver' => $is_receiver,
                'coupon_info' => $coupon_info,
                'receiver_info' => $receiver_info,
                'transfer_info' => $this->transferInfo,
            ]
        ], 200, ['charset' => 'utf-8'])->send();
    }

    private function getMemberInfo()
    {
        return $this->memberModel = Member::select('uid')->where('uid',\YunShop::app()->getMemberId())->first();
    }

    //fixBy-wk- 20201105 获取转让优惠券基本配置
    public function getTransferSetting()
    {

        $set = \Setting::get('coupon.transfer_coupons');
        $data = [];
        if(!empty($set)){
            $data['banner'] = $set['banner'];
            $data['transfer_title'] = $set['transfer_title'];
            $data['transfer_desc'] = $set['transfer_desc'];
            $data['transfer_img'] = $set['transfer_img'];
        }
        return $this->successJson('获取成功', $data);

    }

}
