<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/17
 * Time: 3:27 PM
 */

namespace Yunshop\Nominate\services;


use app\common\services\finance\BalanceChange;
use Yunshop\Nominate\models\MemberChild;
use Yunshop\Nominate\models\ShopMember;
use Yunshop\Nominate\models\UserTask;

class NominateTask
{
    private $levelListener;
    private $directlyParent;
    
    public function __construct($levelListener)
    {
        $this->levelListener = $levelListener;
    }
    
    public function handle()
    {
        if (!$this->levelListener->levelId) {
            return;
        }

        $this->directlyParent = ShopMember::select(['member_id', 'level_id', 'parent_id', 'validity'])
            ->with(['shopMemberLevel'])
            ->whereHas('shopMemberLevel')
            ->where('member_id', $this->levelListener->memberModel->parent_id)
            ->first();

        $this->nominateTask();
    }

    // 推荐任务
    private function nominateTask()
    {
        // 直属上级
        if (!$this->directlyParent) {
            return;
        }
        $directlyParent = $this->directlyParent;
        // 等级推荐奖励设置
        $nominateLevel = $directlyParent->shopMemberLevel->nominateLevel;
        if (!$nominateLevel) {
            return;
        }
        // 直推下线
        $childCount = $this->getChildCount();
        if ($childCount <= 0) {
            return;
        }
        // 推荐任务
        $task = collect($nominateLevel->task)
            ->where('level_id', $this->levelListener->levelId)
            ->where('member_num', $childCount)
            ->where('amount', '>', 0);
        if ($task->isEmpty()) {
            return;
        }

        foreach ($task as $item) {
            $funcName = 'awardValidity';
            if ($item['code'] == 2) {
                $funcName = 'awardBalance';
            }
            // 奖励
            $this->$funcName($item['amount']);
            // 添加 user_task
            UserTask::create([
                'uid' => $directlyParent->member_id,
                'level_id' => $directlyParent->level_id,
                'task_level_id' => $this->levelListener->levelId,
                'type' => $item['code'],
                'num' => $item['amount'],
                'status' => 1,
            ]);
        }
    }

    private function awardValidity($days)
    {
        $this->directlyParent->validity = $this->directlyParent->validity + $days;
        $this->directlyParent->downgrade_at = 0;
        $this->directlyParent->save();
    }

    private function awardBalance($amount)
    {
        // 建议直接奖励到余额
        (new BalanceChange())->award([
            'member_id'     => $this->directlyParent->member_id,
            'remark'        => "推荐任务奖励",
            'relation'      => '',
            'operator'      => 0,
            'operator_id'   => 0,
            'change_value'  => $amount
        ]);
    }

    private function getChildCount()
    {
        return MemberChild::select(['child_id', 'member_id'])
            ->whereHas('shopMember', function ($shopMember) {
                // 有会员等级
                $shopMember->whereHas('shopMemberLevel', function ($shopMemberLevel) {
                    // 条件限制
                    $shopMemberLevel->where('id', $this->levelListener->levelId);
                });
            })
            ->where('member_id', $this->directlyParent->member_id)
            ->where('level', 1)
            ->count();
    }
}