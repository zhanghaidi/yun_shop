<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/27
 * Time: 下午5:43
 */

namespace Yunshop\Commission\Listener;

use app\common\facades\Setting;
use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Commission\models\CommissionOrder;

class OrderCanceledListener
{
    public static $uniqueAccountId = 0;

    public function __construct()
    {
        self::$uniqueAccountId = \YunShop::app()->uniacid;
    }
    public function subscribe(Dispatcher $events)
    {

        $events->listen(\app\common\events\order\AfterOrderCanceledEvent::class, function ($event) {
            //订单model
            $model = $event->getOrderModel();
            $config = \app\common\modules\shop\ShopConfig::current()->get('plugin.commission');
            $data = [
                'status' => '-1',
            ];

            $orderData = CommissionOrder::getOrderByOrderId($config['order_class'],$model->id)->first();
            if ($orderData->status != 2) {
                CommissionOrder::updatedOrderStatus($config['order_class'], $model->id, $data);
            } else {
                return;
            }
        });
    }
}