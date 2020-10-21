<?php

namespace app\frontend\modules\coupon\listeners;

use app\common\facades\Setting;
use app\common\models\Coupon;
use app\common\models\Member;
use app\common\models\MemberCoupon;
use app\common\models\notice\MessageTemp;
use app\common\models\UniAccount;
use app\framework\Support\Facades\Log;


/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/12
 * Time: 下午4:28
 */
class CouponExpireNotice
{
    public $set;
    public $setLog;
    public $uniacid;

    public function handle()
    {
        Log::info('优惠券到期处理');
        set_time_limit(0);
        $uniAccount = UniAccount::getEnable();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $this->uniacid = $u->uniacid;
            $this->set = Setting::get('shop.coupon');
            $this->setLog = Setting::get('shop.coupon_log');
            $this->sendExpireNotice();

        }
    }

    public function sendExpireNotice()
    {
        Log::info('------------------------ 优惠券过期提醒 BEGIN -------------------------------');
        if ($this->set['every_day'] != date('H')) {
            return;
        }
        if ($this->setLog['current_d'] == date('d')) {
            Log::info('优惠券过期提醒 current_d =' . $this->setLog['current_d'] .' now_d = '. date('d'));
            return;
        }

        $this->setLog['current_d'] = date('d');
        Setting::set('shop.coupon_log', $this->setLog);

        $expiredCoupon = [];
        $present = time();
        foreach(\app\common\models\MemberCoupon::uniacid()->where([['used','=',0]])->cursor() as $coupon) {
            if ($coupon->time_end == '不限时间') {
                continue;
            }
            $end = strtotime($coupon->time_end) - $this->set['delayed'] * 86400;
            if ($present < $end || strtotime($coupon->time_end) < $present) {
                continue;
            }
            $t_key = $coupon->uid . '_' . $coupon->coupon_id;
            if (key_exists($t_key, $expiredCoupon)) {
                if($expiredCoupon[$t_key]['time_end'] > $coupon->time_end)
                    $expiredCoupon[$t_key]['time_end'] = $coupon->time_end;
                $expiredCoupon[$t_key]['total_num'] += 1;
                if ($coupon->belongsToCoupon->coupon_method == 1) {
                    $expiredCoupon[$t_key]['total_detect'] += $coupon->belongsToCoupon->deduct;
                }
            } else {
                $expiredCoupon[$t_key] = $coupon->toArray();
                $expiredCoupon[$t_key]['total_num'] = 1;
                if ($coupon->belongsToCoupon->coupon_method == 1) {
                    $expiredCoupon[$t_key]['total_detect'] = $coupon->belongsToCoupon->deduct;
                } else {
                    $expiredCoupon[$t_key]['total_detect'] = $coupon->belongsToCoupon->discount;
                }
            }
        }

        foreach ($expiredCoupon as $value) {
            $member = Member::getMemberByUid($value['uid'])->with('hasOneFans')->first();
            $couponData = [
                'name' => $value['belongs_to_coupon']['name'],
                'api_limit' => $this->apiLimit($value['belongs_to_coupon']),
                'time_end' => $value['time_end'],
                'total_num' => $value['total_num'],
                'total_detect' => $value['total_detect'],
            ];
            $this->sendNotice($couponData, $member);
        }

        Log::info('------------------------ 优惠券过期提醒 END -------------------------------');
    }

    public function sendNotice($couponData, $member)
    {
        if ($member->hasOneFans->follow == 1) {
//            $message = $this->set['expire'];
//            $message = str_replace('[优惠券名称]', $couponData['name'], $message);
//            $message = str_replace('[优惠券使用范围]', $couponData['api_limit'], $message);
//            $message = str_replace('[过期时间]', $couponData['time_end'], $message);
//            $msg = [
//                "first" => '您好',
//                "keyword1" => $this->set['expire_title'] ? $this->set['expire_title'] : '优惠券过期提醒',
//                "keyword2" => $message,
//                "remark" => "",
//            ];
//            \app\common\services\MessageService::notice($this->set['template_id'], $msg, $member['openid'], $this->uniacid);
            $temp_id = $this->set['expire'];
            if (!$temp_id) {
                return;
            }
            $params = [
                ['name' => '优惠券名称', 'value' => $couponData['name']],
                ['name' => '昵称', 'value' => $member['nickname']],
                ['name' => '优惠券使用范围', 'value' => $couponData['api_limit']],
                ['name' => '过期时间', 'value' => $couponData['time_end']],
                ['name' => '优惠券张数', 'value' => $couponData['total_num']],
                ['name' => '优惠券优惠金额', 'value' => $couponData['total_detect']],
            ];
            $msg = MessageTemp::getSendMsg($temp_id, $params);
            if (!$msg) {
                return;
            }

            Log::info('优惠券过期提醒 running : Message = '.\GuzzleHttp\json_encode($msg));
            \app\common\services\MessageService::notice(MessageTemp::$template_id, $msg, $member->uid, $this->uniacid);

        }
        return;
    }

    public function apiLimit($coupon)
    {
        $api_limit = '';
        switch ($coupon['use_type']) {
            case Coupon::COUPON_SHOP_USE:
                $api_limit = '商城通用';
                break;
            case Coupon::COUPON_CATEGORY_USE:
                $api_limit = '适用于下列分类: ';
                $api_limit .= implode(',', $coupon['categorynames']);
                break;
            case Coupon::COUPON_GOODS_USE:
                $api_limit = '适用于下列商品: ';
                $api_limit .= implode(',', $coupon['goods_names']);
                break;
            case Coupon::COUPON_STORE_USE:
                $api_limit = '适用于下列门店: ';
                $api_limit .= implode(',', $coupon['storenames']);
                break;
            case Coupon::COUPON_SINGLE_STORE_USE:
                $api_limit = '适用于下列门店: ';
                $api_limit .= implode(',', $coupon['storenames']);
                break;
        }
        return $api_limit;
    }

    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Coupon-expire-notice', '*/1 * * * *', function () {
                $this->handle();
                return;
            });
        });
    }
}