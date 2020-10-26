<?php

namespace app\frontend\modules\coupon\listeners;

use app\common\facades\Setting;
use app\common\models\MemberCoupon;
use app\common\models\UniAccount;
use app\framework\Support\Facades\Log;


/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2020/10/15
 * Time: 下午14:30
 */
class CouponLock
{
    public $set;
    public $setLog;
    public $uniacid;

    public function handle()
    {
        Log::info('优惠券转让锁定处理');
        set_time_limit(0);
        $uniAccount = UniAccount::getEnable();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $this->uniacid = $u->uniacid;
            $this->processLockedCoupon();
        }
    }

    public function processLockedCoupon()
    {
        $lockedCoupons = MemberCoupon::uniacid()->whereBetween('lock_expire_time', [1, time()])->where([['used','=',0]])->get()->toArray();

        foreach ($lockedCoupons as $coupon) {
            Log::info('解除优惠券转让锁定,优惠券ID:' . $coupon['id']);
            try{
                MemberCoupon::uniacid()->where(id,$coupon['id'])->update(['lock_expire_time' => 0]);
            }catch (\ErrorException $exception){
                Log::error('processLockedCoupon error:' . $exception->getMessage());
            }
        }
    }

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Coupon-lock', '*/1 * * * *', function () {
                $this->handle();
                return;
            });
        });
    }
}