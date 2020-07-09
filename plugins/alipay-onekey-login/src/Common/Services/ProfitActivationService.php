<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-20
 * Time: 11:34
 */

namespace Yunshop\Love\Common\Services;


use Yunshop\Love\Common\Models\MemberLove;
use Yunshop\Love\Common\Models\Order;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\StoreOrder;


class ProfitActivationService
{
    /**
     * @var float
     */
    private $frozeTotal;

    /**
     * @var float
     */
    private $cycleOrderProfit;


    /**
     * @return float
     */
    public function getProfitProportion()
    {
        return SetService::getProfitProportion();
    }

    public function getFrozeTotal()
    {
        if (!isset($this->frozeTotal)) {
            $this->frozeTotal = $this->frozeTotal();
        }
        return $this->frozeTotal;
    }

    public function getCycleOrderProfit()
    {
        if (!isset($this->cycleOrderProfit)) {
            $this->cycleOrderProfit = $this->cycleOrderProfit();
        }
        return $this->cycleOrderProfit;
    }

    /**
     * @return float
     */
    private function frozeTotal()
    {
        return MemberLove::uniacid()->sum('froze');
    }

    /**
     * @return float
     */
    private function cycleOrderProfit()
    {
        $cycleOrder = $this->cycleOrder();

        $profit = $cycleOrder->map(function ($order) {

            /**
             * @var Order $order
             */
            if ($order->plugin_id == 31) {
                return $this->cashierOrderProfit($order->id);
            }
            if ($order->plugin_id == 32) {
                return $this->storeOrderProfit($order->id);
            }
            return $this->baseOrderGoodsProfit($order);
        })->sum();
        return $profit;
    }

    /**
     * 收银台订单利润
     *
     * @param $orderId
     * @return float
     */
    private function cashierOrderProfit($orderId)
    {
        $model = CashierOrder::where('order_id', $orderId)->first();

        return $model->fee ? $model->fee : '0.00';
    }

    /**
     * 门店订单利润
     *
     * @param $orderId
     * @return float
     */
    private function storeOrderProfit($orderId)
    {
        $model = StoreOrder::where('order_id', $orderId)->first();

        return $model->fee ? $model->fee : '0.00';
    }

    /**
     * 普通订单利润
     *
     * @param Order $order
     * @return float
     */
    private function baseOrderGoodsProfit(Order $order)
    {
        $goodsProfit = $order->orderGoods->map(function ($goods) {
            return bcsub($goods->payment_amount, $goods->goods_cost_price, 2);
        })->sum();

        return $goodsProfit;
    }

    /**
     * @return \app\framework\Database\Eloquent\Collection
     */
    private function cycleOrder()
    {
        list($startTime, $endTime) = $this->cycleTime();

        //return Order::completed()->with('orderGoods')->get();
        return Order::completed()
            ->whereBetween('finish_time', [$startTime, $endTime])
            ->with('orderGoods')->get();
    }

    /**
     * @return array
     */
    private function cycleTime()
    {
        return (new ActivationCycleTimeService())->getCycleTime();
    }

}
