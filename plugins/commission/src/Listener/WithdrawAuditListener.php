<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/3/26
 * Time: 11:47 AM
 */

namespace Yunshop\Commission\Listener;


use app\common\events\withdraw\WithdrawAppliedEvent;
use app\common\events\withdraw\WithdrawAuditedEvent;
use app\common\models\Withdraw;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\Income;

class WithdrawAuditListener
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(WithdrawAuditedEvent::class, self::class . "@withdrawAudited");
    }


    /**
     * 收入提现完成更改分销订单佣金状态
     *
     * @param WithdrawAppliedEvent $event
     * @return bool
     */
    public function withdrawAudited($event)
    {
        $withdrawModel = $event->getWithdrawModel();

        if ($withdrawModel->type != CommissionOrder::class) {
            return true;
        }

        if (count($withdrawModel->audit_ids) > 0) {
            $this->withdrawAudit($withdrawModel->audit_ids);
        }
        //如果存在驳回记录
        if (count($withdrawModel->rebut_ids) > 0) {
            $this->withdrawRebut($withdrawModel->rebut_ids);
        }
        //如果存在无效记录
        if (count($withdrawModel->invalid_ids) > 0) {
            $this->withdrawInvalid($withdrawModel->invalid_ids);
        }
        return true;
    }

    private function withdrawAudit(array $incomeIds)
    {
        $commissionOrderIds = $this->getCommissionOrderIds($incomeIds);

        CommissionOrder::whereIn('id', $commissionOrderIds)->update(['status' => 2]);
    }

    private function withdrawRebut(array $incomeIds)
    {
        $commissionOrderIds = $this->getCommissionOrderIds($incomeIds);

        CommissionOrder::whereIn('id', $commissionOrderIds)->update(['withdraw' => 0]);
    }

    private function withdrawInvalid(array $incomeIds)
    {
        $commissionOrderIds = $this->getCommissionOrderIds($incomeIds);

        CommissionOrder::whereIn('id', $commissionOrderIds)->update(['status' => -1]);
    }

    private function getCommissionOrderIds(array $incomeIds)
    {
        $incomeModels = Income::whereIn('id', $incomeIds)->get();

        $commissionOrderIds = [];
        foreach ($incomeModels as $key => $incomeModel) {
            $commissionOrderIds[] = $incomeModel->incometable_id;
        }
        return $commissionOrderIds;
    }

}
