<?php

namespace Yunshop\Poster\services;

use app\frontend\modules\member\services\MemberOfficeAccountService;
use Yunshop\Poster\models\Poster;
use Yunshop\Poster\models\PosterQrcode;
use Yunshop\Poster\models\PosterScan;
use Yunshop\Poster\models\PosterAward;
use Yunshop\Poster\models\Qrcode;
use app\common\models\Member;
use app\common\models\MemberShopInfo;
use app\common\models\McMappingFans;
use app\common\services\finance\PointService;
use app\common\services\WechatPay;
use app\common\models\MemberCoupon;
use app\common\models\CouponLog;
use EasyWeChat\Message\News;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Log;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;

class ResponseEventService extends ResponseService
{
    const SCAN_EVENT = 1; //用户未关注时，进行关注后, 微信推送的事件类别
    const SUBSCRIBE_EVENT = 2; //用户已关注时, 微信推送的事件类别
    const SIGN_UP_THIS_TIME = 1; //用于标识扫码用户是否在本次扫码中注册商城

    //响应微信扫码的scan和subscribe事件
    public static function index($msg)
    {
        $msgEvent = strtolower($msg['event']);
        $msgEventKey = strtolower($msg['eventkey']);
        $subscriberOpenid = $msg['fromusername'];
        $uniacid = \YunShop::app()->get('uniacid');

        if ($msgEvent == 'scan') {
            $scene = $msgEventKey;
            $eventType = self::SCAN_EVENT;
        } else {
            //如果用户之前未关注，进行关注后推送的 Event 是 "subscribe",
            //推送的 EventKey 是以 "qrscene_" 为前缀，后面跟着二维码的参数值.
            //因为需求中提到存在这种情况 -- "尽管之前已经关注,但还不是商城的会员",
            //所以这里并不根据 Event 类型来判别是否是会员, 只是识别出二维码的特征值(场景值/场景字符串), 用于定位二维码 ID
            $scene = substr($msgEventKey, strpos($msgEventKey, '_') + 1);
            $eventType = self::SUBSCRIBE_EVENT;
        }
        \Log::debug('------海报消息-eventkey-----', $scene);
        if (is_int($scene) && ($scene != 0)) { //临时二维码
            $sceneId = $scene;
            $qrcode = Qrcode::getQrcodeBySceneId($sceneId);
        } else { //永久二维码
            $sceneStr = $scene;
            $qrcode = Qrcode::getForeverQrcodeBySceneStr($sceneStr);
        }
        \Log::debug('------海报消息-二维码-----', $qrcode);

        $qrcodeId = $qrcode->id;
        $posterId = PosterQrcode::getPosterIdByQrcodeId($qrcodeId);
        $poster = Poster::getPosterById($posterId);

        \Log::debug('------海报消息-海报-----', $poster);

        if ($poster->status != 1){
            $notice = array(
                'type' => 'text',
                'content' => '该海报已经失效, 请尝试其它海报',
            );
            return $notice;
        }

        /* 预留给活动海报
                //判断是否在活动时间内
                if ($poster->type == Poster::TEMPORARY_POSTER){
                    $status = self::checkTime($poster->time_start, $poster->time_end);
                    if ($status == Poster::NOT_YET_START ){ //活动还未开始
                        $remind = $poster->supplement->not_start_reminder;
                        $remind = empty($remind) ? '活动将于 [starttime] 开始，请耐心等待...' : $remind;
                        $remind = self::dynamicTime($remind, $poster);
                        $notice = array(
                            'type' => 'text',
                            'content' => $remind,
                        );
                        return $notice;
                    } else if ($status == Poster::ALREADY_FINISHED){ //活动已经结束
                        $remind = $poster->supplement->finish_reminder;
                        $remind = empty($remind) ? '活动已于 [endtime] 结束，谢谢您的关注!' : $remind;
                        $remind = self::dynamicTime($remind, $poster);
                        $notice = array(
                            'type' => 'text',
                            'content' => $remind,
                        );
                        return $notice;
                    }
                }
        */

        //微擎框架会自动记录扫码记录到qrcode_stat, 所以这里无需记录

        //未注册用户自动注册, 且确定分销关系并奖励
        $recommendMemberId = PosterQrcode::getRecommenderIdByQrcodeId($qrcodeId);
        $recommenderOpenid = Member::getOpenId($recommendMemberId);
        $recommender = Member::getMemberById($recommendMemberId);
        \Log::debug(sprintf('---海报用户---%d', $recommendMemberId));
        $member_shop_info_model = MemberShopInfo::getMemberShopInfoByOpenid($subscriberOpenid);
        \Log::debug('------海报消息-关注者商城会员模型-----', $member_shop_info_model);
        if (empty($member_shop_info_model)) { //如果该关注公众号的用户未注册芸商城
            $is_register = -1;//记录之前有没有注册过
            $wechatUserBasicInfo = self::getWechatUserBasicInfo($subscriberOpenid); //获取已关注公众号的用户的基本信息
            \Log::debug('------海报消息-关注者信息-----', $wechatUserBasicInfo);
            if ($poster->auto_sub == 1) { //如果开启了海报的"扫码关注成为下线"
                \Log::debug(sprintf('----海报发展用户下线---%d', $recommendMemberId));
                (new MemberOfficeAccountService())->memberLogin(json_decode(json_encode($wechatUserBasicInfo), true), $recommendMemberId); //注册
            } else {
                \Log::debug('----海报发展总店下线---');
                (new MemberOfficeAccountService())->memberLogin(json_decode(json_encode($wechatUserBasicInfo), true), 0); //注册, 做为总店的下线
            }

            $flag = self::SIGN_UP_THIS_TIME; //标注是在本次注册商场, 用于后期开发"统计之前已关注但未注册商城的用户,在本次扫码中注册商城"的数量
            $subscriberMemberModel = McMappingFans::getUId($uniacid, $subscriberOpenid); //尽管进入这个方法的用户都是已关注公众号的用户, 但是偶尔会缺失 mc_member 和 mapping_fans 数据
            $subscriberMemberId = $subscriberMemberModel->uid;
            $subscriber = Member::getMemberById($subscriberMemberId);

            if (!$subscriber->nickname || !$subscriber->avatar) { //因为关注公众号后, 有时候微擎并没有获取到用户的头像或者昵称
                $subscriber->update(['nickname' => $wechatUserBasicInfo->nickname, 'avatar' => $wechatUserBasicInfo->headimgurl]);
            }

            //奖励
            if ($recommendMemberId != $subscriberMemberId && (!empty($poster->supplement->recommender_credit) || !empty($poster->supplement->recommender_bonus)
                    || !empty($poster->supplement->recommender_coupon_num))
            ) {
                if (!empty($poster->supplement->recommender_credit)) {
                    \Log::debug('------海报设置奖励积分-----', $poster->supplement->recommender_credit);
                }
                //积分奖励
                if (!empty($poster->supplement->recommender_credit)) {
                    $pointData = array(
                        'uniacid' => $uniacid,
                        'point_income_type' => 1,
                        'member_id' => $recommendMemberId,
                        'point_mode' => 3,
                        'point' => $poster->supplement->recommender_credit,
                        'remark' => '超级海报: uid 为 ' . $recommendMemberId . ' 的用户推荐uid 为 ' . $subscriberMemberId . ' 的用户, 获得推送海报积分奖励 ' . $poster->supplement->recommender_credit . ' 个',
                    );
                    try {
                        $pointService = new PointService($pointData);
                        $pointService->changePoint();
                    } catch (\Exception $e) {
                        \Log::error('超级海报扫码注册的积分奖励出错:' . $e->getMessage());
                    }
                }

                //余额红包奖励
                if (!empty($poster->supplement->recommender_bonus) && ($poster->supplement->bonus_method == 1)) {
                    $data = [
                        'member_id' => $recommendMemberId,
                        'remark' => '超级海报: uid 为 ' . $recommendMemberId . ' 的用户推荐 uid 为 ' . $subscriberMemberId . ' 的用户, 因此获得余额奖励' . $poster->supplement->recommender_bonus . '元',
                        'source' => ConstService::SOURCE_AWARD,
                        'relation' => '',
                        'operator' => 1,
                        'operator_id' => $posterId,
                        'change_value' => $poster->supplement->recommender_bonus, //奖励金额
                    ];
                    try {
                        if (bccomp($poster->supplement->recommender_bonus, 0, 2) == 1) {
                            (new BalanceChange())->award($data);
                        }
                    } catch (\Exception $e) {
                        \Log::error('超级海报扫码注册的余额红包奖励出错:' . $e->getMessage());
                    }
                }

                //微信红包奖励
                if (!empty($poster->supplement->recommender_bonus) && ($poster->supplement->bonus_method == 2)) {

                    $wechatPay = new WechatPay;
                    $pay = \Setting::get('shop.pay');
                    $shop_name = \Setting::get('shop.shop.name');
                    $notify_url = ''; //回调地址
                    $app = $wechatPay->getEasyWeChatApp($pay, $notify_url);
                    $luckyMoney = $app->lucky_money;
                    $luckyMoneyData = [
                        'mch_billno' => $pay['weixin_mchid'] . date('YmdHis') . rand(1000, 9999),
                        'send_name' => $shop_name,
                        're_openid' => $recommenderOpenid,
                        'total_num' => 1,
                        'total_amount' => $poster->supplement->recommender_bonus * 100,
                        'wishing' => '超级海报推荐奖励',
                        'client_ip' => \Request::getClientIp(),
                        'act_name' => '超级海报推荐奖励',
                        'remark' => '超级海报: 推荐者奖励 ' . $poster->supplement->recommender_bonus . ' 元',
                    ];

                    try {
                        $result = $luckyMoney->sendNormal($luckyMoneyData);
                    } catch (\Exception $e) {
                        \Log::error('超级海报扫码注册的微信红包奖励出错:' . $e->getMessage());
                    }
                }


                //优惠券奖励
                if (!empty($poster->supplement->recommender_coupon_id) && $poster->supplement->recommender_coupon_num > 0) {
                    $couponData = [
                        'uniacid' => $uniacid,
                        'uid' => $recommendMemberId,
                        'coupon_id' => $poster->supplement->recommender_coupon_id,
                        'get_type' => 3,
                        'used' => 0,
                        'get_time' => strtotime('now'),
                    ];
                    for($i = 0; $i<$poster->supplement->recommender_coupon_num; $i++) {
                        $memberCoupon = new MemberCoupon;
                        $res = $memberCoupon->create($couponData);
                        //写入log
                        if ($res) { //发放优惠券成功
                            $log = '超级海报优惠券奖励: 奖励 ' . $poster->supplement->recommender_coupon_num . ' 张优惠券( ID为 ' . $couponData['coupon_id'] . ' )给用户( Member ID 为 ' . $couponData['uid'] . ' )';
                        //超级海报优惠券奖励：奖励1张优惠券（ID为：123）给用户（ID为：12）
                        } else { //发放优惠券失败
                            $log = '超级海报优惠券奖励: 奖励优惠券( ID为 ' . $couponData['coupon_id'] . ' )给用户( Member ID 为 ' . $couponData['uid'] . ' )时失败!';
                        }
                        $logData = [
                            'uniacid' => $uniacid,
                            'logno' => $log,
                            'member_id' => $couponData['uid'],
                            'couponid' => $couponData['coupon_id'],
                            'paystatus' => 0,
                            'creditstatus' => 0,
                            'paytype' => 0,
                            'getfrom' => 0,
                            'status' => 0,
                            'createtime' => time(),
                        ];
                        CouponLog::create($logData);
                    }
                }

                $shop_credit1 = \Setting::get('shop.shop.credit1') ?: '积分';
                //奖励通知
                if ($poster->supplement->recommender_credit != 0 || $poster->supplement->recommender_bonus != 0 || $poster->supplement->recommender_coupon_num != 0) {
                    if (!empty($recommenderOpenid)) {
                        $notice = $poster->supplement->recommender_award_notice;
                        if (empty($notice)) {
                            $notice = "\"[nickname]\" 通过您的二维码关注了公众号, 您因此获得了 [credit] 个".$shop_credit1." [money] 元奖励!";
                            if (!empty($poster->supplement->recommender_coupon_num)) {
                                $notice .= ' 以及 "[couponname]" 优惠券 [couponnum] 张!';
                            }
                        }
                        $notice = self::dynamicName($subscriber->nickname, $notice);
                        $notice = self::dynamicAward($poster, $notice, 'recommender');
                        $news = new News([
                            'title' => !empty($poster->supplement->recommender_award_title) ? $poster->supplement->recommender_award_title : '推荐关注奖励通知',
                            'description' => $notice,
                            'url' => $poster->response_url ? : yzAppFullUrl('home')
                        ]);
                        try {
                            Message::sendNotice($recommenderOpenid, $news);
                        } catch (\Exception $e) {
                            \Log::error('超级海报, 奖励通知时出错:' . $e->getMessage());
                        }
                    }
                }
            }

            if (($recommendMemberId != $subscriberMemberId) && (!empty($poster->supplement->subscriber_credit) || !empty($poster->supplement->subscriber_bonus)
                    || !empty($poster->supplement->subscriber_coupon_num))
            ) {
                \Log::debug('----超级海报,扫码会员ID---',$subscriberMemberId);
                \Log::debug('----超级海报--------------',$poster->supplement->subscriber_credit);

                //积分奖励
                if (!empty($poster->supplement->subscriber_credit)) {
                    $pointData = array(
                        'point_income_type' => 1,
                        'member_id' => $subscriberMemberId,
                        'point_mode' => 3,
                        'point' => $poster->supplement->subscriber_credit,
                        'remark' => '超级海报: uid 为 ' . $subscriberMemberId . ' 的用户, 获得关注海报积分奖励 ' . $poster->supplement->subscriber_credit . ' 个',
                    );
                    try {
                        $pointService = new PointService($pointData);
                        $pointService->changePoint();
                    } catch (\Exception $e) {
                        \Log::error('超级海报扫码注册的积分奖励出错:' . $e->getMessage());
                    }
                }

                //余额红包奖励
                if (!empty($poster->supplement->subscriber_bonus) && ($poster->supplement->bonus_method == 1)) {
                    $data = [
                        'member_id' => $subscriberMemberId,
                        'remark' => '超级海报: uid 为 ' . $subscriberMemberId . ' 的用户通过扫描 uid 为 ' . $recommendMemberId . ' 的用户的二维码注册商城, 获得余额奖励' . $poster->supplement->subscriber_bonus . '元',
                        'source' => ConstService::SOURCE_AWARD,
                        'relation' => '',
                        'operator' => 1,
                        'operator_id' => $posterId,
                        'change_value' => $poster->supplement->subscriber_bonus, //奖励金额
                    ];
                    try {
                        (new BalanceChange())->award($data);
                    } catch (\Exception $e) {
                        \Log::error('超级海报扫码注册的余额红包奖励出错:' . $e->getMessage());
                    }
                }

                //微信红包奖励
                if (!empty($poster->supplement->subscriber_bonus) && ($poster->supplement->bonus_method == 2)) {

                    $wechatPay = new WechatPay;
                    $pay = \Setting::get('shop.pay');
                    $shop_name = \Setting::get('shop.shop.name');
                    $notify_url = ''; //回调地址
                    $app = $wechatPay->getEasyWeChatApp($pay, $notify_url);
                    $luckyMoney = $app->lucky_money;

                    $luckyMoneyData = [
                        'mch_billno' => $pay['weixin_mchid'] . date('YmdHis') . rand(1000, 9999),
                        'send_name' => $shop_name,
                        're_openid' => $subscriberOpenid,
                        'total_num' => 1,
                        'total_amount' => $poster->supplement->subscriber_bonus * 100,
                        'wishing' => '超级海报关注注册奖励',
                        'client_ip' => \Request::getClientIp(),
                        'act_name' => '超级海报关注注册奖励',
                        'remark' => '超级海报: 扫码者奖励 ' . $poster->supplement->subscriber_bonus . ' 元',
                    ];

                    try {
                        $result = $luckyMoney->sendNormal($luckyMoneyData);
                    } catch (\Exception $e) {
                        \Log::error('超级海报扫码注册的微信红包奖励出错:' . $e->getMessage());
                    }
                }

                //优惠券奖励
                if (!empty($poster->supplement->subscriber_coupon_id) && $poster->supplement->subscriber_coupon_num > 0) {
                    $couponData = [
                        'uniacid' => $uniacid,
                        'uid' => $subscriberMemberId,
                        'coupon_id' => $poster->supplement->subscriber_coupon_id,
                        'get_type' => 3,
                        'used' => 0,
                        'get_time' => strtotime('now'),
                    ];
                    for ($i = 0; $i < $poster->supplement->subscriber_coupon_num; $i++) {
                        $memberCoupon = new MemberCoupon;
                        $res = $memberCoupon->create($couponData);
                        if ($res) { //发放优惠券成功
                            $log = '超级海报优惠券奖励: 奖励 ' . $poster->supplement->subscriber_coupon_num . ' 张优惠券( ID为 ' . $couponData['coupon_id'] . ' )给用户( Member ID 为 ' . $couponData['uid'] . ' )';
                            //超级海报优惠券奖励：奖励1张优惠券（ID为：123）给用户（ID为：12）
                        } else { //发放优惠券失败
                            $log = '超级海报优惠券奖励: 奖励优惠券( ID为 ' . $couponData['coupon_id'] . ' )给用户( Member ID 为 ' . $couponData['uid'] . ' )时失败!';
                        }
                        $logData = [
                            'uniacid' => $uniacid,
                            'logno' => $log,
                            'member_id' => $couponData['uid'],
                            'couponid' => $couponData['coupon_id'],
                            'paystatus' => 0,
                            'creditstatus' => 0,
                            'paytype' => 0,
                            'getfrom' => 0,
                            'status' => 0,
                            'createtime' => time(),
                        ];
                        CouponLog::create($logData);
                    }
                }

                if ($poster->supplement->subscriber_credit != 0 || $poster->supplement->subscriber_bonus != 0 || $poster->supplement->subscriber_coupon_num != 0) {
                    //奖励通知
                    $notice = $poster->supplement->subscriber_award_notice;
                    if (empty($notice)) {
                        $notice = "您扫描了 \"[nickname]\" 的二维码关注了公众号, 因此获得了 [credit] 个积分 [money] 元奖励!";
                        if (!empty($poster->supplement->subscriber_coupon_num)) {
                            $notice .= ' 以及 "[couponname]" 优惠券 [couponnum] 张!';
                        }
                    }
                    $notice = self::dynamicName($recommender->nickname, $notice);
                    $notice = self::dynamicAward($poster, $notice, 'subscriber');
                    $news = new News([
                        'title' => !empty($poster->supplement->subscriber_award_title) ? $poster->supplement->subscriber_award_title : '关注奖励通知',
                        'description' => $notice,
                        'url' => $poster->response_url ?: yzAppFullUrl('home')
                    ]);
                    try {
                        Message::sendNotice($subscriberOpenid, $news);
                    } catch (\Exception $e) {
                        \Log::error('超级海报, 奖励通知时出错:' . $e->getMessage());
                    }
                }
            }

            if ($recommendMemberId != $subscriberMemberId) {
                //奖励记录
                $data = array(
                    'uniacid' => $uniacid,
                    'poster_id' => $poster->id,
                    'subscriber_memberid' => $subscriberMemberId,
                    'recommender_memberid' => $recommendMemberId,
                    'recommender_credit' => $poster->supplement->recommender_credit,
                    'recommender_bonus' => $poster->supplement->recommender_bonus,
                    'recommender_coupon_id' => $poster->supplement->recommender_coupon_id,
                    'recommender_coupon_num' => $poster->supplement->recommender_coupon_num,
                    'subscriber_credit' => $poster->supplement->subscriber_credit,
                    'subscriber_bonus' => $poster->supplement->subscriber_bonus,
                    'subscriber_coupon_id' => $poster->supplement->subscriber_coupon_id,
                    'subscriber_coupon_num' => $poster->supplement->subscriber_coupon_num,
                );
                PosterAward::create($data);
            }


        } else {
            $subscriberMemberId = $member_shop_info_model->member_id;
            $is_register = 1;//记录之前有没有注册过
            //考虑这种场景:
            //"丙"之前已经被"甲"引导注册(比如分享链接), 但是还未锁定成为"甲"的下线,
            //当"丙"扫描"乙"的海报时, 要重新以"乙"做为"丙"的上线来考虑, 而不是"甲"做为上线
            if ($member_shop_info_model->inviter != 1) { //inviter 为 0 表示未锁定下线, 为 1 表示已经锁定下线;
                Member::chkAgent($subscriberMemberId, $recommendMemberId);
            }
        }

        //记录到芸众插件的扫码记录
        $data = [
            'uniacid' => $uniacid,
            'poster_id' => $poster->id,
            'subscriber_memberid' => $subscriberMemberId,
            'recommender_memberid' => $recommendMemberId,
            'event_type' => $eventType,
            'sign_up_this_time' => isset($flag) ? $flag : 0,
            'is_register' => $is_register
        ];
        PosterScan::create($data);

        //如果后台设定的"推送标题"为空, 则不推送图文消息
        if (empty($poster->response_title)) {
            return ResponseDefaultService::index();
        }

        //推送微信图文消息
        $news = array(
            'type' => 'news',
            'content' => array(
                'title' => $poster->response_title,
                'picurl' => $poster->response_thumb,
                'url' => $poster->response_url ?: yzAppFullUrl('home'),
                'description' => $poster->response_desc,
            ),
        );
        return $news;
    }

    //获取已关注用户的基本信息
    public static function getWechatUserBasicInfo($openid)
    {
        $options = self::wechatConfig();
        $app = new Application($options);
        $userService = $app->user;
        $userInfo = $userService->get($openid);

        return $userInfo;
    }

}
