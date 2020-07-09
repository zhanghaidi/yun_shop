<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2018/11/30
 * Time: 9:31 AM
 */

namespace Yunshop\Love\Common\Listeners;


use app\common\events\payment\RechargeComplatedEvent;
use app\common\exceptions\ShopException;
use app\framework\Support\Facades\Log;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Love\Common\Models\LoveRechargeRecords;
use Yunshop\Love\Common\Services\Love\RechargeService;

class RechargeCompletedListener
{
    /**
     * 爱心值充值前缀
     *
     * @var string
     */
    private $prefix = "RL";

    /**
     * 充值金额
     *
     * @var double
     */
    private $amount;

    /**
     * yuan|fen
     *
     * @var string
     */
    private $unit;

    /**
     * 充值单号
     *
     * @var string
     */
    private $orderSn;

    /**
     * @var LoveRechargeRecords
     */
    private $rechargeModel;


    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen(RechargeComplatedEvent::class, static::class . "@rechargeCompleted");
    }

    /**
     * @param RechargeComplatedEvent $event
     * @throws ShopException
     */
    public function rechargeCompleted($event)
    {
        $resultData = $event->getRechargeData();

        $this->setUnit($resultData['unit']);
        $this->setRechargeAmount($resultData['total_fee']);
        $this->setRechargeOrderSn($resultData['order_sn']);

        if ($this->isHandle()) {
            $this->handleBalanceRechargeCompleted();
        }
    }

    /**
     * @param $unit
     */
    private function setUnit($unit)
    {
        $this->unit = (string)$unit;
    }

    /**
     * @param double $amount
     */
    private function setRechargeAmount($amount)
    {
        $this->amount = (double)$amount;
    }

    /**
     * @param string $orderSn
     */
    private function setRechargeOrderSn($orderSn)
    {
        $this->orderSn = (string)$orderSn;
    }

    /**
     * @return LoveRechargeRecords
     * @throws ShopException
     */
    private function setRechargeModel()
    {
        !isset($this->rechargeModel) && $this->_setRechargeModel();

        return $this->rechargeModel;
    }

    /**
     * @throws ShopException
     */
    private function _setRechargeModel()
    {
        $rechargeModel = LoveRechargeRecords::where('order_sn', $this->orderSn)->first();

        if (!$rechargeModel) {
            throw new ShopException('Love recharge record do not exist！');
        }
        if ($rechargeModel->status == LoveRechargeRecords::STATUS_SUCCESS) {
            throw new ShopException('单号已经充值，不能重复充值（LOVE）');
        }
        $this->rechargeModel = $rechargeModel;
    }

    /**
     * @return bool
     * @throws ShopException
     */
    private function isHandle()
    {
        return $this->isBelongRecharge() && $this->validatorRechargeAmount();
    }

    /**
     * @return bool
     */
    private function isBelongRecharge()
    {
        $prefix = strtoupper(substr($this->orderSn, 0, 2));

        return $prefix === $this->prefix;
    }

    /**
     * @return bool
     * @throws ShopException
     */
    private function validatorRechargeAmount()
    {
        $this->setRechargeModel();

        $completeAmount = $this->getCompletedAmount();

        $compare = bccomp($completeAmount, $this->rechargeModel->money, 2);
        if ($compare == 0) {
            return true;
        }
        $content = [
            'order_sn'  => $this->orderSn,
            'total_fee' => $this->amount
        ];
        return false;
    }

    /**
     * @return float
     */
    private function getCompletedAmount()
    {
        if ($this->unit == 'fen') {
            return bcdiv($this->amount, 100, 2);
        }
        return $this->amount;
    }

    /**
     * 执行充值确认
     */
    private function handleBalanceRechargeCompleted()
    {
        (new RechargeService($this->rechargeModel))->tryRecharge();
    }
}
