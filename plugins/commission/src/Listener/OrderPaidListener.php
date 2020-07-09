<?php


namespace Yunshop\Commission\Listener;

use app\common\events\order\AfterOrderPaidEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Commission\Jobs\UpgrateByOrderJob;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\services\BecomeAgentService;
use Yunshop\Commission\services\UpgradeService;
use Illuminate\Foundation\Bus\DispatchesJobs;

class OrderPaidListener
{
    use DispatchesJobs;

    /**
     *
     * @param  Dispatcher $events
     * @return mixed
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(\app\common\events\order\AfterOrderPaidEvent::class, self::class . '@handle');
    }

    public function handle(AfterOrderPaidEvent $event)
    {
        date_default_timezone_set("PRC");
        $model = $event->getOrderModel();
        \Log::info('分销订单支付',$model->id);
        //先验证是否成为分销商
        (new BecomeAgentService())->verification($model->uid, 0);
        $this->upgradeData($model);

        // 结算事件判断
        $config = \app\common\modules\shop\ShopConfig::current()->get('plugin.commission');
        $set = \Setting::get('plugin.commission');
        if (!$set['settlement_event']) {
            return;
        }
        $commissionModelExisrs = CommissionOrder::getOrderByTypeId($config['order_class'], $model->id, '0')->exists();
        if (!$commissionModelExisrs) {
            return;
        }
        $this->orderStatus($model, $config);
    }

    public function orderStatus($model, $config)
    {
        $data = ['status' => '1', 'recrive_at' => time()];
        CommissionOrder::updatedOrderStatus($config['order_class'], $model->id, $data);
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
            \Log::info('分销商升级[与]');
            $this->dispatch((new UpgrateByOrderJob($model->uid, 1, $model, $levels, $set))->delay(10));
        } else {
            \Log::info('订单升级->');
            //分销商 自购升级
            UpgradeService::selfBuyAfterPaid($model->uid);
            //指定商品
            foreach ($model->hasManyOrderGoods as $goods) {
                UpgradeService::goodsAfterPaid($goods['goods_id'], $model->uid);
                UpgradeService::manyGood($goods['goods_id'], $model->uid);
            }
            \Log::info('订单升级<-');
        }
    }
}
