<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/30 下午3:34
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;


use app\common\events\order\AfterOrderReceivedEvent;
use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use app\common\models\Member;
use app\common\models\OrderGoods;
use app\common\models\Setting;
use app\Jobs\OrderBonusJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\Agents;
use Yunshop\Diyform\admin\DiyformDataController;
use Yunshop\Love\Backend\Widgets\Goods\LoveGoodsWidget;
use Yunshop\Love\Common\Models\GoodsLove;
use Yunshop\Love\Common\Models\LoveOrderGoods;
use Yunshop\Love\Frontend\Models\MemberLove;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\Store;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\StoreCashier\common\models\Order;
use Yunshop\Supplier\common\models\Goods;

class LoveGiveService
{
    use DispatchesJobs;
    /**
     * @var string
     */
    private $love_name;

    /**
     * @var
     */
    private $orderModel;


    /**
     * @var
     */
    private $orderGoods;


    /**
     * @var
     */
    private $relationLoveGoods;


    /**
     * @var LoveChangeService
     */
    private $loveChangeService;


    /**
     * 全局默认比例
     *
     * @var double
     */
    private $default_proportion;


    /**
     * 商品独立设置：是否开启字段名称
     *
     * @var int
     */
    private $status_field_name;


    /**
     * 商品独立设置：奖励比例值字段
     *
     * @var string
     */
    private $proportion_field_name;


    /**
     * 商品独立设置：奖励固定值字段
     *
     * @var string
     */
    private $fixed_field_name;

    /**
     *加速爱心值全局比例
     */
    private $speedupRatio;

    /**
     *分销商等级
     */
    private $level;

    /**
     *确认反序列
     */
    private $serialize;

    public function __construct()
    {
        //todo 区分奖励、加速激活，放在一个类太冗杂
        //todo 买方奖励、上级奖励应该分成两个类
        $this->love_name = SetService::getLoveName();
        $this->loveChangeService = new LoveChangeService(SetService::getAwardType());
    }

    private function isHandle($orderModel)
    {
        $this->orderModel = $orderModel;
        if (!$this->orderModel) {
            return false;
        }
        $this->orderGoods = $this->orderModel->hasManyOrderGoods;
        if (!$this->orderGoods) {
            return false;
        }
        //如果爱心值商品关联设置不存在，则不赠送
        $this->relationLoveGoods = $this->getRelationLoveGoodsSet();
        if ($this->relationLoveGoods->isEmpty()) {
            return false;
        }
        return true;
    }

    /**
     * 订单完成赠送爱心值
     *
     * @param $orderModel
     * @throws ShopException
     */
    public function loveGive($orderModel)
    {
        if ($this->isHandle($orderModel)) {
            $this->shoppingGive();
        }
    }

    /**
     * 加速激活爱心值功能
     *
     * @param $orderModel
     */
    public function quickenActivation($orderModel)
    {
        if ($this->isHandle($orderModel)) {
            $this->_quickenActivation();
        }
    }

    private function _quickenActivation()
    {
        //判断全局爱心值加速是否开启
        if (SetService::getAcceleratedActivationStatus()) {
            //爱心值加速全局比例
            $this->speedupRatio = SetService::getAcceleratedActivationOfLoveRatio();

            //获取爱心值计算的加速值
            $ratio_value = $this->AcceleratedActivationLove();

            $froze = MemberLove::getMemberLoveFroze($this->orderModel->uid);

            if ($ratio_value != 0 && $froze->froze != 0) {

                if ($froze && bccomp($ratio_value, $froze->froze, 2) == 1) {
                    $ratio_value = $froze->froze;
                }
                $param = '加速激活爱心值';
                //赋值
                $this->accelerateUpdateMemberLove($ratio_value, $param);
            }
        }
    }

    /**
     * 加速激活爱心值
     */
    private function accelerateUpdateMemberLove($ratio_value, $param)
    {
        $data = [
            //会员id
            'member_id'    => $this->orderModel->uid,
            //操作员
            'operator'     => ConstService::OPERATOR_ORDER,
            //爱心值加速值
            'change_value' => $ratio_value,
            //操作id
            'operator_id'  => $this->orderModel->id,
            //说明
            'remark'       => $param,
            //关系
            'relation'     => ''
        ];

        //操作数据库
        $this->loveChangeService->quickenActivation($data);
    }


