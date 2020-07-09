<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/4/9
 * Time: 16:35
 */

namespace Yunshop\Commission\services;

use Yunshop\Commission\Listener\OrderCreatedListener;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\Commission;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\CommissionOrderGoods;

class ExpandDividendService
{
    private $order;
    private $set;
    private $expand_set;
    private $requestAgents;
    public function __construct($order, $expand_set, $set)
    {
        $this->order = $order;
        $this->expand_set = $expand_set;
        $this->set = $set;
    }

    public function getAgent()
    {
        //获取上级关系链
        $this->requestAgents = OrderCreatedService::getParentAgents($this->order->uid, 0);
        \Log::debug('订单分销' . $this->order->id . '关系链', $this->requestAgents);

        //确认分销商层级
        $agents = $this->getParentAgents($this->requestAgents->toArray());
        \Log::debug('订单分销' . $this->order->id . '层级', $agents);

        if (!isset($agents['first'])) {
            \Log::info('订单分销没有上级',print_r($this->order->id,true));
            return;
        }

        $this->dividend($agents);

    }

    public function dividend($agents)
    {
        //分销商分别添加 分销订单
        foreach ($agents as $level => $agent) {
            if (!$agent) {
                \Log::info('分销异常'.$this->order->id,print_r($level,true));
                continue;
            }

            if (empty($agent['agent']) || $agent['agent']['is_black']) {
                \Log::info('订单分销不是分销商或者被拉黑'.$this->order->id,print_r($agent,true));
                continue;
            }

            //该分销商所在层级
            $hierarchy = $this->getHierarchy($level);
            $agent['agent']['hierarchy'] = $level;

            //获取佣金 计算金额 计算公式 佣金比例 分销订单商品等数据
            $commission = $this->getCommission($this->order, $agent['agent'], $this->set, $level);

            if ($commission['commission'] > 0) {
                $this->addCommissionOrder($commission, $agent, $hierarchy, $level);
            } else {
                \Log::info('订单分销没有佣金'.$this->order->id,print_r($agent,true));
            }

        }
    }

    /**
     * @param $requestAgents
     * @param $set
     * @return mixed
     * 确认分销商层级
     */
    public function getParentAgents($requestAgents)
    {
        $agentData = [];
        // 如果开启内购并且该会员是分销商，该会员为一级
        $first_level = $requestAgents['belongs_to_parent'];
        $second_level = $first_level['belongs_to_parent'];
//        $third_level = $second_level['belongs_to_parent'];
        unset($first_level['belongs_to_parent']);
        unset($second_level['belongs_to_parent']);

        $agentData['first'] = $first_level;

        if ($first_level['agent']['agent_level_id'] == $second_level['agent']['agent_level_id'] && $requestAgents['agent']['agent_level_id'] == $second_level['agent']['agent_level_id']) {
            $agentData['second'] = $second_level;
        }

        return $agentData;
    }

    /**
     * @param $level
     * @return string
     * 分销商层级转换
     */
    public function getHierarchy($level)
    {
        switch ($level) {
            case 'first':
                $hierarchy = '1';//分销层级
                break;
            case 'second':
                $hierarchy = '2';//分销层级
                break;
            default:
                $hierarchy = '3';//分销层级
        }
        return $hierarchy;
    }

    /**
     * @param $orderModel
     * @param $agent
     * @param $set
     * @return array
     * 获取佣金 计算金额 计算公式 佣金比例 分销订单商品等数据
     */
    public function getCommission($orderModel, $agent, $set, $hierarchy)
    {
        $orderGoods = $orderModel->hasManyOrderGoods;
        $commissionAmount = 0;
        $formula = '';
        $commissionRate = 0;
        $commissionPay = 0;
        $commission = 0;


        //临时解决分销等级删除后，分销订单不能使用默认等级计算问题
        if ($agent['agent_level_id']) {
            $agentLevelModel = AgentLevel::find($agent['agent_level_id']);
            if (!$agentLevelModel) {
                $agent['agent_level_id'] = 0;
            }
        }

        foreach ($orderGoods as $key => $og) {

            //获取商品分销设置信息
            $commissionGoods = Commission::getGoodsById($og->goods_id)->first();
            if (!$commissionGoods->is_commission) {
                continue;
            }
            //分销订单商品 商品分销设置信息默认值
            $orderGoods[$key]['commissionGoods'] = [
                'has_commission' => '0',
                'commission_rate' => $agent['agent_level'][$agent['hierarchy']],
                'commission_pay' => 0,
            ];
            //分销订单商品 商品信息
            $orderGoods[$key]['goods'] = [
                'name' => $og->title,
                'thumb' => $og->thumb,
            ];
            if ($commissionGoods) {
                $countAmount = $this->getCountAmount($orderModel, $og, $agent, $set, $hierarchy);
                $commissionAmount += $countAmount['amount'];//分佣计算金额
                $formula = $countAmount['method'].'(定制分红)';//分佣计算方式
                $commissionRate = $countAmount['rate'];//分佣比例
                $commission += $countAmount['commission'];//佣金
            }
        }
        return [
            'commission_amount' => $commissionAmount,
            'formula' => $formula,
            'commission_rate' => $commissionRate,
            'commission' => $commission,
            'orderGoods' => $orderGoods
        ];
    }

