<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/4/8
 * Time: 下午5:36
 */

namespace Yunshop\Commission\services;


use app\common\facades\Setting;
use app\common\models\Order;
use Illuminate\Support\Facades\Log;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\YzMember;
use Yunshop\Merchant\common\services\CenterUpgradeService;

class UpgradeService
{
    /**
     * 订单升级入口
     * @param int $memberId
     */
    public static function order($memberId)
    {
        //等级 升级条件
        $levels = static::getLevelUpgraded();
        if (!$levels) {
            return;
        }
        //分销商数据
        $agentModel = Agents::getAgentByMemberId($memberId)->first();
        if (!$agentModel) {
            return;
        }
        static::setOrder($memberId);
        //分销上级
        //二级
        $secondLevel = Agents::getAgentByMemberId($agentModel->parent_id)->first();
        //三级
        if ($secondLevel) {
            static::setOrder($secondLevel->member_id);
            $thirdLevel = Agents::getAgentByMemberId($secondLevel->parent_id)->first();
        }
        if (isset($thirdLevel) && $thirdLevel) {
            static::setOrder($thirdLevel->member_id);
        }
        return;
    }

    public static function setOrder($memberId)
    {
        //等级 升级条件
        $levels = static::getLevelUpgraded();
        if (!$levels) {
            return;
        }
        //分销商数据
        $agentModel = Agents::getAgentByMemberId($memberId)->first();
        if (!$agentModel) {
            return;
        }

        $orderModel = CommissionOrder::getCommissionOrderByMemberId($memberId);
        $order = $orderModel->whereHas('order', function ($query) {
            $query->whereBetween('status', [Order::WAIT_SEND, Order::COMPLETE]);
        })->get();

        if (!$order) {
            return;
        }
        foreach ($levels as $level) {
            //分销订单金额
            if (isset($level['upgraded']['order_money'])) {
                static::orderMoney($order, $level, $memberId);
            }
            //分销订单数量
            if (isset($level['upgraded']['order_count'])) {
                static::orderCount($order, $level, $memberId);
            }
            //一级分销订单金额
            if (isset($level['upgraded']['first_order_money'])) {
                static::firstOrderMoney($orderModel, $level, $memberId);
            }
            //一级分销订单数量
            if (isset($level['upgraded']['first_order_count'])) {
                static::firstOrderCount($orderModel, $level, $memberId);
            }
            //一级客户消费满x元 人数达到x个
            if (isset($level['upgraded']['buy_and_sum'])) {
                static::buyAndSum($memberId,$level);
            }
        }
        return;
    }

