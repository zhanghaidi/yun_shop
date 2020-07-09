<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/4/4
 * Time: 15:23
 */

namespace Yunshop\Commission\services;

use app\backend\modules\member\models\MemberRecord;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\Order;
use Carbon\Carbon;
use Yunshop\Commission\models\CommissionOrder;

class FixService
{

    public $set;
    public function handle($set)
    {
        $this->set = $set;
//        $this->fixNotCommission();
        $this->fixCommissionOrder();
    }
    /**
     * 修复无分红订单
     */
    public function fixNotCommission()
    {
        $start_time = Carbon::now()->subMinute(15)->timestamp;
        $end_time = Carbon::now()->subMinute(5)->timestamp;
        $orders = Order::uniacid()->whereBetween('created_at',[$start_time,$end_time])->whereDoesntHave('hasOneCommission')->get();
        if (!$orders->isEmpty()) {
            foreach ($orders as $order) {
                $agent = Agents::where('member_id', $order->uid)->where('created_at', '>', $order->created_at)->first();
                $member_record = MemberRecord::uniacid()
                    ->where('uid', $order->uid)
                    ->where('updated_at', '>', $order->created_at)
                    ->orderBy('id', 'desc')
                    ->first();
                if($agent || $member_record) {
                   return;
                }
                
                (new \Yunshop\Commission\Listener\OrderCreatedListener())->handler($order);
                \Log::info('分销订单-补'.$order->id);
            }
            return;
        }

    }

    /**
     * 修复未结算订单
     * @return bool
     */
    public function fixCommissionOrder()
    {
        $success = 0;
        $waitCommissionOrder = CommissionOrder::uniacid()
            ->whereNull('recrive_at')
            ->whereStatus(0)
            ->with('order')
            ->orderBy('id','desc')
            ->limit(500)
            ->get();

        if (!$waitCommissionOrder->isEmpty()) {

            $status = $this->set['settlement_event'] ? 1 : 3;
            foreach ($waitCommissionOrder as $key => $commissionOrder) {

                $orderModel = $commissionOrder->order;

                if ($orderModel->status >= $status) {

                    $commissionOrder->status = 1;

                    $commissionOrder->recrive_at = time();

                    if ($commissionOrder->save()) {
                        $success += 1;
                    }
                }
                unset($orderModel);
            }
        }
        \Log::info('分销订单'.\Yunshop::app()->uniacid.'-结'.$success);
        return true;
    }

}