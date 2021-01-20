<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/26
 * Time: 11:10 AM
 */

namespace Yunshop\Mryt\services;


use app\common\facades\Setting;
use app\common\models\Order;
use Yunshop\Mryt\common\models\Log;
use Yunshop\Mryt\common\models\MemberParent;
use Yunshop\Mryt\common\models\MemberReferralAward;
use Yunshop\Mryt\common\models\MemberTeamAward;
use Yunshop\Mryt\common\models\TierAward;
use Yunshop\Mryt\models\MrytMemberModel;

class TestService
{
    // 升级的会员uid
    private $uid;
    // 已获得奖励的会员等级奖励比例, 用于验证极差
    private $finishAwardAmount = 0;
    // 会员上线层级 初始为1
    private $agent_level = 1;
    // 会员上级
    private $agent_list;
    // 产生团队奖的等级id
    private $team_level_id = 0;
    // 产生感恩奖的等级id
    private $grat_level_id = 0;
    // 层级
    private $hierarchy = 0;
    // 平级奖相关
    private $tiers = [];
    private $set;
    private $orderId;

    public function __construct($uid, $uniacid, $orderId)
    {
        $this->uid = $uid;
        \YunShop::app()->uniacid = $uniacid;
        Setting::$uniqueAccountId = $uniacid;
        $this->uniacid = $uniacid;
        $this->set = CommonService::getSet();
        $this->orderId = $orderId;
    }

    public function handleAward()
    {
        $isAward = $this->verify();
        if (!$isAward) {
            $remark = "订单[{$this->orderId}]实付金额为0";
            Log::create([
                'uniacid' => $this->uniacid,
                'uid' => $this->uid,
                'type' => 1,
                'source_id' => $this->orderId,
                'remark' => $remark
            ]);
            return;
        }

        $agent_list = MemberParent::with([
            'hasOneMrytMember' => function ($mryt_member) {
                $mryt_member->with(['hasOneLevel']);
            }
        ])->where('member_id', $this->uid)->get();
        if ($agent_list->isEmpty()) {
            $remark = "会员[{$this->uid}]没有上级";
            Log::create([
                'uniacid' => $this->uniacid,
                'uid' => $this->uid,
                'type' => 2,
                'source_id' => $this->uid,
                'remark' => $remark
            ]);
            // 没有上级
            return;
        }
        $this->agent_list = $agent_list;
        // 直推奖
        $this->referralAward();
        // 团队奖
        $this->teamAward();
    }

    /**
     * @name 验证订单实付金额
     * @author
     * @return bool
     */
    private function verify()
    {
        if ($this->set['is_award'] == 1) {
            return true;
        }
        if (intval($this->orderId) != 0) {
            $orderModel = Order::find(intval($this->orderId));
            if ($orderModel && $orderModel->price <= 0) {
                return false;
            }
        }
        return true;
    }

    private function referralAward()
    {
        // 直接 查询member 直属上级,查询 上级等级 设置 的 直推奖励 进行奖励
        $agent = $this->agent_list->where('level', $this->agent_level)->first();
        if (!$agent || !$agent->hasOneMrytMember) {
            return;
        }
        // 基础设置默认 直推奖 金额
        $direct = $this->set['push_prize'];
        $mryt_level = $agent->hasOneMrytMember->hasOneLevel;
        // 如果有等级 取等级设置 直推奖 金额
        if ($mryt_level && $mryt_level->direct) {
            $direct = $mryt_level->direct;
        }
        if (!$direct) {
            return;
        }
        $model = MemberReferralAward::create([
            'uniacid' => $this->uniacid,
            'uid' => $agent->parent_id,
            'source_uid' => $this->uid,
            'amount' => $direct,
            'status' => 1 // 直接存入收入, status 都是1
        ]);
        (new IncomeService($model, $this->set['referral_name']))->handle();
        MessageService::awardMessage($this->uniacid, $agent->parent_id, "{$this->set['referral_name']}-{$direct}");
        if ($agent->hasOneMrytMember) {
            $agent->hasOneMrytMember->direct += $direct;
            $agent->hasOneMrytMember->save();
        }
    }