    private function shoppingGive()
    {
        //买方奖励
        if (SetService::getAwardStatus()) {//获取奖励状态
            \Log::debug("==获取奖励状态");
            $result = $this->awardBuyer();
            if ($result !== true) {
                throw new ShopException('订单完成奖励爱心值失败');
            }
        }

        $pluginCommission = \YunShop::plugin()->get('commission');
        if ($pluginCommission == true && SetService::getParentAwardStatus() == 2) {
            //分销上级奖励
            $result = $this->awardBuyerCommissionLevels();
            if ($result !== true) {
                throw new ShopException('订单完成奖励上级爱心值失败');
            }
        } elseif (SetService::getParentAwardStatus() == 1) {
            //会员上级奖励
            $result = $this->awardBuyerParent();
            if ($result !== true) {
                throw new ShopException('订单完成奖励上级爱心值失败');
            }
        }

        // 订单插件分红记录
        (new OrderBonusJob('yz_love', 'love', 'operator_id', 'id', 'change_value', $this->orderModel))->handle();

        return true;
    }

    /**
     * 奖励买家
     * @return bool
     * @throws AppException
     */
    private function awardBuyer()
    {
        //获取奖励比例
        $this->default_proportion = SetService::getAwardProportion();
        $this->status_field_name = 'award';
        $this->proportion_field_name = 'award_proportion';
        $this->fixed_field_name = '';

        //计算商品赠送比例
        $award_value = $this->getAwardValue();
        if ($award_value < 0) {
            return true;
        }
        $param = '购物赠送爱心值：购物赠送';

        \Log::debug("赠送爱心值为" . $award_value);
        \Log::debug("会员ID" . $this->orderModel->uid);
        return $this->updateMemberLove($this->orderModel->uid, $award_value, $param);
    }


    private function awardBuyerParent()
    {
        //todo 此处可以优化为一条查询语句
        $memberModel = $this->getMemberModel($this->orderModel->uid);
        if ($memberModel->yzMember->parent_id) {
            //上一级奖励
            $result = $this->awardBuyerFirstParent($memberModel->yzMember->parent_id);
            if ($result !== true) {
                throw new ShopException('订单完成奖励上一级爱心值失败');
            }
            $firstParentModel = $this->getMemberModel($memberModel->yzMember->parent_id);
            if ($firstParentModel->yzMember->parent_id) {
                //上二级奖励
                $result = $this->awardBuyerSecondParent($firstParentModel->yzMember->parent_id);
                if ($result !== true) {
                    throw new ShopException('订单完成奖励上二级爱心值失败');
                }
                $secondParentModel = $this->getMemberModel($firstParentModel->yzMember->parent_id);
                if ($secondParentModel->yzMember->parent_id) {
                    //上三级奖励
                    $result = $this->awardBuyerThirdParent($secondParentModel->yzMember->parent_id);
                    if ($result !== true) {
                        throw new ShopException('订单完成奖励上三级爱心值失败');
                    }
                }
            }
        }
        return true;
    }

    public function awardBuyerCommissionLevels()
    {
        $memberModel = $this->getMemberModel($this->orderModel->uid);
        $agent = $this->getAgentModel($memberModel->yzMember->parent_id);
        $levelSet = unserialize(SetService::getCommissionLevelGiveScale());
        if ($agent) {
            //一级
            $level = 'level_' . $agent->agent_level_id;
            $result = $this->awardBuyerFirstAgent($agent->member_id, $levelSet['rule'][$level]['first_level_rate'], $level);
            if ($result !== true) {
                throw new ShopException('订单完成奖励上一级爱心值失败');
            }
            //二级
            $parentAgent = $this->getAgentModel($agent->parent_id);
            if ($parentAgent) {
                $levelFirst = 'level_' . $parentAgent->agent_level_id;
                $result = $this->awardBuyerSecondAgent($parentAgent->member_id, $levelSet['rule'][$levelFirst]['second_level_rate'], $levelFirst);
                if ($result !== true) {
                    throw new ShopException('订单完成奖励上二级爱心值失败');
                }
                //三级
                $parentFirstAgent = $this->getAgentModel($parentAgent->parent_id);
                if ($parentFirstAgent) {
                    $levelSecond = 'level_' . $parentFirstAgent->agent_level_id;
                    $result = $this->awardBuyerThirdAgent($parentFirstAgent->member_id, $levelSet['rule'][$levelSecond]['third_level_rate'], $levelSecond);
                    if ($result !== true) {
                        throw new ShopException('订单完成奖励上三级爱心值失败');
                    }
                }
            }
        }
        return true;
    }