    /**
     * @param $orderModel
     * @param $orderGoods
     * @param $agent
     * @param $set
     * @param $hierarchy
     * @return array
     * 佣金计算规则 计算佣金 计算方式
     */
    public function getCountAmount($orderModel, $orderGoods, $agent, $set, $hierarchy)
    {
        $amount = 0;
        $method = "";
        if (isset($set['culate_method_plus'])) {
            foreach ($set['culate_method_plus'] as $key => $plus) {
                $methods = $key . 'Plus';
                $amount += CommissionOrderService::$methods($orderGoods, $orderModel);
                $method .= "+" . CommissionOrderService::getMethodName($key);
            }
        }
        if (isset($set['culate_method_minus'])) {
            foreach ($set['culate_method_minus'] as $key => $minus) {
                $methods = $key . 'Minus';
                $amount -= CommissionOrderService::$methods($orderGoods, $orderModel);
                $method .= "-" . CommissionOrderService::getMethodName($key);
            }
        }
        //获取对应层级比例
        $rate = $this->getRate($agent, $hierarchy);
        //结算金额乘以比例
        $commission = $amount / 100 * $rate;
        return [
            'amount' => $amount,
            'method' => $method,
            'rate' => $rate,
            'commission' => $commission
        ];
    }

    /**
     * @param $agent
     * @param $hierarchy
     * @return mixed
     * 获取佣金比例
     * 权重: 分销商等级比例->默认比例
     */
    public function getRate($agent,$hierarchy)
    {
        if (empty($agent['agent_level'])) {
            return $this->expand_set['dividend'][$hierarchy]['level_0'];
        } else {
            return $this->expand_set['dividend'][$hierarchy]['level_'.$agent['agent_level_id']];
        }
    }

    /**
     * @param $commission
     * @param $agent
     * @param $hierarchy
     * @param $level
     */
    public function addCommissionOrder($commission, $agent, $hierarchy, $level)
    {
        $config = \app\common\modules\shop\ShopConfig::current()->get('plugin.commission');
        //分销订单数据
        $orderData = [
            'uniacid' => \YunShop::app()->uniacid,
            'ordertable_type' => $config['order_class'],
            'ordertable_id' => $this->order->id,
            'buy_id' => $this->order->uid,
            'member_id' => $agent['member_id'],
            'hierarchy' => $hierarchy,//分销层级
            'commission_amount' => $commission['commission_amount'],// 计算金额,
            'formula' => $commission['formula'],// 计算公式
            'commission_rate' => $commission['commission_rate'],// 佣金比例
            'commission' => $commission['commission'],// 佣金
            'status' => '0',
            'settle_days' => $this->set['settle_days'],
            'created_at' => time(),
        ];
        //添加分销订单数据 生成ID
        $commissionOrderIds = CommissionOrder::insertGetId($orderData);

        $this->notice($commission, $agent, $hierarchy, $level);
        $this->addCommissionOrderGoods($commission, $commissionOrderIds);

    }


    /**
     * @param $agent
     * @param $commission
     * @param $hierarchy
     * @param $level
     */
    public function notice($commission, $agent, $hierarchy, $level)
    {
        $noticeData = [
            'order' => $this->order,
            'goods' => $this->order->hasManyOrderGoods->toArray(),
            'agent' => $agent['has_one_fans'],
            'buy' => $this->requestAgents->hasOneFans,
            'commission' => $commission['commission'],
            'hierarchy' => $hierarchy
        ];

//        if ($this->set['self_buy'] && $level != "first_level") {
//            MessageService::createdOrder($noticeData);
//        } elseif (!$this->set['self_buy']) {
            MessageService::createdOrder($noticeData);
//        }
    }

    /**
     * @param $commission
     * @param $commissionOrderIds
     */
    public function addCommissionOrderGoods($commission, $commissionOrderIds)
    {
        //分销订单商品数据
        foreach ($commission['orderGoods'] as $orderGood) {
            $orderGoodsData[] = [
                'commission_order_id' => $commissionOrderIds,
                'name' => $orderGood->goods['name'],
                'thumb' => $orderGood->goods['thumb'],
                'has_commission' => $orderGood->commissionGoods['has_commission'],
                'commission_rate' => $orderGood->commissionGoods['commission_rate'],
                'commission_pay' => $orderGood->commissionGoods['commission_pay']
            ];
        }

        if (!empty($orderGoodsData)) {
            //添加分销订单商品数据
            CommissionOrderGoods::insert($orderGoodsData);
        }
    }

}