    /**
     * 一级客户消费满x元 人数达到x个
     * @param $uid
     * @param $level_model
     */
    public static function buyAndSum($uid, $level_model)
    {
        $agentPraentData = Agents::getLowerData($uid, '1')->get();
        if (!$agentPraentData->isEmpty() && $level_model['upgraded']['buy_and_sum']['buy'] && $level_model['upgraded']['buy_and_sum']['sum']) {
            $sum = 0;
            foreach ($agentPraentData->toArray() as $agent) {
                $buy_price_total = Order::select('id', 'uid', 'price')
                    ->whereStatus(3)
                    ->whereUid($agent['member_id'])
                    ->sum('price');
                if ($buy_price_total >= $level_model['upgraded']['buy_and_sum']['buy']) {
                    $sum += 1;
                }
                if ($sum >= $level_model['upgraded']['buy_and_sum']['sum']) {
                    $result = static::validate($level_model['level'], $uid);
                    if ($result) {
                        \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level_model['id'], $result, '[或]分销订单金额[实际-'.$level_model.']条件-['.$level_model['upgraded']['buy_and_sum'].']');
                        static::upgrade($level_model, $uid, $result);
                    }
                }
            }
        }
    }

    /**
     * @param $orderModel
     * @param $level
     * @param $memberId
     * 分销订单金额
     */
    public static function orderMoney($orderModel, $level, $memberId)
    {

        $sum_price = $orderModel->sum("order.price");
        if ($sum_price >= $level['upgraded']['order_money']) {
            $result = static::validate($level['level'], $memberId);
            if ($result) {
                \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]分销订单金额[实际-'.$sum_price.']条件-['.$level['upgraded']['order_money'].']');
                static::upgrade($level, $memberId, $result);
            }
        }
        return;
    }

    /**
     * @param $orderModel
     * @param $level
     * @param $memberId
     * 分销订单数量
     */
    public static function orderCount($orderModel, $level, $memberId)
    {
        $count_id = $orderModel->count("id");
        if ($count_id >= $level['upgraded']['order_count']) {
            $result = static::validate($level['level'], $memberId);
            if ($result) {
                \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]分销订单数量[实际-'.$count_id.'条件-'.$level['upgraded']['order_count'].']');
                static::upgrade($level, $memberId, $result);
            }
        }
        return;
    }

    public static function selfBuyMoney($orderModel, $level, $memberId)
    {
        if (!$level['upgraded']['self_order_after']) {
            $status = 3;
        } else {
            $status = 1;
        }
        $sum_price = $orderModel->where('status', '>=', $status)->sum("price");
        if ($sum_price >= $level['upgraded']['self_buy_money']) {
            $result = static::validate($level['level'], $memberId);
            if ($result) {
                \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]自购订单金额[实际-'.$sum_price.'条件-'.$level['upgraded']['self_buy_money'].']');
                static::upgrade($level, $memberId, $result);
            }
        }
        return;
    }

    /**
     * @param $orderModel
     * @param $level
     * @param $memberId
     * 分销订单数量
     */
    public static function selfBuyCount($orderModel, $level, $memberId)
    {
        if (!$level['upgraded']['self_order_after']) {
            $status = 3;
        } else {
            $status = 1;
        }
        $count_id = $orderModel->where('status', '>=', $status)->count("id");
        if ($count_id >= $level['upgraded']['self_buy_count']) {
            $result = static::validate($level['level'], $memberId);
            if ($result) {
                \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]自购订单数量[实际-'.$count_id.'条件-'.$level['upgraded']['self_buy_count'].']');
                static::upgrade($level, $memberId, $result);
            }
        }
        return;
    }

    /**
     * @param $orderModel
     * @param $level
     * @param $memberId
     * 一级分销订单金额
     */
    public static function firstOrderMoney($orderModel, $level, $memberId)
    {
        \Log::debug("监听一级订单");
        $order = $orderModel->where('hierarchy', '1')->get();
        $sum_price = $order->sum("order.price");
        \Log::debug("监听upgraded",[$sum_price ,$level['upgraded']['first_order_money']]);
        if ($sum_price >= $level['upgraded']['first_order_money']) {
            $result = static::validate($level['level'], $memberId);
            if ($result) {
                \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]一级分销订单金额[实际-'.$sum_price.'条件-'.$level['upgraded']['first_order_money'].']');
                static::upgrade($level, $memberId, $result);
            }
        }
        return;
    }

    /**
     * @param $orderModel
     * @param $level
     * @param $memberId
     * 一级分销订单数量
     */
    public static function firstOrderCount($orderModel, $level, $memberId)
    {
        $order = $orderModel->where('hierarchy', '1')->get();
        $count_id = $order->count("id");
        if ($count_id >= $level['upgraded']['first_order_count']) {
            $result = static::validate($level['level'], $memberId);
            if ($result) {
                \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]一级分销订单数量[实际-'.$count_id.'条件-'.$level['upgraded']['first_order_count'].']');
                static::upgrade($level, $memberId, $result);
            }
        }
        return;
    }

    /**
     * 自购升级入口
     * @param $memberId
     */
    public static function selfBuy($memberId)
    {
        //等级 升级条件
        $levels = static::getLevelUpgraded();

        if (!$levels) {
            return;
        }
        //分销商数据
        $agentModel = Agents::getAgentByMemberId($memberId)->first();
        if (!$agentModel) {
            return;
        }
//        $orderModel = CommissionOrder::getCommissionOrderByMemberId($memberId);
//        $order = $orderModel->where('buy_id', $memberId)->get();
        $order = Order::where('uid',$memberId);
        if (!$order) {
            return;
        }
        foreach ($levels as $level) {
            //判断订单是付款后还是完成后
            //自购订单金额
            if (isset($level['upgraded']['self_buy_money'])) {
                static::selfBuyMoney($order, $level, $memberId);
            }
            //自购订单数量
            if (isset($level['upgraded']['self_buy_count'])) {
                static::selfBuyCount($order, $level, $memberId);
            }
        }
        return;
    }

    public static function selfBuyAfterPaid($memberId)
    {
        //等级 升级条件
        $levels = static::getLevelUpgraded();

        if (!$levels) {
            return;
        }
        //分销商数据
        $agentModel = Agents::getAgentByMemberId($memberId)->first();
        if (!$agentModel) {
            return;
        }
//        $orderModel = CommissionOrder::getCommissionOrderByMemberId($memberId);
//        $order = $orderModel->where('buy_id', $memberId)->get();
        $order = Order::where('uid',$memberId)->whereBetween('status', [Order::WAIT_SEND,Order::COMPLETE]);
        if (!$order) {
            return;
        }
        foreach ($levels as $level) {
            //判断订单是付款后还是完成后
            if ($level['upgraded']['self_order_after'] == 1) {
                //自购订单金额
                if (isset($level['upgraded']['self_buy_money'])) {
                    static::selfBuyMoney($order, $level, $memberId);
                }
                //自购订单数量
                if (isset($level['upgraded']['self_buy_count'])) {
                    static::selfBuyCount($order, $level, $memberId);
                }
            }
        }
        return;
    }

    /**
     * 会员下线升级入口
     * @param $memberId
     */
    public static function member($memberId)
    {
        Log::info('会员下线升级');
        //等级 升级条件
        $levels = static::getLevelUpgraded();
        if (!$levels) {
            return;
        }

        $member = YzMember::getMemberByMemberId($memberId)->first();
        $memberPraent = YzMember::getPraents($member->relation)->get();
        foreach ($levels as $level) {
            //下线会员数量
            Log::info('下线会员数量');
            if (isset($level['upgraded']['lower_count'])) {
                static::lowerCount($memberPraent, $level);
            }
            //一级下线会员数量
            Log::info('一级下线会员数量');
            if (isset($level['upgraded']['first_lower_count'])) {
                static::firstLowerCount($memberPraent, $level);
            }

            //一级下线分销商数量
            Log::info('一级下线分销商数量');
            if (isset($level['upgraded']['first_lower_agent_count'])) {
                static::firstLowerAgentCount($memberPraent, $level);
            }
        }
        return;
    }

    /**
     * @param $memberPraent
     * @param $level
     */
    public static function lowerCount($memberPraent, $level)
    {
        foreach ($memberPraent as $member) {
            $memberPraentData = YzMember::getLowerData($member['member_id'])->get();

            $count_member_id = $memberPraentData->count('member_id');
            if ($count_member_id >= $level['upgraded']['lower_count']) {
                $result = static::validate($level['level'], $member['member_id']);
                if ($result) {
                    \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]下线人数[实际-'.$count_member_id.'条件-'.$level['upgraded']['lower_count'].']');
                    static::upgrade($level, $member['member_id'], $result);
                }
            }
        }

        return;
    }

    /**
     * @param $memberData
     * @param $level
     * @param $memberId
     * @param $agentLevelId
     */
    public static function firstLowerCount($memberPraent, $level)
    {
        foreach ($memberPraent as $member) {
            $memberPraentData = YzMember::getLowerData($member['member_id'], '1')->get();
            $count_member_id = $memberPraentData->count('member_id');
            if ($count_member_id >= $level['upgraded']['first_lower_count']) {
                $result = static::validate($level['level'], $member['member_id']);
                if ($result) {
                    \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]一级下线人数[实际-'.$count_member_id.'条件-'.$level['upgraded']['first_lower_count'].']');
                    static::upgrade($level, $member['member_id'], $result);
                }
            }
        }
        return;
    }


    /**
     * 分销下线升级入口
     * @param $agent
     */
    public static function agent($agent)
    {
        //等级 升级条件
        $levels = static::getLevelUpgraded();
        if (!$levels) {
            return;
        }

        $agentPraent = Agents::getPraents($agent->parent)->get();
        foreach ($levels as $level) {
            //下线会员数量
            if (isset($level['upgraded']['lower_agent_count'])) {

                static::lowerAgentCount($agentPraent, $level);
            }
            //一级下线会员数量
            if (isset($level['upgraded']['first_lower_agent_count'])) {

                static::firstLowerAgentCount($agentPraent, $level);
            }
        }
        return;
    }

    /**
     * @param $agentPraent
     * @param $level
     */
    public static function lowerAgentCount($agentPraent, $level)
    {

        foreach ($agentPraent as $member) {
            $agentPraentData = Agents::getLowerData($member['member_id'])->get();

            $count_id = $agentPraentData->count('id');

            if ($count_id >= $level['upgraded']['lower_agent_count']) {
                $result = static::validate($level['level'], $member['member_id']);
                if ($result) {
                    \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]下级分销人数[实际-'.$count_id.'条件-'.$level['upgraded']['lower_agent_count'].']');
                    static::upgrade($level, $member['member_id'], $result);
                }
            }
        }
        return;
    }

    /**
     * @param $agentPraent
     * @param $level
     */
    public static function firstLowerAgentCount($agentPraent, $level)
    {
        foreach ($agentPraent as $member) {
            $agentPraentData = Agents::getLowerData($member['member_id'], '1')->get();

            $count_id = $agentPraentData->count('id');

            if ($count_id >= $level['upgraded']['first_lower_agent_count']) {
                $result = static::validate($level['level'], $member['member_id']);
                if ($result) {
                    \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]下级一级分销人数[实际-'.$count_id.'条件-'.$level['upgraded']['first_lower_agent_count'].']');
                    static::upgrade($level, $member['member_id'], $result);
                }
            }
        }
        return;
    }

    /**
     * 佣金升级入口
     * @param $memberId
     */
    public static function commission($memberId)
    {
        //等级 升级条件
        $levels = static::getLevelUpgraded();
        if (!$levels) {
            return;
        }
        //分销商数据
        $agentModel = Agents::getAgentByMemberId($memberId)->first();
        if (!$agentModel) {
            return;
        }

        foreach ($levels as $level) {
            if (isset($level['upgraded']['settle_money'])) {
                if ($agentModel->commission_total >= $level['upgraded']['settle_money']) {
                    $result = static::validate($level['level'], $memberId);
                    if ($result) {
                        \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]佣金[实际-'.$agentModel->commission_total.'条件-'.$level['upgraded']['settle_money'].']');
                        static::upgrade($level, $memberId, $result);
                    }
                    break;
                }
            }
        }
        return;
    }

    /**
     * 购买指定商品
     */
    public static function goods($goodsId, $memberId)
    {

        //等级 升级条件
        $levels = static::getLevelUpgraded();
        if (!$levels) {
            return;
        }
        //分销商数据
        $agentModel = Agents::getAgentByMemberId($memberId)->first();
        if (!$agentModel) {
            return;
        }

        foreach ($levels as $level) {
            if (isset($level['upgraded']['goods'])) {
                if ($goodsId == $level['upgraded']['goods']) {
                    $result = static::validate($level['level'], $memberId);
                    if ($result) {
                        \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]购买指定商品[实际-'.$goodsId.'条件-'.$level['upgraded']['goods'].']');
                        static::upgrade($level, $memberId, $result);
                    }
                    break;
                }
            }
        }
        return;
    }

    /**
     * 购买指定商品之一
     */
    public static function manyGood($goodsId, $memberId)
    {

        //等级 升级条件
        $levels = static::getLevelUpgraded();
        if (!$levels) {
            return;
        }
        //分销商数据
        $agentModel = Agents::getAgentByMemberId($memberId)->first();
        if (!$agentModel) {
            return;
        }

        foreach ($levels as $level) {
            if (isset($level['upgraded']['many_good'])) {
//                if (empty(($level['upgraded']['self_order_after']))) {
                    if (in_array($goodsId, $level['upgraded']['many_good'])) {
                        $result = static::validate($level['level'], $memberId);
                        if ($result) {
                            \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]购买指定商品[实际-'.$goodsId.'条件-'.$level['upgraded']['many_good'].']');
                            static::upgrade($level, $memberId, $result);
                        }
                        break;
                    }
//                }
            }
        }
        return;
    }

    /**
     * 购买指定商品付款后
     */
    public static function goodsAfterPaid($goodsId, $memberId)
    {

        //等级 升级条件
        $levels = static::getLevelUpgraded();
        if (!$levels) {
            return;
        }
        //分销商数据
        $agentModel = Agents::getAgentByMemberId($memberId)->first();
        if (!$agentModel) {
            return;
        }

        foreach ($levels as $level) {
            if (isset($level['upgraded']['goods'])) {
                if ($level['upgraded']['self_order_after'] == 1) {
                    if ($goodsId == $level['upgraded']['goods']) {
                        $result = static::validate($level['level'], $memberId);
                        if ($result) {
                            \Yunshop\Commission\models\Log::addLog($result['agent_level_id'], $level['id'], $result, '[或]购买指定商品[实际-'.$goodsId.'条件-'.$level['upgraded']['goods'].']');
                            static::upgrade($level, $memberId, $result);
                        }
                        break;
                    }
                }
            }
        }
        return;
    }

    /**
     * @return array
     */
    public static function getLevelUpgraded()
    {
        $result = AgentLevel::getLevels()->orderBy('level', 'desc')->get();
        $levelData = [];
        foreach ($result as $key => $level) {
            $levelData[$key] = [
                'id' => $level['id'],
                'level' => $level['level'],
                'upgraded' => unserialize($level['upgraded']),
            ];
        }
        return $levelData;
    }

    /**
     * @param $level
     * @param $memberId
     * @return bool|Agents
     */
    public static function validate($level, $memberId)
    {

        $agentModel = Agents::getAgentByMemberId($memberId)->first();
        if ($agentModel->agent_not_upgrade) {
            return false;
        }
        $agentLevel = isset($agentModel->agentLevel['level']) ? $agentModel->agentLevel['level'] : 0;
        if ($level <= $agentLevel) {
            return false;
        }
        return $agentModel;
    }

    public static function upgrade($level, $memberId, $agent)
    {
        $set = Setting::get('plugin.commission');
        $newLevel = AgentLevel::getAgentLevelByid($level['id'])->toArray();
        $oldLevel = AgentLevel::getAgentLevelByid($agent->agent_level_id);

        if (!$oldLevel) {
            $oldLevel['name'] = '默认等级';
            $oldLevel['first_level'] = isset($set['first_level']) ? $set['first_level'] : 0;
            $oldLevel['second_level'] = isset($set['second_level']) ? $set['second_level'] : 0;
            $oldLevel['third_level'] = isset($set['third_level']) ? $set['third_level'] : 0;
        }

        $member = YzMember::getMemberByMemberId($memberId)->first();
        \Log::debug("监听hasOneFans",[$member->hasOneFans,$member]);
        if($member->hasOneFans){
            $noticeData = [
                'newLevel' => $newLevel,
                'oldLevel' => $oldLevel,
                'memberFans' => $member->hasOneFans->toArray(),
            ];
            MessageService::upgrade($noticeData);
        }
        \Log::debug("监听分销升级",[$level['id'], $memberId]);
        Agents::updatedLevelByMemberId($level['id'], $memberId);
        //招商中心是否可以升级
       // CenterUpgradeService::handle($memberId);
        if (app('plugins')->isEnabled('merchant')){
            CenterUpgradeService::handle($memberId);
        }

    }

}