    private function teamAward()
    {
        // 上级
        $agent = $this->agent_list->where('level', $this->agent_level)->first();
        if (!$agent) {
            return;
        }
        // 如果没有会员 或 没有会员等级
        if (!$agent->hasOneMrytMember || !$agent->hasOneMrytMember->hasOneLevel) {
            // 继续执行
            $this->superposition();
        }
        // 会员等级
        $mryt_level = $agent->hasOneMrytMember->hasOneLevel;
        // 会员等级没设置团队奖
        if (!$mryt_level->team) {
            // 继续执行
            $this->superposition();
        }
        // 等级设置的团队奖 减去 已经奖励的团队奖
        $amount = $mryt_level->team - $this->finishAwardAmount;
        // 感恩奖
        if ($amount <= 0) {
            // 每个等级的感恩奖只会获得一次
            if ($agent->hasOneMrytMember->level != $this->grat_level_id && $agent->hasOneMrytMember->level == $this->team_level_id) {
                if (($this->agent_level - $this->hierarchy) == 1) {
                    $this->gratitude();
                }
            }
            // 继续执行
            $this->superposition();
        } else {
            // 如果 当前等级 不等于 一级获得团队奖的等级
            if ($agent->hasOneMrytMember->level != $this->team_level_id) {
                $this->addMemberTeamAward($mryt_level, $agent, $amount);
            }
            // 继续执行
            $this->superposition();
        }
    }

    // 感恩奖
    private function gratitude()
    {
        $agent = $this->agent_list->where('level', $this->agent_level)->first();
        if ($agent) {
            $this->addGratitude($agent);
        }
        $this->superposition();
    }

    private function addMemberTeamAward($mryt_level, $agent, $amount)
    {
        $thankful = $mryt_level->thankful > 0 ? $mryt_level->thankful : 0;
        $model = MemberTeamAward::create([
            'uniacid' => $this->uniacid,
            'uid' => $agent->parent_id,
            'level_id' => $agent->hasOneMrytMember->level,
            'source_uid' => $this->uid,
            'award_type' => 1,
            'level_team_award_amount' => $mryt_level->team,
            'lower_level_team_award_amount' => $this->finishAwardAmount,
            'level_gratitude_amount' => $thankful,
            'amount' => $amount,
            'status' => 1 // 直接存入收入, status 都是1
        ]);
        (new IncomeService($model, $this->set['team_name']))->handle();
        MessageService::awardMessage($this->uniacid, $agent->parent_id, "{$this->set['team_name']}-{$amount}");
        if ($agent->hasOneMrytMember) {
            $agent->hasOneMrytMember->team += $amount;
            $agent->hasOneMrytMember->save();
        }
        // 赋值 已完成奖励
        $this->finishAwardAmount = $mryt_level->team;
        // 赋值 获得团队奖的等级
        $this->team_level_id = $agent->hasOneMrytMember->level;
        // 赋值 获得团队奖的 等级id 与 权重 与 层级
        $this->pushTiers($mryt_level, $agent->parent_id);
        /*
         * 层级 用于验证感恩奖
         * [ps:只有当前层级 - 已获得团队奖的层级 = 1 才会获得感恩奖]
         * 也就是不能断层, 总经理1->总监1->总经理2, 那么总经理1不会获得感恩奖
         */
        $this->hierarchy = $this->agent_level;
    }

