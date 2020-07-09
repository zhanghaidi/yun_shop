<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/26 上午11:42
 * Email: livsyitian@163.com
 */

namespace Yunshop\Commission\Listener;


use app\common\events\withdraw\WithdrawAppliedEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\Income;

class WithdrawApplyListener
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(WithdrawAppliedEvent::class, self::class . "@withdrawApplied");
    }


    /**
     * 收入提现完成更改分销订单佣金状态
     *
     * @param WithdrawAppliedEvent $event
     * @return bool
     */
    public function withdrawApplied($event)
    {
        $withdrawModel = $event->getWithdrawModel();

        if ($withdrawModel->mark != 'commission') {
            return true;
        }

        $income_ids = explode(',', $withdrawModel->type_id);
        if (count($income_ids) <= 0) {
            return true;
        }

        $incomeModels = Income::whereIn('id', $income_ids)->get();

        $commission_order_ids = [];
        foreach ($incomeModels as $key => $incomeModel) {
            $commission_order_ids[] = $incomeModel->incometable_id;
        }
        CommissionOrder::whereIn('id', $commission_order_ids)->update(['withdraw' => 1]);
        return true;
    }

}
