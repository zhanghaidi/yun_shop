<?php

namespace app\frontend\modules\coupon\models;
use app\common\facades\Setting;


class MemberCoupon extends \app\common\models\MemberCoupon
{
    public $table = 'yz_member_coupon';

    const USED = 1;
    const NOT_USED = 0;


    //获取指定用户名下的优惠券
    public static function getCouponsOfMember($memberId)
    {
        $coupons = static::uniacid()->with(['belongsToCoupon' => function ($query) {
            return $query->select(['id', 'name', 'coupon_method', 'deduct', 'discount', 'enough', 'use_type', 'category_ids', 'categorynames',
                'goods_ids', 'goods_names', 'storeids', 'storenames', 'time_limit', 'time_days', 'time_start', 'time_end', 'total',
                'money', 'credit', 'plugin_id','transfer','jump_switch']);
        }])->where('uid', $memberId)
            ->select(['id', 'coupon_id','get_type', 'used', 'use_time', 'get_time','is_member_deleted','lock_time','lock_expire_time','trans_from','created_at','transfer_times'])
            ->orderBy('get_time', 'desc');
        return $coupons;
    }

    public static function getExchange($memberId, $pluginId)
    {
        $coupons = static::uniacid()
            ->whereHas('belongsToCoupon',function ($query) use($pluginId) {
                return $query->where('plugin_id',$pluginId)->where('use_type',8);
            })
            ->with(['belongsToCoupon' => function ($query) {
                return $query->select(['id', 'name', 'coupon_method', 'deduct', 'discount', 'enough', 'use_type',
                    'goods_ids', 'goods_names','time_limit', 'time_days', 'time_start', 'time_end', 'total',
                    'money', 'credit', 'plugin_id']);
            }])
            ->where('used', '=', 0)
            ->where('is_member_deleted', 0)
            ->where('uid', $memberId)
            ->select(['id', 'coupon_id', 'used','use_time', 'get_time'])
            ->orderBy('get_time', 'desc');
        return $coupons;
    }

    //fixby-zlt-coupontransfer 2020-10-15 13:50 锁定优惠券
    public static function lockMemberCoupon(MemberCoupon $_model){
        $trans_set = Setting::get('coupon.transfer_coupons');
        $lock_time = !empty($trans_set['lock_time']) ? $trans_set['lock_time'] : 30;
        $update = [
            'lock_time' => time(),
            'lock_expire_time' => time() + 60 * $lock_time,
            'has_transfered' => 1,
        ];
        return self::where('id',$_model->id)->update($update);
    }

}
