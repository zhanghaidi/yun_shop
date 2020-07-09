<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/30 下午3:25
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Listeners;

use Illuminate\Contracts\Events\Dispatcher;
use app\common\exceptions\AppException;
use Yunshop\Love\Common\Services\LoveGiveService;
use Yunshop\Love\Common\Events\LoveWithdrawAppliedEvent;
use Yunshop\Love\Common\Models\LoveWithdrawRecords;
use Yunshop\Love\Common\Services\SetService;
use app\common\services\finance\PointService;
use \app\common\models\Member;

class LoveWithdrawListener
{
    /**
     * @var LoveWithdrawRecords
     */
    private $loveWithdrawModel;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(LoveWithdrawAppliedEvent::class, static::class . '@deductionOfIntegrals');
    }

    //爱心值提现扣除积分
    public function deductionOfIntegrals($event)
    {

        $this->loveWithdrawModel = $event->getLoveWithdrawModel();
        if (SetService::getLoveSet('proportion_switch') == 1) {
            if ($this->loveWithdrawModel->path == 2) {
                \Log::debug('爱心值提现扣除积分', $this->loveWithdrawModel);
                $integral_withdraw_proportion = SetService::getLoveSet('integral_withdraw_proportion');
                $scle = SetService::getLoveSet('integral_withdraw_scale');

                //$reward_points = ($this->loveWithdrawModel->love_value * $integral_withdraw_proportion/100);

                $processing_fee_ratio = bcdiv($integral_withdraw_proportion, 100, 4);
                $reward_points = bcmul($this->loveWithdrawModel->love_value, $processing_fee_ratio, 2);

                $member_id = $this->loveWithdrawModel->member_id;
                $memberModel = Member::getMemberById($member_id);
                $credit1 = $memberModel->credit1;

                if ($reward_points < $credit1 && SetService::getLoveSet('proportion_switch') == 1) {
                    $pointData = array(
                        'uniacid'           => \YunShop::app()->uniacid,
                        'point_income_type' => PointService::POINT_INCOME_LOSE,
                        'member_id'         => $this->loveWithdrawModel->member_id,
                        'point_mode'        => PointService::POINT_MODE_LOVE_WITHDRAWAL_DEDUCTION,
                        'point'             => -$reward_points,
                        'remark'            => '------会员ID为' . $this->loveWithdrawModel->member_id . 'love提现到消费积分扣除积分' . $reward_points . '个',
                    );
                    try {
                        \Log::debug('爱心值提现扣除积分', $pointData);
                        $pointService = new PointService($pointData);
                        $pointService->changePoint();
                    } catch (\Exception $e) {
                        \Log::error('手续费扣除积分出错:' . $e->getMessage());
                        throw new AppException('提现失败:手续费扣除积分出错');
                    }
                } else {
                    throw new AppException('提现失败:积分不足扣除手续费');
                }
            }
        }


    }

}
