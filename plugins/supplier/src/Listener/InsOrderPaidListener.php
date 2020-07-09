<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-03-27
 * Time: 17:19
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Supplier\Listener;


use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\models\Order;
use app\common\models\PayType;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\Supplier\common\models\Insurance;

class InsOrderPaidListener
{
    use DispatchesJobs;

    public $order;
    public $event;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderPaidImmediatelyEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderPaidImmediatelyEvent $event)
    {
        $this->event = $event;
        $this->order = Order::find($event->getOrderModel()->id);
\Log::debug('----------保单支付订单内容---------', $this->order);
        if ($this->order->plugin_id != 93) {
            return;
        }

        $insurance_orders = $this->order->hasManyInsOrder;
\Log::debug('----------保单支付订单关联关系---------', $insurance_orders);
        $ids = [];
        foreach ($insurance_orders as $item) {
            $ids[] = $item['ins_id'];
        }
\Log::debug('----------保单支付保单id---------', $ids);
        $pay_type_name = PayType::get_pay_type_name($this->order->pay_type_id);

        foreach ($ids as $id) {
            $insurance = Insurance::find($id);
            $insurance->update(['is_pay' => 1, 'pay_type' => $pay_type_name, 'pay_time' => time()]);
        }

        return;
    }
}