<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-07-16
 * Time: 19:34
 */

namespace Yunshop\LuckyDraw\common\services;


use app\common\models\MemberCoupon;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\finance\PointService;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Love\Common\Services\SetService;

class DrawRewardService
{
    protected $member_id;
    protected $activityModel;
    protected $uniacid;

    public function __construct($member_id, $activityModel, $uniacid)
    {
        $this->member_id = $member_id;
        $this->activityModel = $activityModel;
        $this->uniacid = $uniacid;
    }

    public function doReward()
    {
        if ($this->activityModel->partake_coupon_id != 0) {
            $this->rewardCoupon();
        }
        if ($this->activityModel->partake_point != 0) {
            $this->rewardPoint();
        }
        if ($this->activityModel->partake_love != 0) {
            $this->rewardLove();
        }
        if ($this->activityModel->partake_amount != 0) {
            $this->rewardAmount();
        }
    }

    public function rewardCoupon()
    {
        $data = [
            'uniacid' => $this->uniacid,
            'uid' => $this->member_id,
            'coupon_id' => $this->activityModel->partake_coupon_id,
            'get_type' => 4,
            'get_time' => time(),
        ];

        $memberCoupon = new MemberCoupon();
        $memberCoupon->fill($data);
        $memberCoupon->save();
    }

    public function rewardPoint()
    {
        $data = [
            'point_mode' => PointService::POINT_MODE_DRAW_REWARD_GET,
            'member_id' => $this->member_id,
            'point' => $this->activityModel->partake_point,
            'remark' => '[会员ID:'.$this->member_id.'参与活动:'.$this->activityModel->name.'获得积分'.$this->activityModel->partake_point.']',
            'point_income_type' => PointService::POINT_INCOME_GET
        ];

        $pointService = new PointService($data);
        $pointService->changePoint();
    }

    public function rewardLove()
    {
        if (app('plugins')->isEnabled('love')) {
            $love_name = SetService::getLoveName();
            $data = [
                'member_id' => $this->member_id,
                'change_value' => $this->activityModel->partake_love,
                'operator' => 0,
                'operator_id' => 0,
                'remark' => '[会员ID:'.$this->member_id.'参与活动:'.$this->activityModel->name.'获得'.$love_name.$this->activityModel->partake_love.']',
                'relation' => ''
            ];

            (new LoveChangeService())->DrawReward($data);
        }
    }

    public function rewardAmount()
    {
        $data = array(
            'member_id' => $this->member_id,
            'remark' => '[会员ID:' . $this->member_id . '参与活动:' .$this->activityModel->name.'获得'.$this->activityModel->partake_amount.'元]',
            'source' => ConstService::SOURCE_DRAW_REWARD,
            'relation' => '',
            'operator' => ConstService::OPERATOR_SHOP,
            'operator_id' => $this->member_id,
            'change_value' => $this->activityModel->partake_amount,
        );

        (new BalanceChange())->DrawReward($data);
    }
}