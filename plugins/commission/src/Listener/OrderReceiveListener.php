<?php


namespace Yunshop\Commission\Listener;

use app\common\events\order\AfterOrderReceivedEvent;
use app\common\models\McMappingFans;
use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\Commission\Jobs\UpgrateByOrderJob;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\services\BecomeAgentService;
use Yunshop\Commission\services\MessageService;
use Yunshop\Commission\services\UpgradeService;

class OrderReceiveListener
{
    use DispatchesJobs;
    /**
     * 在这个方法里你可以做任何可以在 bootstrap.php 内做的事情
     *
     * @param  Dispatcher $events
     * @return mixed
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(\app\common\events\order\AfterOrderReceivedEvent::class, self::class . '@handle');
    }

    public function handle (AfterOrderReceivedEvent $event) {
        date_default_timezone_set("PRC");
        \Log::info('确认收货 分销监听开始->');
        //订单model
        $model = $event->getOrderModel();
        $buyMember = $model->belongsToMember;
        $buyMemberFans = McMappingFans::where('uid', $buyMember->uid)->first();
        $order = Order::find($model->id);

        // 升级数据
        \Log::debug('升级数据');

        //先验证是否成为分销商
        (new BecomeAgentService())->verification($model->uid, 1);
        $this->upgradeData($model);

        // 结算事件判断
        $config = \app\common\modules\shop\ShopConfig::current()->get('plugin.commission');

        //todo 注释原因：出现特殊情况，用户先设置为完成后——其中有未完成已支付的订单——再改设置为支付后。那些未完成已支付订单就完美避开结算设置
//        $set = \Setting::get('plugin.commission');
//        if ($set['settlement_event']) {
//            return;
//        }

        \Log::info('确认收货 分销监听开始-');

        //做自己的操作
        $commissionModelExisrs = CommissionOrder::getOrderByTypeId($config['order_class'], $model->id, '0')->exists();
        if (!$commissionModelExisrs) {
            \Log::info('确认收货 分销监听返回<-');
            return;
        }

        $this->noticeData($model, $order, $config, $buyMemberFans);

        $this->orderStatus($model, $config);

        \Log::info('确认收货 分销监听结束->');
    }
    /**
     * @param $model
     * @param $config
     */
    public function orderStatus($model, $config)
    {
        $data = ['status' => '1', 'recrive_at' => time()];
        CommissionOrder::updatedOrderStatus($config['order_class'], $model->id, $data);
    }

    /**
     * @param $model
     * @param $order
     * @param $config
     * @param $buyMemberFans
     */
    public function noticeData($model, $order, $config, $buyMemberFans)
    {
        $commissinOrders = CommissionOrder::getCommissiomOrders($config['order_class'], $model->id)->get();
        if (!is_null($buyMemberFans)) {
            foreach ($commissinOrders as $commissinOrder) {
                $noticeData = [
                    'commissinOrder' => $commissinOrder->toArray(),
                    'order' => $order->toArray(),
                    'goods' => $model->hasManyOrderGoods->toArray(),
                    'agent' => $commissinOrder->hasOneFans ? $commissinOrder->hasOneFans->toArray() : [],
                    'buy' => $buyMemberFans->toArray(),
                ];
                MessageService::receiveOrder($noticeData);
            }
        }
    }

    /**
     * @param $model
     */
    public function upgradeData($model)
    {
        $levels = UpgradeService::getLevelUpgraded();
        if (!$levels) {
            return;
        }
        $set = \Setting::get('plugin.commission');
        if ($set['is_with']) {
            $this->dispatch((new UpgrateByOrderJob($model->uid, 0, $model, $levels, $set))->delay(10));
        } else {
            \Log::info('订单升级->');
            //分销商 订单升级
            UpgradeService::order($model->uid);
            //分销商 自购升级
            UpgradeService::selfBuy($model->uid);
            //指定商品
            foreach ($model->hasManyOrderGoods as $goods) {
                UpgradeService::goods($goods['goods_id'], $model->uid);
                UpgradeService::manyGood($goods['goods_id'], $model->uid);
            }
            \Log::info('订单升级<-');
        }
    }
}
