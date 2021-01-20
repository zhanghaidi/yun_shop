<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-06-12
 * Time: 09:44
 */

namespace Yunshop\LuckyDraw\common\services;



use app\common\models\MemberCoupon;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\finance\PointService;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Love\Common\Services\SetService;

class DrawRechargeService
{
    protected $prizeModel;
    protected $member_id;
    protected $uniacid;

    public function __construct($prizeModel, $member_id, $uniacid)
    {
        $this->prizeModel = $prizeModel;
        $this->member_id = $member_id;
        $this->uniacid = $uniacid;
    }

    public function chargeOfType()
    {
        $type = $this->prizeModel->type;

        switch ($type) {
            case 1 :
                $this->takeCoupon();
                break;
            case 2 :
                $this->chargePoint();
                break;
            case 3 :
                $this->chargeLove();
                break;
            case 4 :
                $this->chargeAmount();
                break;
        }
    }

    public function takeCoupon()
    {
        $data = [
            'uniacid' => $this->uniacid,
            'uid' => $this->member_id,
            'coupon_id' => $this->prizeModel->coupon_id,
            'get_type' => 4,
            'get_time' => time(),
        ];

        $memberCoupon = new MemberCoupon();
        $memberCoupon->fill($data);
        $memberCoupon->save();
    }

    public function chargePoint()
    {
        $data = [
            'point_mode' => PointService::POINT_MODE_DRAW_CHARGE_GET,
            'member_id' => $this->member_id,
            'point' => $this->prizeModel->point,
            'remark' => '[会员ID:'.$this->member_id.'抽中:'.$this->prizeModel->name.'获得积分'.$this->prizeModel->point.']',
            'point_income_type' => PointService::POINT_INCOME_GET
        ];

        $pointService = new PointService($data);
        $pointService->changePoint();
    }

    public function chargeLove()
    {
        if (app('plugins')->isEnabled('love')) {
            $love_name = SetService::getLoveName();
            $data = [
                'member_id' => $this->member_id,
                'change_value' => $this->prizeModel->love,
                'operator' => 0,
                'operator_id' => 0,
                'remark' => '[会员ID:'.$this->member_id.'抽中:'.$this->prizeModel->name.'获得'.$love_name.$this->prizeModel->love.']',
                'relation' => ''
            ];

            (new LoveChangeService())->DrawGet($data);
        }
    }

    public function chargeAmount()
    {
        $data = array(
            'member_id' => $this->member_id,
            'remark' => '[会员ID:' . $this->member_id . '抽中:' .$this->prizeModel->name.'获得'.$this->prizeModel->amount.'元]',
            'source' => ConstService::SOURCE_DRAW_CHARGE,
            'relation' => '',
            'operator' => ConstService::OPERATOR_SHOP,
            'operator_id' => $this->member_id,
            'change_value' => $this->prizeModel->amount,
        );

        (new BalanceChange())->DrawGet($data);
    }
}