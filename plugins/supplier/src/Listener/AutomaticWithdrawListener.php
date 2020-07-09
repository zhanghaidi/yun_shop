<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/7/15
 * Time: 下午 02:01
 */

namespace Yunshop\Supplier\Listener;


use Yunshop\Supplier\common\events\SupplierAutomaticWithdrawEvent;
use Yunshop\Supplier\Jobs\AutomaticWithdrawJob;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\facades\Setting;

class AutomaticWithdrawListener
{
    public function subscribe(Dispatcher $dispatcher)
    {

        /**
         * 提现申请后，免审核任务
         */
        $dispatcher->listen(
            SupplierAutomaticWithdrawEvent::class,
            static::class . "@supplierWithdrawApplied",
            999
        );
    }

    /**
     * 提现申请后，免审核任务
     *
     * @param $event SupplierAutomaticWithdrawEvent
     */
    public function supplierWithdrawApplied($event)
    {
        $withdrawModel = $event->getWithdrawModel();

        $withdraw_set = $this->getWithdrawSet();
        if ($withdraw_set['audit_free'] == 1 && $withdrawModel->type == 5) {
            $job = new AutomaticWithdrawJob($withdrawModel);
            dispatch($job);
        }
    }

    private function getWithdrawSet()
    {
        return Setting::get('plugin.supplier');
    }

}