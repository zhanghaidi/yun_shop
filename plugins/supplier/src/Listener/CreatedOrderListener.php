<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午2:14
 */
namespace Yunshop\Supplier\Listener;
use app\common\events\order\AfterOrderCreatedEvent;

use app\common\events\order\AfterOrderCreatedImmediatelyEvent;
use app\frontend\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Printer\common\services\PrintingService;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\models\SupplierGoods;
use Yunshop\Supplier\common\models\SupplierOrder;

class CreatedOrderListener
{
    /**
     * 插入供应商订单
     *
     * @param  Dispatcher $events
     * @return mixed
     */
    public function subscribe(Dispatcher $events)
    {

        $events->listen(AfterOrderCreatedImmediatelyEvent::class, function($event) {

            $order = $event->getOrderModel();
            if(!$order->plugin_id == \Yunshop\Supplier\common\models\Supplier::PLUGIN_ID){
                return ;
            }

            $supplierOrder = SupplierOrder::where('order_id',$order->id)->first();

            \Log::info('supplier_id:',$supplierOrder->supplier_id);
            if (app('plugins')->isEnabled('printer')) {
                \app\common\modules\shop\ShopConfig::current()->set('printer_owner', [
                    'owner' => Supplier::PLUGIN_ID,
                    'owner_id' => $supplierOrder->supplier_id
                ]);
                new PrintingService($order, 1, '供应商下单打印');
            }
        });
    }
}