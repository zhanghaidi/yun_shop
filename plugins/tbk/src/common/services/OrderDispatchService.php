<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/10
 * Time: 17:54
 */

namespace Yunshop\Tbk\common\services;


use app\common\models\UniAccount;
use Yunshop\Tbk\common\jobs\OrderSynJob;
use Yunshop\Tbk\common\models\TbkOrder;

class OrderDispatchService
{
    public function handle()
    {
        set_time_limit(0);
        \Log::info('爱心值激活队列开始');
        //$this->dispatch(new LoveActivation());
        $this->handleQueue();
    }


    private function handleQueue()
    {
        $uniAccount = UniAccount::get() ?: [];
        foreach ($uniAccount as $u) {

            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;

            $orders = TbkOrder::where(["yz_order_status" => 0, "is_queue" => 0])->get();
            $orders = $orders->groupBy("order_sn");

            foreach($orders as $order) {
                dispatch(new OrderSynJob($order));
                $order->update(["is_queue" => 1]);
            }
        }
    }
}