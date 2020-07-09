<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/18 下午5:49
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Modules\Love\Listeners;


use app\common\events\withdraw\WithdrawPayedEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Yunshop\Love\Common\Services\ConstService;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Love\Common\Services\SetService;

class WithdrawListener
{

    public function subscribe(Dispatcher $event)
    {
        $event->listen(WithdrawPayedEvent::class,self::class."@incomeWithdrawAward");
    }


    /**
     * @param WithdrawPayedEvent $event
     */
    public function incomeWithdrawAward($event)
    {
        //如果提现奖励关闭，直接返回
        if (!SetService::getWithdrawAwardStatus()) {
            return;
        }
        $withdrawModel = $event->getWithdrawModel();

        DB::beginTransaction();
        $result = (new LoveChangeService('usable'))->withdrawAward($this->getLoveChangeData($withdrawModel));
        if ($result !== true) {
            \Log::debug('收入提现奖励爱心值失败，收入提现ID'.$withdrawModel->id.',应奖励爱心值' . $withdrawModel->actual_poundage);
            DB::rollBack();
        }
        \Log::debug('收入提现奖励爱心值成功，收入提现ID'.$withdrawModel->id.',应奖励爱心值' . $withdrawModel->actual_poundage);
        DB::commit();
    }

    private function getLoveChangeData($withdrawModel)
    {
        $love = SetService::getLoveName();
        return [
            'member_id'         => $withdrawModel->member_id,
            'change_value'      => $withdrawModel->actual_poundage,
            'operator'          => ConstService::OPERATOR_SHOP,
            'operator_id'       => \YunShop::app()->uid ?: '0',
            'remark'            => '收入提现奖励手续费等值' . $love . $withdrawModel->actual_poundage,
            'relation'          => ''
        ];
    }

}
