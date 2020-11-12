<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/16 下午3:04
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Frontend\Services;


use Yunshop\Sign\Common\Services\SetService;
use Yunshop\Sign\Frontend\Models\Sign;
use Yunshop\Sign\Frontend\Modules\Sign\Controllers\SignController;

class SignAwardService
{
    public $signModel;


    public function __construct()
    {
        $this->signModel = $this->getSignModel();
    }

    //今日签到奖励内容
    public function getSignAwardContent()
    {
        $award_point = $this->getAwardPoint();

        $award_coupon = $this->getAwardCouponNum();

        $award_love = (new SignController())->award_love;

        if (app('plugins')->isEnabled('love')) {
            return "积分：+ {$award_point}；优惠券：（{$award_coupon}）张；" . LOVE_NAME . "：+{$award_love}";
        }
        return "积分：+" . $award_point . "；优惠券：（" . $award_coupon . "）张";
    }


    /**
     * 签到获得奖励积分值
     *
     * @return string
     */
    public function getAwardPoint()
    {
        $every_award = $this->getEveryAwardPoint();
        $cumulative_award = $this->getCumulativeAwardPoint();

        return bcadd($every_award, $cumulative_award, 2);
    }


    /**
     * 签到获得奖励优惠券数量
     *
     * @return int
     */
    public function getAwardCouponNum()
    {
        $award_coupon = $this->getAwardCoupon();
        $coupon_num = 0;
        foreach ($award_coupon as $key => $item) {
            $coupon_num += $item['coupon_num'];
        }
        return $coupon_num;
    }


    /**
     * 签到获得奖励优惠券详情
     *
     * @return array
     */
    public function getAwardCoupon()
    {
        $every_award = $this->getEveryAwardCoupon();
        $cumulative_award = $this->getCumulativeAwardCoupon();

        return array_merge($every_award, $cumulative_award);
    }


    /**
     * 每日签到获得奖励积分值
     *
     * @return string
     */
    public function getEveryAwardPoint()
    {
        return SetService::getSignSet('award_point') ?: 0;
    }


    /**
     * 连续签到获得奖励积分值
     *
     * @return array|int
     */
    public function getCumulativeAwardPoint()
    {
        return $this->getCumulativeAwardByType(1);
    }


    /**
     * 每日签到获得奖励优惠券详情
     *
     * @return array
     */
    public function getEveryAwardCoupon()
    {
        $coupon_id = SetService::getSignSet('award_coupon_id') ?: 0;
        $coupon_num = SetService::getSignSet('award_coupon_num') ?: 0;

        if ($coupon_id && $coupon_num) {
            return [[
                'coupon_id'   => $coupon_id,
                'coupon_name' => SetService::getSignSet('award_coupon_name'),
                'coupon_num'  => $coupon_num
            ]];
        }
        return [];
    }


    /**
     * 连续签到获得奖励优惠券详情
     *
     * @return array|int
     */
    public function getCumulativeAwardCoupon()
    {
        return $this->getCumulativeAwardByType(2);
    }


    /**
     * 通过 $type 获取连续签到获得奖励内容
     * @param $type
     * @return array|int
     */
    private function getCumulativeAwardByType($type)
    {
        $cumulative_set = SetService::getSignSet('cumulative');

        if (!$cumulative_set) {
            return $type == 1 ? 0 : [];
        }

        $cumulative_number = $this->getCumulativeNumber();

        $point = 0;
        $coupons = array();
        foreach ($cumulative_set as $key => $item) {
//            fixBy-wk-20201106 签到累计发放奖励问题解决 余数为零 并且 倍数小于1
            if (fmod($cumulative_number, $item['days']) == 0 && intval($cumulative_number/$item['days']) == 1 ) {
                if ($type == 1 && $item['award_type'] == $type) {
                    $point += $item['award_value'];
                }
                if ($type == 2 && $item['award_type'] == $type && $item['coupon_id'] && $item['award_value']) {
                    $coupons[] = [
                        'coupon_id'   => $item['coupon_id'],
                        'coupon_name' => $item['coupon_name'],
                        'coupon_num'  => $item['award_value']
                    ];
                }

            }
        }
        return $type == 1 ? $point : $coupons;
    }


    /**
     * 连续签到值 + 本次签到
     *
     * @return int|mixed
     */
    public function getCumulativeNumber()
    {

        if ($this->signModel->cumulative) {
            return $this->signModel->cumulative_number + 1;
        }
        return 1;
    }


    /**
     * 会员签到 model
     *
     * @return Sign
     */
    private function getSignModel()
    {
        return Sign::withMember()->first() ?: new Sign();
    }

}
