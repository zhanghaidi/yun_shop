<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/26
 * Time: 11:10 AM
 */

namespace Yunshop\Mryt\services;


use Yunshop\Mryt\common\models\MemberParent;
use Yunshop\Mryt\common\models\OrderParentingAward;
use Yunshop\Mryt\common\models\OrderTeamAward;
use Yunshop\Mryt\common\models\CashierOrder;
use Yunshop\Mryt\common\models\StoreOrder;

class StoreOrderAwardService
{
    // 销售佣金记录
    private $order;
    // 会员上线层级 初始为1
    private $agent_level = 1;
    // 会员上级
    private $agent_list;
    // 已完成奖励比例 用于极差
    private $finishiAwardRatio = 0;
    // 已完成奖励模型
    private $finishiAwardModel;
    // 产生团队奖的等级id
    private $team_level_id = 0;
    // 产生育人奖的等级id
    private $parenting_level_id = 0;
    // 层级
    private $hierarchy = 0;
    // 会员id
    private $uid = 0;

    public function __construct($order)
    {
        $this->order = $order;
        \YunShop::app()->uniacid = $order->uniacid;
    }

    public function handleAward()
    {
        $this->setUid();
        $agent_list = MemberParent::with([
            'hasOneMrytMember' => function ($mryt_member) {
                $mryt_member->with(['hasOneLevel']);
            }
        ])->where('member_id', $this->uid)->get();
        if ($agent_list->isEmpty()) {
            // 存order  没有上级
            return;
        }
        $this->agent_list = $agent_list;
        // 团队奖
        $this->teamAward();
    }

    private function teamAward()
    {
        $agent = $this->agent_list->where('level', $this->agent_level)->first();
        if (!$agent) {
            return;
        }
        if (!$agent->hasOneMrytMember || !$agent->hasOneMrytMember->hasOneLevel) {
            $this->superposition();
        }
        $mryt_level = $agent->hasOneMrytMember->hasOneLevel;
        if (!$mryt_level->team_manage_ratio) {
            $this->superposition();
        }
        // 当前等级的团队管理奖比例 - 上级获得团队管理奖的比例  <= 0
        $ratio = $mryt_level->team_manage_ratio - $this->finishiAwardRatio;
        if ($ratio <= 0 && $this->team_level_id == $agent->hasOneMrytMember->level) {
            if ($agent->hasOneMrytMember->level != $this->parenting_level_id) {
                if ($this->parenting_level_id) {
                    if (($this->agent_level - $this->hierarchy) == 1) {
                        $this->parentingAward();
                    }
                } else {
                    $this->parentingAward();
                }
            } else {
                // 继续执行
                $this->superposition();
            }

        } else {
            if ($agent->hasOneMrytMember->level != $this->team_level_id) {
                $this->addOrderTeamAward($ratio, $agent, $mryt_level);
            }
            $this->superposition();
        }
    }

    private function parentingAward()
    {
        $agent = $this->agent_list->where('level', $this->agent_level)->first();
        if ($this->finishiAwardModel && $agent) {
            $mryt_level = $agent->hasOneMrytMember->hasOneLevel;
            $train_ratio = $mryt_level->train_ratio > 0 ? $mryt_level->train_ratio : 0;
            $amount = $this->finishiAwardModel->amount * $train_ratio / 100;
            if ($amount > 0) {
                $this->parenting_level_id = $agent->hasOneMrytMember->level;
                $this->hierarchy = $this->agent_level;
                $model = OrderParentingAward::create([
                    'uniacid' => $this->order->uniacid,
                    'uid' => $agent->parent_id,
                    'level_id' => $agent->hasOneMrytMember->level,
                    'team_uid' => $this->finishiAwardModel->uid,
                    'team_log_id' => $this->finishiAwardModel->id,
                    'team_amount' => $this->finishiAwardModel->amount,
                    'parenting_ratio' => $train_ratio,
                    'amount' => $amount,
                    'status' => 1
                ]);
                (new IncomeService($model, '育人奖'))->handle();
                MessageService::awardMessage($this->order->uniacid, $agent->parent_id, "育人奖-{$amount}");
                if ($agent->hasOneMrytMember) {
                    $agent->hasOneMrytMember->train += $amount;
                    $agent->hasOneMrytMember->save();
                }
            }
        }
        $this->superposition();
    }

    private function addOrderTeamAward($ratio, $agent, $mryt_level)
    {
        $amount = $this->order->price * $ratio / 100;
        if ($amount > 0) {
            $res_model = OrderTeamAward::create([
                'uniacid' => $this->order->uniacid,
                'uid' => $agent->parent_id,
                'level_id' => $agent->hasOneMrytMember->level,
                'log_uid' => $this->uid,
                'log_id' => $this->order->order_sn,
                'log_amount' => $this->order->price,
                'award_ratio' => $ratio,
                'lower_award_ratio' => $this->finishiAwardRatio,
                'amount' => $amount,
                'status' => 1,
                'type' => 2,
            ]);
            (new IncomeService($res_model, '团队管理奖'))->handle();
            MessageService::awardMessage($this->order->uniacid, $agent->parent_id, "团队管理奖-{$amount}");
            if ($agent->hasOneMrytMember) {
                $agent->hasOneMrytMember->team_manage += $amount;
                $agent->hasOneMrytMember->save();
            }
            $this->finishiAwardModel = $res_model;
            // 赋值 已完成奖励比例
            $this->finishiAwardRatio = $mryt_level->team_manage_ratio;
            $this->team_level_id = $agent->hasOneMrytMember->level;
        }
        $this->superposition();
    }

    private function superposition()
    {
        // 完成层级更改
        $this->agent_level += 1;
        $this->teamAward();
    }

    private function setUid()
    {
        if ($this->order->plugin_id == 31) {
            $cashierOrder = CashierOrder::select()->where('order_id', $this->order->id)->with('hasOneStore')->first();
            $this->uid = $cashierOrder->hasOneStore->uid;
        } elseif ($this->order->plugin_id == 32) {
            $cashierOrder = StoreOrder::select()->where('order_id', $this->order->id)->with('hasOneStore')->first();
            $this->uid = $cashierOrder->hasOneStore->uid;
        }

    }
}