    /**
     * 上一级奖励
     * @param $member_id
     * @return bool
     * @throws AppException
     */
    private function awardBuyerFirstParent($member_id)
    {
        $this->default_proportion = SetService::getFirstParentAwardProportion();
        $this->status_field_name = 'parent_award';
        $this->proportion_field_name = 'parent_award_proportion';
        $this->fixed_field_name = 'parent_award_fixed';

        $award_value = $this->getAwardValue();
        if ($award_value < 0) {
            return true;
        }
        $param = '购物赠送爱心值：上一级赠送';
        return $this->updateMemberLove($member_id, $award_value, $param);
    }


    /**
     * 上二级奖励
     */
    private function awardBuyerSecondParent($member_id)
    {
        $this->default_proportion = SetService::getSecondParentAwardProportion();
        $this->status_field_name = 'parent_award';
        $this->proportion_field_name = 'second_award_proportion';
        $this->fixed_field_name = 'second_award_fixed';

        $award_value = $this->getAwardValue();
        if ($award_value < 0) {
            return true;
        }

        $param = '购物赠送爱心值：上二级赠送';
        return $this->updateMemberLove($member_id, $award_value, $param);
    }

    /**
     * 上三级奖励
     */
    private function awardBuyerThirdParent($member_id)
    {

        $this->default_proportion = SetService::getThirdParentAwardProportion();
        $this->status_field_name = 'parent_award';
        $this->proportion_field_name = 'third_award_proportion';
        $this->fixed_field_name = 'third_award_fixed';

        $award_value = $this->getAwardValue();
        if ($award_value < 0) {
            return true;
        }

        $param = '购物赠送爱心值：上三级赠送';
        return $this->updateMemberLove($member_id, $award_value, $param);
    }

    /**
     * 分销商上一级奖励
     */
    private function awardBuyerFirstAgent($member_id, $firstRate, $level)
    {
        $this->default_proportion = $firstRate ?: 0;
        $this->status_field_name = 'commission_level_give';
        $this->proportion_field_name = 'first_level_rate';
        $this->fixed_field_name = 'first_level_fixed';
        $this->level = $level;
        $this->serialize = true;

        $award_value = $this->getCommissionAwardValue();
        if ($award_value < 0) {
            return true;
        }
        $param = '购物赠送爱心值：分销商上一级赠送';
        return $this->updateMemberLove($member_id, $award_value, $param);
    }

    /**
     * 分销商上二级奖励
     */
    private function awardBuyerSecondAgent($member_id, $secondRate, $levelFirst)
    {
        $this->default_proportion = $secondRate ?: 0;
        $this->status_field_name = 'commission_level_give';
        $this->proportion_field_name = 'second_level_rate';
        $this->fixed_field_name = 'second_level_fixed';
        $this->level = $levelFirst;
        $this->serialize = false;

        $award_value = $this->getCommissionAwardValue();
        if ($award_value < 0) {
            return true;
        }
        $param = '购物赠送爱心值：分销商上二级赠送';
        return $this->updateMemberLove($member_id, $award_value, $param);
    }

    /**
     * 分销商上三级奖励
     */
    private function awardBuyerThirdAgent($member_id, $thirdRate, $levelSecond)
    {
        $this->default_proportion = $thirdRate ?: 0;
        $this->status_field_name = 'commission_level_give';
        $this->proportion_field_name = 'third_level_rate';
        $this->fixed_field_name = 'third_level_fixed';
        $this->level = $levelSecond;
        $this->serialize = false;

        $award_value = $this->getCommissionAwardValue();
        if ($award_value < 0) {
            return true;
        }
        $param = '购物赠送爱心值：分销商上三级赠送';
        return $this->updateMemberLove($member_id, $award_value, $param);
    }

    /**
     * 获取奖励值订单商品循环
     *
     * @return double
     */
    private function getAwardValue()
    {
        $award_value = 0;
        //订单商品循环
        foreach ($this->orderGoods as $keyOne => $item) {
            $award_value += $this->relationLoveGoods($item);
        }
        return $award_value;
    }

    /**
     * 获取奖励值订单商品循环(分销)
     *
     * @return double
     */
    private function getCommissionAwardValue()
    {
        $award_value = 0;
        //订单商品循环
        foreach ($this->orderGoods as $keyOne => $item) {
            $award_value += $this->relationLoveGoodsCommission($item);
        }
        return $award_value;
    }

    /**
     * 获取订单商品关联爱心值商品循环(分销)
     *
     * @return double
     */
    public function relationLoveGoodsCommission($orderGoods)
    {
        $award_value = 0;
        //关联爱心值商品循环
        foreach ($this->relationLoveGoods as $keyTwo => $item) {
            if ($orderGoods['goods_id'] == $item['goods_id'] && $item[$this->status_field_name] == 1) {
                $award_value += $this->calculationTwo($orderGoods, $item);
            }
        }
        return $award_value;
    }