    private function addGratitude($agent)
    {
        $mryt_level = $agent->hasOneMrytMember->hasOneLevel;
        $thankful = $mryt_level->thankful > 0 ? $mryt_level->thankful : 0;
        if ($thankful <= 0) {
            return;
        }
        $this->grat_level_id = $agent->hasOneMrytMember->level;
        $model = MemberTeamAward::create([
            'uniacid' => $this->uniacid,
            'uid' => $agent->parent_id,
            'level_id' => $agent->hasOneMrytMember->level,
            'source_uid' => $this->uid,
            'award_type' => 2,
            'level_team_award_amount' => $mryt_level->team,
            'lower_level_team_award_amount' => $this->finishAwardAmount,
            'level_gratitude_amount' => $thankful,
            'amount' => $thankful,
            'status' => 1 // 直接存入收入, status 都是1
        ]);
        (new IncomeService($model, $this->set['thanksgiving_name']))->handle();
        MessageService::awardMessage($this->uniacid, $agent->parent_id, "{$this->set['thanksgiving_name']}-{$thankful}");
        if ($agent->hasOneMrytMember) {
            $agent->hasOneMrytMember->thankful += $thankful;
            $agent->hasOneMrytMember->save();
        }
    }

    private function tierAward()
    {
        // 上级
        $agent = $this->agent_list->where('level', $this->agent_level)->first();
        if (!$agent) {
            return;
        }
        // 如果没有会员 或 没有会员等级
        if (!$agent->hasOneMrytMember || !$agent->hasOneMrytMember->hasOneLevel) {
            return;
        }
        // 会员等级
        $mryt_level = $agent->hasOneMrytMember->hasOneLevel;
        // 如果当前会员的等级 没有 进行过团队分红,返回
        if (!array_key_exists($mryt_level->id, $this->tiers)) {
            return;
        }
        // 如果当前会员的等级的平级奖层级 小于等于 等级设置的平级奖层级,返回
        if ($mryt_level->tier <= $this->tiers[$mryt_level->id]['awarded_tier']) {
            return;
        }
        // 会员等级没设置平级奖金额
        if ($mryt_level->tier_amount <= 0) {
            return;
        }
        $tier = $this->tiers[$mryt_level->id]['awarded_tier'] + 1;
        // 获得分红
        $model = TierAward::create([
            'uniacid' => $this->uniacid,
            'uid' => $agent->parent_id,
            'source_uid' => $this->uid,
            'amount' => $mryt_level->tier_amount,
            'tier' => $tier,
            'level_id' => $agent->hasOneMrytMember->level,
            'level_tier' => $mryt_level->tier,
            'status' => 1 // 直接存入收入, status 都是1
        ]);
        (new IncomeService($model, $this->set['tier_name']))->handle();
        MessageService::awardMessage($this->uniacid, $agent->parent_id, "{$this->set['tier_name']}-{$mryt_level->tier_amount}");
        // 平级奖层级 +1
        $this->tiers[$mryt_level->id]['awarded_tier'] = $tier;
    }

    private function superposition()
    {
        // 完成层级更改
        $this->agent_level += 1;
        // 平级奖
        $this->tierAward();
        // 继续执行团队奖
        $this->teamAward();
    }

    private function pushTiers($mryt_level, $uid)
    {
        /*
         * 赋值 获得团队奖的 等级id 与 权重 与 层级
         * 总经理A->总监->总经理   总经理A会获得平级奖
         * 总监A->总经理->总监   总监A不会获得平级奖
         * 以下代码处理逻辑:获得团队奖的等级权重比上一个获得团队奖的等级权重大,删除原有数据
         */
        if (!array_key_exists($mryt_level->id, $this->tiers)) {
            if (!$this->tiers) {
                $this->tiers[$mryt_level->id] = [
                    'level_weight' => $mryt_level->level_weight,
                    'awarded_tier' => 0,
                    'uid' => $uid
                ];
            } else {
                foreach ($this->tiers as $level_id => $info) {
                    if ($mryt_level->level_weight > $info['level_weight']) {
                        unset($this->tiers[$level_id]);
                    }
                    $this->tiers[$mryt_level->id] = [
                        'level_weight' => $mryt_level->level_weight,
                        'awarded_tier' => 0,
                        'uid' => $uid
                    ];
                }
            }
        }
    }
}