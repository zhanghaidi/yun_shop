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
use app\common\events\withdraw\WithdrawAppliedEvent;
use Yunshop\Love\Common\Services\SetService;
use app\common\services\finance\PointService;
use \app\common\models\Member;

class WithdrawAppliedListener
{
    /**
     * @var LoveWithdrawRecords
     */
    private $withdraw;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(WithdrawAppliedEvent::class,static::class.'@deductionOfIntegrals');
    }

    //爱心值提现扣除积分
    public function deductionOfIntegrals($event)
    {
        $this->withdraw = $event->getWithdrawModel();
        if( SetService::getLoveSet('proportion_switch') == 1 && $this->withdraw->type == \Yunshop\Love\Common\Models\LoveWithdrawRecords::class){
            $member_id = $this->withdraw->member_id;
            $memberModel = Member::getMemberById($member_id);
            $credit1 = $memberModel->credit1;
            if( $this->withdraw->poundage < $credit1){
                \Log::debug('爱心值提现扣除积分',$this->withdraw);

                $reward_points  = $this->withdraw->poundage;

                $pointData = array(
                    'uniacid' => \YunShop::app()->uniacid,
                    'point_income_type' => PointService::POINT_INCOME_LOSE,
                    'member_id' => $member_id,
                    'point_mode' => PointService::POINT_MODE_LOVE_WITHDRAWAL_DEDUCTION,
                    'point' => -$reward_points,
                    'remark' => '------会员ID为'.$member_id.'love提现到消费积分扣除积分'.$reward_points.'个',
                );
                try {
                    $pointService = new PointService($pointData);
                    $pointService->changePoint();

                } catch (\Exception $e) {
                    \Log::error('手续费扣除积分出错:' . $e->getMessage());
                    throw new AppException('提现失败:扣除手续费出错');
                }
            }else{
                throw new AppException('提现失败:积分不足以扣除手续费');
            }
        }


    }

}
