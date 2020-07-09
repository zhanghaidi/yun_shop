<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/28 上午10:55
 * Email: livsyitian@163.com
 */

namespace Yunshop\Supplier\Listener;


use app\common\events\finance\AlipayWithdrawEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Supplier\common\models\SupplierWithdraw;

class WithdrawResultListener
{
    public function subscribe(Dispatcher $dispatcher)
    {
        /**
         * 提现申请，验证打款方式
         */
        $dispatcher->listen(AlipayWithdrawEvent::class, static::class . "@aliPayResult");
    }


    /**
     * @param $event AlipayWithdrawEvent
     */
    public function aliPayResult($event)
    {
        $withdraw_sn = $event->getTradeNo();

        $withdrawModel = SupplierWithdraw::where('apply_sn', $withdraw_sn)->where('status', 2)->first();

        if ($withdrawModel) {
            $withdrawModel->status = 3;
            $withdrawModel->pay_time = time();
            $withdrawModel->save();
        }
    }
}
