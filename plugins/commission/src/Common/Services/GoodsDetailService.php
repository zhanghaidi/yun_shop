<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/17
 * Time: 下午1:59
 */

namespace Yunshop\Commission\Common\Services;


use app\common\models\Goods;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\Commission;

class GoodsDetailService
{
    /**
     * @var array
     */
    private $commission_set;


    /**
     * @var Goods
     */
    private $goodsModel;


    /**
     * @var Commission
     */
    private $goodsCommissionModel;


    /**
     * @var Agents
     */
    private $agentModel;


    public function __construct($goodsModel)
    {
        $this->goodsModel = $goodsModel;
        $this->agentModel = $this->getAgentModel();
        $this->commission_set = \Setting::get('plugin.commission');
        $this->goodsCommissionModel = $this->getGoodsCommissionModel();
    }

    /**
     * @return array
     */
    public function getGoodsDetailData()
    {
        if ($this->isHaveGoodsDetail()) {
            return $this->getResultData();
        }
        return [
            'commission_show' => 0
        ];
    }

    /**
     * @return array
     */
    private function getResultData()
    {
        return [
           'commission_show' => 1,
           'commission_show_level' => $this->commission_set['goods_detail_level'] ?: 1,
           'first_commission' => $this->getFirstCommission(),
           'second_commission' => $this->getSecondCommission(),
           'third_commission' => $this->getThirdCommission()
        ];
    }

    /**
     * @return bool
     */
    private function isHaveGoodsDetail()
    {
        if ($this->commission_set['is_commission'] == 1
            && $this->commission_set['goods_detail'] == 1
            && $this->goodsCommissionModel->is_commission == 1
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return float
     */
    private function getFirstCommission()
    {
        if ($this->commission_set['goods_detail_level'] >= 1) {

            return $this->getCommission('first_level');
        }
        return number_format(0, 2);
    }

    /**
     * @return float
     */
    private function getSecondCommission()
    {
        if ($this->commission_set['goods_detail_level'] >= 2) {

            return $this->getCommission('second_level');
        }
        return number_format(0, 2);
    }

    /**
     * @return float
     */
    private function getThirdCommission()
    {
        if ($this->commission_set['goods_detail_level'] >= 3) {

            return $this->getCommission('third_level');
        }
        return number_format(0, 2);
    }

    /**
     * @param $hierarchy
     * @return float
     */
    private function getCommission($hierarchy)
    {
        if ($this->goodsCommissionModel->has_commission == 1) {
            return $this->getGoodsIndieCommission($hierarchy);
        }
        return $this->getBaseCommission($hierarchy);
    }

    /**
     * @param $hierarchy
     * @return float
     */
    private function getGoodsIndieCommission($hierarchy)
    {
        $goodsIndieLevelRule = $this->getGoodsIndieCommissionRule();

        $rate = $goodsIndieLevelRule["{$hierarchy}_rate"];
        if ($rate > 0) {
            return bcmul(bcdiv($rate, 100, 4), $this->goodsModel->price, 2);
        }
        $fixed = $goodsIndieLevelRule["{$hierarchy}_pay"];

        return  $fixed ? number_format($fixed,2) : "0.00";
    }

    /**
     * @return array
     */
    private function getGoodsIndieCommissionRule()
    {
        $goodsIndieRule = unserialize($this->goodsCommissionModel->rule);

        $goodsIndieLevelRule = $goodsIndieRule['level_0'];
        if (isset($this->agentModel->agentLevel->id)) {
            $goodsIndieLevelRule = $goodsIndieRule['level_' . $this->agentModel->agent_level_id];
        }
        return $goodsIndieLevelRule;
    }

    /**
     * @param $hierarchy
     * @return float
     */
    private function getBaseCommission($hierarchy)
    {
        $rate = $this->commission_set[$hierarchy];
        if (isset($this->agentModel->agentLevel->id)) {
            $rate = $this->agentModel->agentLevel->$hierarchy;
        }
        return bcmul(bcdiv($rate, 100, 4), $this->goodsModel->price, 2);
    }

    /**
     * @return Commission
     */
    private function getGoodsCommissionModel()
    {
        return Commission::getGoodsById($this->goodsModel->id)->first();
    }

    /**
     * @return Agents
     */
    private function getAgentModel()
    {
        $member_id = \YunShop::app()->getMemberId();

        return  Agents::getAgentByMemberId($member_id)->first();
    }

}
