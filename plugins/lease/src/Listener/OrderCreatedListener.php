<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/29
 */
namespace Yunshop\LeaseToy\Listener;

use app\common\events\order\AfterOrderCreatedEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use app\common\models\Order;
use Yunshop\LeaseToy\models\LeaseOrderModel;
use Yunshop\LeaseToy\models\RightsLogModel;
use Yunshop\LeaseToy\models\orderGoods\LeaseToyOrderGoodsModel;
use Illuminate\Support\Facades\DB;


class OrderCreatedListener
{
    use DispatchesJobs;

    protected $event;
    protected $order;

  public function subscribe(Dispatcher $events)
  {
    $events->listen(AfterOrderCreatedEvent::class, self::class . '@handle');
  }

  public function handle(AfterOrderCreatedEvent $event)
  {
    $this->event = $event->getOrderModel();
    $this->order = Order::find($event->getOrderModel()->id);

     if ($this->order->plugin_id == LeaseOrderModel::PLUGIN_ID) {
            $this->setMemberRightsLog();
     }

  }

  public function setMemberRightsLog()
  {
    $rights = LeaseToyOrderGoodsModel::where('order_id', $this->order->id)->first([
            DB::raw('SUM(lease_rent_free) as sue_rent_free'),
            DB::raw('SUM(lease_deposit_free) as sue_deposit_free')
        ])->toArray();

    $data = [
        'uniacid' => \YunShop::app()->uniacid,
        'member_id' => $this->order->uid,
        'order_id' => $this->order->id,
        'sue_rent_free' => $rights['sue_rent_free'] ? $rights['sue_rent_free'] : 0,
        'sue_deposit_free' =>  $rights['sue_deposit_free'] ? $rights['sue_deposit_free'] : 0,
        'created_at' => time(),
        'updated_at' => time(),
    ];

    return RightsLogModel::insert($data);
  }


}