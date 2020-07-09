<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/29
 * Time: 下午3:13
 */

namespace Yunshop\Supplier\Listener;

use app\common\events\order\AfterOrderPaidEvent;
use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Printer\common\services\PrintingService;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\models\SupplierOrder;

class PayedOrderListener
{

    public $order;

    public function subscribe(Dispatcher $events)
    {
//        $events->listen(AfterOrderPaidEvent::class, function($event) {
//            $order = $event->getOrderModel();
//            $supplierOrder = SupplierOrder::where('order_id',$order->id)->first();
//            \Log::info('supplier_id:',$supplierOrder->supplier_id);
//            if (app('plugins')->isEnabled('printer')) {
//                if (app('plugins')->isEnabled('printer')) {
//                    \app\common\modules\shop\ShopConfig::current()->set('printer_owner', [
//                        'owner' => Supplier::PLUGIN_ID,
//                        'owner_id' => $supplierOrder->supplier_id
//                    ]);
//                    new PrintingService($order, 2, '供应商付款打印');
//                }
//            }
//        });

        $events->listen(AfterOrderPaidEvent::class, self::class.'@handle');
    }
    
    public function handle(AfterOrderPaidEvent $event)
    {
        $order = $event->getOrderModel();
        $supplierOrder = SupplierOrder::where('order_id',$order->id)->first();
        \Log::info('supplier_id:',$supplierOrder->supplier_id);

        if (app('plugins')->isEnabled('printer')) {
            \app\common\modules\shop\ShopConfig::current()->set('printer_owner', [
                'owner' => Supplier::PLUGIN_ID,
                'owner_id' => $supplierOrder->supplier_id
            ]);
            if (class_exists(PrintingService::class)) {
                new PrintingService($order, 2, '供应商付款打印');
            }
        }
    }
}