<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/15
 * Time: 上午11:31
 */

namespace Yunshop\ClockIn\services;


use app\common\models\Member;
use Yunshop\ClockIn\models\ClockPayLogModel;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\Income;
use Yunshop\Commission\services\AgentService;
use Yunshop\Commission\services\CommissionOrderService;
use Yunshop\Commission\services\MessageService;
use Yunshop\Commission\services\OrderCreatedService;
use Yunshop\Commission\services\UpgradeService;

class CommissionService
{
    private $uid;
    private $amount;
    private $order_sn;
    private $set;
    private $requestAgents;

    public function __construct($uid, $amount, $order_sn)
    {
        $this->uid = $uid;
        $this->amount = $amount;
        $this->order_sn = $order_sn;
        $this->set = \Setting::get('plugin.clock_in');
    }

    public function handle()
    {
        // 没有 或 未开启分销插件，返回
        $exist_commission = app('plugins')->isEnabled('commission');
        if (!$exist_commission) {
            return;
        }

        // 如果 打卡设置未开启分销，返回
        if (!$this->set['is_commission']) {
            return;
        }

        // 如果 没有上层关系链， 返回
        $this->requestAgents = OrderCreatedService::getParentAgents($this->uid, $this->set['self_buy']);
        if (!$this->requestAgents) {
            return;
        }

        //确认分销商层级
        $agents = AgentService::getParentAgents($this->requestAgents->toArray(), $this->set);

        //分销订单
        $this->commissionOrdersData($agents);
    }

    private function commissionOrdersData($agents)
    {
        //分销商分别添加 分销订单
        foreach ($agents as $level => $agent) {

            if (empty($agent['agent']) || $agent['agent']['is_black']) {
                continue;
            }

            //该分销商所在层级
            $hierarchy = CommissionOrderService::getHierarchy($level);
            $agent['agent']['hierarchy'] = $level;

            //获取佣金 计算金额 计算公式 佣金比例 分销订单商品等数据
            $commission = $this->getCommission($this->amount, $agent['agent'], $this->set);

            if ($commission['commission'] > 0) {
                $this->addCommissionOrder($commission, $agent, $hierarchy, $level);
            }
        }
    }

    private function getCommission($amount, $agent, $set)
    {
        $commissionAmount = 0;
        $commission = 0;

        $countAmount = $this->getCountAmount($amount, $agent, $set);
        $commissionAmount += $countAmount['amount'];//分佣计算金额
        $formula = $countAmount['method'];//分佣计算方式
        $commissionRate = $countAmount['rate'];//分佣比例
        $commission += $countAmount['commission'];//佣金

        return [
            'commission_amount' => $commissionAmount,
            'formula' => $formula,
            'commission_rate' => $commissionRate,
            'commission' => $commission,
            //'orderGoods' => $orderGoods
        ];
    }

    private function getCountAmount($amount, $agent, $set)
    {
        //获取对应层级比例
        $rate = $this->getRate($agent, $set);
        //结算金额乘以比例
        $commission = $amount / 100 * $rate;

        if (app('plugins')->isEnabled('commission')) {
            $clock_set = \Setting::get('plugin.clock_in');
            $clock_name = $clock_set['plugin_name'] ?: '早起打卡';
        }

        return [
            'amount' => $amount,
            'method' => $clock_name . '分销奖励',
            'rate' => $rate,
            'commission' => $commission
        ];
    }

    private function getRate($agent, $set)
    {
        if (empty($agent['agent_level'])) {
            return $set[$agent['hierarchy']];
        } else {
            if ($set['commission_level'] && $set['commission_level'][$agent['agent_level']['id']]) {
                return $set['commission_level'][$agent['agent_level']['id']][$agent['hierarchy']];
            }
            return $agent['agent_level'][$agent['hierarchy']];
        }
    }

    private function addCommissionOrder($commission, $agent, $hierarchy, $level)
    {
        $clock = ClockPayLogModel::select('id')->where('order_sn', $this->order_sn)->first();
        //分销订单数据
        $orderData = [
            'uniacid' => \YunShop::app()->uniacid,
            'ordertable_type' => ClockPayLogModel::class,
            'ordertable_id' => $clock->id,
            'buy_id' => $this->uid,
            'member_id' => $agent['member_id'],
            'hierarchy' => $hierarchy,//分销层级
            'commission_amount' => $commission['commission_amount'],// 计算金额,
            'formula' => $commission['formula'],// 计算公式
            'commission_rate' => $commission['commission_rate'],// 佣金比例
            'commission' => $commission['commission'],// 佣金
            'status' => '2',//打卡分销直接就是[已结算]状态
            'settle_days' => $this->set['settle_days'],
            'created_at' => time(),
        ];

        $commissionOrderIds = CommissionOrder::insertGetId($orderData);

        //更新累计佣金
        $this->updateCommission($commission['commission'], $agent['member_id']);
        //插入收入
        $this->addIncome($agent['member_id'], $commission['commission'], $commissionOrderIds);
        $this->notice($commission, $agent, $hierarchy, $level);
    }

    public function notice($commission, $agent, $hierarchy, $level)
    {
        $noticeData = [
            'order' => [],
            'goods' => [],
            'agent' => $agent['has_one_fans'],
            'buy' => $this->requestAgents->hasOneFans,
            'commission' => $commission['commission'],
            'hierarchy' => $hierarchy
        ];

        if ($this->set['self_buy'] && $level != "first_level") {
            MessageService::createdOrder($noticeData);
        } elseif (!$this->set['self_buy']) {
            MessageService::createdOrder($noticeData);
        }
    }

    private function updateCommission($amount, $uid)
    {
        $requestAgent = Agents::updateCommission($amount, $uid, 'plus');

        if ($requestAgent) {
            // 佣金升级
            UpgradeService::commission($uid);
        }
        return $requestAgent;
    }

    private function addIncome($uid, $amount, $id)
    {
        if (app('plugins')->isEnabled('commission')) {
            $clock_set = \Setting::get('plugin.clock_in');
            $clock_name = $clock_set['plugin_name'] ?: '早起打卡';
        }

        $incomeData = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $uid,
            'incometable_type' => CommissionOrder::class,
            'incometable_id' => $id,
            'type_name' => $clock_name . '分销奖励',
            'amount' => $amount,
            'status' => '0',
            'detail' => '',//收入明细数据
            'create_month' => date("Y-m"),
        ];

        //插入收入
        $incomeModel = new Income();
        $incomeModel->setRawAttributes($incomeData);
        $incomeModel->save();

        $this->noticeData($uid, $amount);
    }

    public function noticeData($uid, $amount)
    {
        $member = Member::getMemberByUid($uid)->with('hasOneFans')->first();
        $notice = [
            'amount' => $amount,
            'agent' => $member->hasOneFans,
        ];
        MessageService::statement($notice);
    }
}