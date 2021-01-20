<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/29
 */
namespace Yunshop\LeaseToy\Listener;

use app\common\events\order\AfterOrderCanceledEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\common\models\Order;
use Yunshop\LeaseToy\models\LeaseOrderModel;
use Yunshop\LeaseToy\models\RightsLogModel;
use Illuminate\Support\Facades\DB;


class OrderCanceledListener
{
    use DispatchesJobs;

    protected $event;
    protected $order;

  public function subscribe(Dispatcher $events)
  {
    $events->listen(AfterOrderCanceledEvent::class, self::class . '@handle');
  }

  public function handle(AfterOrderCanceledEvent $event)
  {
    $this->event = $event->getOrderModel();
    $this->order = Order::find($event->getOrderModel()->id);

     if ($this->order->plugin_id == LeaseOrderModel::PLUGIN_ID) {
        
        $this->delMemberRightsLog();
     }

  }

  public function delMemberRightsLog()
  {
    return RightsLogModel::uniacid()->where('order_id', $this->order->id)->delete();
  }

}