    /**
     * 获取订单商品关联爱心值商品循环
     *
     * @return double
     */
    public function relationLoveGoods($orderGoods)
    {
        $award_value = 0;
        //关联爱心值商品循环
        foreach ($this->relationLoveGoods as $keyTwo => $item) {
            if ($orderGoods['goods_id'] == $item['goods_id'] && $item[$this->status_field_name] == 1) {//
                //如果比例存在优先使用比例计算
                $award_value += $this->calculation($orderGoods, $item);
            }

        }
        return $award_value;
    }

    /**
     * 获取奖励值计算奖励值(分销)
     *
     * @return double
     */
    public function calculationTwo($orderGoods, $relationLoveGoods)
    {
        $rule = $this->price($orderGoods);
        $award_value = 0;
        if ($this->serialize) {
            $relationLoveGoods->commission = unserialize($relationLoveGoods->commission);
        }

        if ($relationLoveGoods['commission']['rule'][$this->level][$this->proportion_field_name] > 0) {
            $award_value += $this->algorithm($rule, $relationLoveGoods['commission']['rule'][$this->level][$this->proportion_field_name]);
        } elseif ($relationLoveGoods['commission']['rule'][$this->level][$this->fixed_field_name] > 0) {
            $scalar = $relationLoveGoods['commission']['rule'][$this->level][$this->fixed_field_name];
            $scalar = bcmul($scalar, $orderGoods['total'], 2);
            $award_value += $scalar;
        } else {
            $award_value += $this->algorithm($rule, $this->default_proportion);
        }

        return $award_value;
    }

    /**
     * 获取奖励值计算奖励值
     *
     * @return double
     */
    public function calculation($orderGoods, $relationLoveGoods)
    {
        $rule = $this->price($orderGoods);
        $award_value = 0;
        if ($relationLoveGoods[$this->proportion_field_name] > 0) {
            $award_value += $this->algorithm($rule, $relationLoveGoods[$this->proportion_field_name]);
            //否则，如果固定值存在，使用固定值
        } elseif ($relationLoveGoods[$this->fixed_field_name] > 0) {
            $scalar = $relationLoveGoods[$this->fixed_field_name];
            $scalar = bcmul($scalar, $orderGoods['total'], 2);
            $award_value += $scalar;
            //否则，使用全局比例
        } else {
            $award_value += $this->algorithm($rule, $this->default_proportion);
        }
        return $award_value;
    }


    /**
     * 获取奖励值计算公式
     *
     * @return double
     */
    public function algorithm($price, $proportion)
    {
        return bcdiv(bcmul($price, $proportion, 4), 100, 2);
    }

    /**
     * @param $member_id
     * @return mixed
     */
    private function getMemberModel($member_id)
    {
        return Member::getMemberByUid($member_id)->with('yzMember')->first();
    }

    /**
     * @param $member_id
     * @return mixed
     */
    private function getAgentModel($member_id)
    {
        return Agents::getAgentByMemberId($member_id)->with('yzMember')->first();
    }

    /**
     * 更新会员爱心值
     *
     * @param $member_id
     * @param $award_value
     * @param $param
     * @return bool
     * @throws AppException
     */
    private function updateMemberLove($member_id, $award_value, $param)
    {

        if (!$member_id) {
            Log::info("爱心值购物奖励：会员ID错误");
            return true;
        }
        if (bccomp($award_value, 0, 2) != 1) {
            Log::info($param . "值为0");
            return true;
        }

        $record_data = $this->getRecordData($member_id, $award_value, $param);

        if ($this->status_field_name == "parent_award") {
            $result = $this->loveChangeService->parentAward($record_data);
        } elseif ($this->status_field_name == "commission_level_give") {
            $result = $this->loveChangeService->commissionLevelGive($record_data);
        } else {
            $result = $this->loveChangeService->award($record_data);
            Log::debug("状态" . $result);
        }


        if ($result === true) {
            return true;
        }
        throw new AppException(SetService::getLoveName() . '奖励错误');
    }


    /**
     * 会员爱心值记录请求数据
     *
     * @param $member_id
     * @param $changeValue
     * @param $param
     * @return array
     */
    private function getRecordData($member_id, $changeValue, $param)
    {
        return [
            'member_id'    => $member_id,
            'operator'     => ConstService::OPERATOR_ORDER,
            'change_value' => $changeValue,
            'operator_id'  => $this->orderModel->id,
            'remark'       => $param . $changeValue,
            'relation'     => ''
        ];
    }


