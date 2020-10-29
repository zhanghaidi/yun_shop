<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/18
 * Time: 下午3:46
 */

namespace app\Jobs;


use app\common\events\order\AfterOrderCreatedEvent;
use app\common\facades\Setting;
use app\common\models\Order;
use app\frontend\modules\order\models\PreOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderCreatedEventQueueJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var PreOrder
     */
    protected $order;

    /**
     * OrderCreatedEventQueueJob constructor.
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $this->order = Order::find($orderId);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('OrderCreatedEventQueueJob orderid:' . $this->order->id);
        DB::transaction(function () {
            \YunShop::app()->uniacid = $this->order->uniacid;
            Setting::$uniqueAccountId = $this->order->uniacid;
            if ($this->order->orderCreatedJob->status == 'finished') {
                return;
            }
            Log::info('OrderCreatedEventQueueJob orderid1:' . $this->order->id);
            $this->order->orderCreatedJob->status = 'finished';
            $this->order->orderCreatedJob->save();
            Log::info('OrderCreatedEventQueueJob orderid2:' . $this->order->id);
            $event = new AfterOrderCreatedEvent($this->order);
            event($event);
            Log::info('OrderCreatedEventQueueJob orderid3:' . $this->order->id);

        });
    }

}