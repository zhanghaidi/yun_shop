<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-07-16
 * Time: 16:31
 */

namespace Yunshop\Love\Common\Services\Love;


use app\common\exceptions\ShopException;
use Illuminate\Support\Facades\DB;
use Yunshop\Love\Common\Models\LoveRechargeRecords;
use Yunshop\Love\Common\Models\MemberShop;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Love\Common\Services\SetService;

class RechargeAwardService
{
    /**
     * @var LoveRechargeRecords
     */
    private $rechargeModel;

    /**
     * @var int
     */
    private $firstParentId;

    /**
     * @var int
     */
    private $secondParentId;


    public function __construct(LoveRechargeRecords $rechargeModel)
    {
        $this->rechargeModel = $rechargeModel;
    }

    //奖励上级接口
    public function rechargeAward()
    {
        if ($this->rechargeAwardSet()) {
            DB::transaction(function () {
                $this->_rechargeAward();
            });
        }
        return true;
    }

    /**
     * @throws ShopException
     */
    private function _rechargeAward()
    {
        $result = $this->awardFirstParent();
        if ($result !== true) {
            throw new ShopException(LOVE_NAME . '充值奖励上一级失败');
        }
        $result = $this->awardSecondParent();
        if ($result !== true) {
            throw new ShopException(LOVE_NAME . '充值奖励上二级失败');
        }
    }

    /**
     * 奖励会员上一级
     *
     * @return bool|string
     */
    private function awardFirstParent()
    {
        $memberId = $this->getFirstParentId();
        $rate = $this->rechargeAwardFirstSet();
        $amount = $this->rechargeAwardAmount($rate);

        if ($memberId && $rate > 0 && $amount > 0) {
            $data = $this->rechargeAwardData($memberId, $amount);

            return (new LoveChangeService('usable'))->rechargeAwardFirstParent($data);
        }
        return true;
    }

    /**
     * 奖励会员上二级
     *
     * @return bool|string
     */
    private function awardSecondParent()
    {
        $memberId = $this->getSecondParentId();
        $rate = $this->rechargeAwardSecondSet();
        $amount = $this->rechargeAwardAmount($rate);

        if ($memberId && $rate > 0 && $amount > 0) {
            $data = $this->rechargeAwardData($memberId, $amount);

            return (new LoveChangeService('usable'))->rechargeAwardSecondParent($data);
        }
        return true;
    }

    /**
     * @param $memberId
     * @param $amount
     * @return array
     */
    private function rechargeAwardData($memberId, $amount)
    {
        return [
            'member_id'         => $memberId,
            'change_value'      => $amount,
            'operator'          => 0,
            'operator_id'       => 0,
            'remark'            => '充值奖励',
            'relation'          => $this->rechargeModel->order_sn
        ];
    }

    private function rechargeAwardAmount($rate)
    {
        $amount = $this->rechargeModel->money;

        return bcdiv(bcmul($amount, $rate, 4), 100, 2);
    }

    /**
     * 会员上一级ID
     *
     * @return int
     */
    private function getFirstParentId()
    {
        !isset($this->firstParentId) && $this->firstParentId = $this->_getFirstParentId();

        return $this->firstParentId;
    }

    /**
     * @return int
     */
    private function _getFirstParentId()
    {
        $memberId = $this->rechargeModel->member_id;

        return $this->getParentId($memberId);
    }

    /**
     * 会员上二级ID
     *
     * @return int
     */
    private function getSecondParentId()
    {
        !isset($this->secondParentId) && $this->secondParentId = $this->_getSecondParentId();

        return $this->secondParentId;
    }

    /**
     * @return int
     */
    private function _getSecondParentId()
    {
        $memberId = $this->firstParentId;

        return $this->getParentId($memberId);
    }

    /**
     * 会员上线ID
     *
     * @param $memberId
     * @return int
     */
    private function getParentId($memberId)
    {
        $yzMember = MemberShop::where('member_id', $memberId)->first();

        return $yzMember->parent_id ?: 0;
    }

    /**
     * 充值奖励开关
     *
     * @return float
     */
    private function rechargeAwardFirstSet()
    {
        return $this->rechargeAwardParentSet('recharge_award_first');
    }

    /**
     * 充值奖励开关
     *
     * @return float
     */
    private function rechargeAwardSecondSet()
    {
        return $this->rechargeAwardParentSet('recharge_award_second');
    }

    /**
     * @param $key
     * @return float
     */
    private function rechargeAwardParentSet($key)
    {
        $set = SetService::getLoveSet($key);
        if ($set > 0) {
            return $set;
        }
        return 0;
    }

    /**
     * 充值奖励开关
     *
     * @return bool
     */
    private function rechargeAwardSet()
    {
        return SetService::getLoveSet('recharge_award') ? true : false;
    }
}
