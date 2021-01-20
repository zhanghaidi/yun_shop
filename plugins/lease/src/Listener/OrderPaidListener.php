<?php

/**
 * 订单支付后
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/16
 * Time: 下午2:20
 */
namespace Yunshop\LeaseToy\Listener;

use app\common\events\order\AfterOrderPaidEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\common\models\Order;
use Yunshop\LeaseToy\models\LeaseOrderModel;
use Yunshop\LeaseToy\models\LeaseMemberModel;

class OrderPaidListener
{
    use DispatchesJobs;
    
    protected $event;
    protected $order;
    protected $leaseOrder;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderPaidEvent::class, self::class. '@handle');
    }

    public function handle(AfterOrderPaidEvent $event)
    {
        $this->event = $event;
        $this->order = Order::find($event->getOrderModel()->id);

        if ($this->order->plugin_id == LeaseOrderModel::PLUGIN_ID) {
            
            $this->leaseOrder = LeaseOrderModel::where('order_id',$this->order->id)->first();

            if (empty($this->leaseOrder)) {
                return;
            }

            //更新租赁订单
            $this->updateLeaseOrder();

            //用户押金管理
            $this->addLeaseMember();
        }

    }

    public function addLeaseMember()
    {
        $leaseMember =  LeaseMemberModel::where('member_id', $this->order->uid)->first();
        if ($leaseMember) {
            $leaseMember->total_deposit += $this->leaseOrder->deposit_total;
        } else {
            $leaseMember = new LeaseMemberModel([
                'member_id' => $this->order->uid,
                'total_deposit' => $this->leaseOrder->deposit_total,
                'uniacid' => $this->leaseOrder->uniacid,
            ]);
        }

        return $leaseMember->save();

    }

    public function updateLeaseOrder()
    {
        $leaseOrderModel = $this->leaseOrder;

        $leaseOrderModel->start_time = strtotime($this->order->pay_time);
        
        //一天
        $daysTime = 24*60*60;

        $leaseOrderModel->end_time = strtotime($this->order->pay_time) + ($daysTime * $leaseOrderModel->return_days);
        
        return $leaseOrderModel->save();
    }
}