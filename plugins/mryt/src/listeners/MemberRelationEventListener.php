<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/28
 * Time: 15:48
 */

namespace Yunshop\Mryt\listeners;


use app\common\events\member\MemberCreateRelationEvent;
use app\common\events\member\MemberRelationEvent;
use app\common\models\CouponLog;
use app\common\models\MemberShopInfo;
use app\common\services\member\MemberRelation;
use app\Jobs\MemberAddupVipJob;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\Mryt\job\UpgradeByRegisterJob;
use Yunshop\Mryt\services\UpgradeService;
use Yunshop\Mryt\models\MrytMemberModel;
use Yunshop\Mryt\services\AwardService;
use Yunshop\Mryt\services\UpgradeConditionsService;
use app\common\models\Coupon;
use app\common\models\MemberCoupon;
use app\common\models\Member;
use app\backend\modules\coupon\services\Message;
class MemberRelationEventListener
{
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(MemberRelationEvent::class, function ($event) {
            $member = $event->getMemberModel();

            // Yy edit:2019-03-06
            $orderId = $event->getOrderId();

            $yzMemberModel = $event->getMemberModel()->yzMember;
            \Log::info('MRYT会员关系' . $yzMemberModel->member_id);

            $mryt = MrytMemberModel::where('uid', $member->uid)->first();
            if (!$mryt) {
                $this->addMrtyMember($member, $orderId);
            }

            // 会员升级
            $levels = UpgradeService::getLevelUpgraded();
            $this->dispatch(new UpgradeByRegisterJob($member->uid, $levels));
            
        });
    }

    public function addMrtyMember($member, $orderId)
    {
        $mrytMemberModel = new MrytMemberModel();
        $mrytMemberData  = [
            'uniacid' => \Yunshop::app()->uniacid,
            'uid' => $member->uid,
            'realname' => $member->realname,
            'mobile' => $member->mobile,
            'level' => 0,
        ];

        $mrytMemberModel->setRawAttributes($mrytMemberData);
        if ($mrytMemberModel->save()) {
            //统计新进VIP
            $this->dispatch(new MemberAddupVipJob($member->uid, \YunShop::app()->uniacid));

            //TODO 传uid
            $res = new AwardService($member->uid, $mrytMemberData['uniacid'], '', $orderId);
            $res->upgrateAward();

            //发放优惠券
            $this->sendCoupon($member->uid);
        }
    }

    public function sendCoupon($memberId)
    {
        $set = \Setting::get('plugin.mryt_set');
        $coupon_id = $set['coupon']['coupon_id'];
        $mun = $set['coupon']['coupon_several'];
        if (empty($coupon_id) || empty($mun)) {
            return;
        }
        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'coupon_id' => $coupon_id,
            'get_type' => 0,
            'used' => 0,
            'get_time' => strtotime('now'),
        ];

        $couponModel = Coupon::getCouponById($coupon_id);
        $messageData = [
            'title' => htmlspecialchars_decode($couponModel->resp_title),
            'image' => tomedia($couponModel->resp_thumb),
            'description' => $couponModel->resp_desc ? htmlspecialchars_decode($couponModel->resp_desc) : '亲爱的 [nickname], 你获得了 1 张 "' . $couponModel->name . '" 优惠券',
            'url' => $couponModel->resp_url ?: yzAppFullUrl('home'),
        ];


        for ($i = 0; $i < $mun; $i++) {
            $memberCoupon = new MemberCoupon;
            $data['uid'] = $memberId;
            $res = $memberCoupon->create($data);

            //写入log
            if ($res) { //发放优惠券成功
                $log = '成为MRYT 会员发放优惠券成功: 成功发放 ' . $mun . ' 张优惠券( ID为 ' . $couponModel->id . ' )给用户( Member ID 为 ' . $memberId . ' )';

            } else { //发放优惠券失败
                $log = '成为MRYT 会员发放优惠券失败: 发放优惠券( ID为 ' . $couponModel->id . ' )给用户( Member ID 为 ' . $memberId . ' )时失败!';
                \Log::info($log);
            }
            $this->log($log, $couponModel, $memberId);
        }

        if (!empty($messageData['title'])) { //没有关注公众号的用户是没有 openid
            $templateId = \Setting::get('coupon_template_id'); //模板消息ID
            $nickname = Member::getMemberById($memberId)->nickname;
            $dynamicData = [
                'nickname' => $nickname,
                'couponname' => $couponModel->name,
            ];
            $messageData['title'] = self::dynamicMsg($messageData['title'], $dynamicData);
            $messageData['description'] = self::dynamicMsg($messageData['description'], $dynamicData);

            Message::message($messageData, $templateId, $memberId); //默认使用微信"客服消息"通知, 对于超过 48 小时未和平台互动的用户, 使用"模板消息"通知
        }
    }

    public function log($log, $couponModel, $memberId)
    {
        $logData = [
            'uniacid' => \YunShop::app()->uniacid,
            'logno' => $log,
            'member_id' => $memberId,
            'couponid' => $couponModel->id,
            'paystatus' => 0, //todo 手动发放的不需要支付?
            'creditstatus' => 0, //todo 手动发放的不需要支付?
            'paytype' => 0, //todo 这个字段什么含义?
            'getfrom' => 0,
            'status' => 0,
            'createtime' => time(),
        ];
        $res = CouponLog::create($logData);
        return $res;
    }

    public function fixRelation($parent_id)
    {
        $members = MemberShopInfo::where('parent_id', $parent_id)->get();

        if (!is_null($members)) {
            $member_relation = new MemberRelation();
            foreach ($members as $m) {
                echo '----递归查询----' . $m->member_id . '-' . $parent_id . '<BR>';
                $member_relation->fixData($m->member_id, $parent_id);

                $this->fixRelation($m->member_id);
            }
        }

echo '------结束----' . $m->member_id . '<BR>';

    }

    public function fixAward($parent_id)
    {
        $uniacid = \Yunshop::app()->uniacid;

        $members = MemberShopInfo::where('parent_id', $parent_id)->get();

        if (!is_null($members)) {
            foreach ($members as $m) {
                echo '----递归查询----' . $m->member_id . '-' . $parent_id . '<BR>';
                //统计新进VIP
                $this->dispatch(new MemberAddupVipJob($m->member_id, \YunShop::app()->uniacid));

                //TODO 传uid
                $res = new AwardService($m->member_id, $uniacid, '');
                $res->upgrateAward();

                //发放优惠券
                $this->sendCoupon($m->member_id);

                // 会员升级
                $levels = UpgradeService::getLevelUpgraded();
                $this->dispatch(new UpgradeByRegisterJob($m->member_id, $levels));

                $this->fixAward($m->member_id);
            }
        }

        echo '------结束----' . $m->member_id . '<BR>';
    }
}