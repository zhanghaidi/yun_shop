<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/25
 * Time: 2:33 PM
 */

namespace Yunshop\Love\Common\Services\Love;


use app\common\exceptions\ShopException;
use Illuminate\Support\Facades\DB;
use Yunshop\Love\Common\Models\LoveRechargeRecords;
use Yunshop\Love\Common\Services\LoveChangeService;

class RechargeService
{
    /**
     * @var LoveRechargeRecords
     */
    private $rechargeModel;


    public function __construct(LoveRechargeRecords $rechargeModel)
    {
        $this->rechargeModel = $rechargeModel;
    }


    public function tryRecharge()
    {
        DB::transaction(function () {
            $this->_tryRecharge();
        });
        return true;
    }

    public function tryOrderRecharge()
    {
        DB::transaction(function () {
            $result = $this->orderRefundLove();
            if (!$result) {
                throw new ShopException('订单返还失败：更新数据失败');
            }
        });
        return true;
    }

    /**
     * @throws ShopException
     */
    private function _tryRecharge()
    {
        $result = $this->updateMemberLove();
        if (!$result) {
            throw new ShopException(LOVE_NAME . '充值失败：更新数据失败');
        }
        $result = $this->rechargeAward();
        if (!$result) {
            throw new ShopException(LOVE_NAME . '充值失败：充值奖励错误');
        }
        $result = $this->updateRechargeStatus();
        if (!$result) {
            throw new ShopException(LOVE_NAME . '充值失败：修改充值状态失败');
        }
    }

    /**
     * @return string
     */
    private function orderRefundLove()
    {
        $value_type = $this->rechargeModel->value_type == 1 ? 'usable' : 'froze';
        $change_value = $this->rechargeModel->change_value;

        if ($change_value > 0) {
            $changeData = $this->getChangeData();
            $changeData['remark'] = '订单返还变动['.$this->rechargeModel->money.']积分，订单号ID【'.$this->rechargeModel->order_sn.'】';
            return (new LoveChangeService($value_type))->cancelConsume($changeData);
        } else {
            return (new LoveChangeService($value_type))->rechargeMinus($this->getChangeData());
        }
    }

    /**
     * @return bool
     */
    private function updateMemberLove()
    {
        $value_type = $this->rechargeModel->value_type == 1 ? 'usable' : 'froze';
        $change_value = $this->rechargeModel->change_value;

        if ($change_value < 0) {
            return (new LoveChangeService($value_type))->rechargeMinus($this->getChangeData());
        } else {
            return (new LoveChangeService($value_type))->recharge($this->getChangeData());
        }
    }

    /**
     * @return bool
     */
    private function rechargeAward()
    {
        return (new RechargeAwardService($this->rechargeModel))->rechargeAward();
    }

    /**
     * @return bool
     */
    private function updateRechargeStatus()
    {
        $this->rechargeModel->status = LoveRechargeRecords::STATUS_SUCCESS;
        return $this->rechargeModel->save();
    }

    /**
     * @return array
     */
    private function getChangeData()
    {
        return [
            'member_id'         => $this->rechargeModel->member_id,
            'change_value'      => abs($this->rechargeModel->change_value),
            'operator'          => $this->rechargeModel->type,
            'operator_id'       => \YunShop::app()->uid ? \YunShop::app()->uid : $this->rechargeModel->member_id,
            'remark'            => $this->rechargeRemark(),
            'relation'          => $this->rechargeModel->order_sn
        ];
    }

    /**
     * @return string
     */
    private function rechargeRemark()
    {
        return "充值变动['{$this->rechargeModel->money}']积分，充值记录ID【{$this->rechargeModel->id}】";
    }
}