    /**
     * 获取订单所有商品爱心值赠送设置
     *
     * @return mixed
     */
    private function getRelationLoveGoodsSet()
    {
        return GoodsLove::whereIn('goods_id', $this->getGoodsIds())->get();
    }


    /**
     * 获取订单中所有商品的ID集合
     *
     * @return array
     */
    private function getGoodsIds()
    {
        return collect($this->orderGoods)->map(function ($goods) {
            return $goods['goods_id'];
        })->toArray();
    }

    /**
     * 加速激活爱心值
     */
    public function AcceleratedActivationLove()
    {
        $ratio = 0;
        //订单商品循环
        foreach ($this->orderGoods as $keyOne => $item) {
            $ratio += $this->acceleratedRelationLoveGoods($item);
        }
        return $ratio;
    }

    /**
     * 加速激活爱心值关联爱心值商品循环
     *
     */
    public function acceleratedRelationLoveGoods($orderGoods)
    {
        $ratio = 0;
        //关联爱心值商品循环
        foreach ($this->relationLoveGoods as $keyTwo => $itme) {
            //判断商品设置是否开启爱心值加速
            if ($orderGoods['goods_id'] == $itme['goods_id'] && $itme['activation_state']) {
                $ratio += $this->acceleratedCalculation($itme, $orderGoods);
            }
        }
        return $ratio;
    }


    /**
     * 加速激活爱心值关计算
     */
    public function acceleratedCalculation($relationLoveGoods, $orderGoods)
    {
        $ratio = 0;
        //判断商品加速激活爱心值比例是否大于0
        if ($relationLoveGoods['love_accelerate'] > 0) {
            //商品价格*单独设定比例=释放数量
            $ratio += $this->acceleratedAlgorithm($orderGoods['price'], $relationLoveGoods['love_accelerate']);
        } else {
            //若商品爱心值开启，比例值小于0，默认使用全局比例运算
            $ratio += $this->acceleratedAlgorithm($orderGoods['price'], $this->speedupRatio);
        }
        return $ratio;
    }


    /**
     * 加速激活爱心值关算法：商品价格*单独设定比例=释放数量
     */
    public function acceleratedAlgorithm($price, $proportion)
    {
        return bcdiv(bcmul($price, $proportion, 4), 100, 2);
    }

    /**
     * 计算方式
     */
    public function price($orderGoods)
    {
        //商品赠送规则
        $ruleStatus = \Setting::get('love.reward_rule');
        switch ($ruleStatus) {
            //判断赠送规则，按现价，成本价，实际价,利润
            case 1 :
                $rule = $orderGoods['payment_amount'];
                break;//订单实际价
            case 2 :
                $rule = $orderGoods['goods_price'];
                break;//商品金额
            case 3 :
                $rule = $this->storeOrder($orderGoods, $orderGoods->goods_id);
                break;//成本价
            case 4 :
                //成本
                $cost = $this->storeOrder($orderGoods, $orderGoods->goods_id);
                //利润
                $profit = bcsub($orderGoods['goods_price'], $cost, 2);
                //销售价比例
                $ratio_set = SetService::getLoveSet('profit_award_proportion');
                $ratio = bcdiv($ratio_set, 100, 2);
                //销售价
                $sales_price = bcmul($orderGoods['goods_price'], $ratio, 2);

                $rule = bcsub($profit, $sales_price, 2);
                break;
            default:
                $rule = $orderGoods['payment_amount'];
                break;//订单实际价
        }
        return $rule;
    }

    /**
     * 获取成本赠送值
     * @param $orderGoods
     * @param $goods_id
     * @return mixed
     */
    public function storeOrder($orderGoods, $goods_id)
    {

        if (app('plugins')->isEnabled('store-cashier')) {
            if ($this->orderModel->plugin_id == Store::PLUGIN_ID || $this->orderModel->plugin_id == Store::CASHIER_PLUGIN_ID) {//门店订单
                $orderStore = OrderGoods::where('order_id', $this->orderModel->id)->first();
                $rule = $orderStore->goods_cost_price;
            } else {
//                $goodsCostPrice = \app\common\models\Goods::where('id',$goods_id)->first();
                $rule = $orderGoods['goods_cost_price'];//$goodsCostPrice->cost_price;//
            }
        } else {
//            $goodsCostPrice = \app\common\models\Goods::where('id',$goods_id)->first();
            $rule = $orderGoods['goods_cost_price'];//$goodsCostPrice->cost_price;
        }
        return $rule;
    }

    public function getCommissionLevels()
    {
        $levels = AgentLevel::getLevels()->get();
        return $levels;
    }

}
