<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/3/22
 * Time: 3:07 PM
 */

namespace Yunshop\Commission\Listener;


use app\common\events\withdraw\WithdrawPayedEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Commission\models\Agents;

class WithdrawPayedListener
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(WithdrawPayedEvent::class, self::class . "@withdrawPayed");
    }

    /**
     * 收入提现打款完成更新已打款佣金统计
     *
     * @param WithdrawPayedEvent $event
     * @return bool
     */
    public function withdrawPayed($event)
    {
        $withdrawModel = $event->getWithdrawModel();

        $commissionConfigs = \app\backend\modules\income\Income::current()->getItem('commission');

        if($withdrawModel->type == $commissionConfigs['class']){
            Agents::addPayCommission($withdrawModel->member_id, $withdrawModel->actual_amounts);
        }
        return true;
    }

}
