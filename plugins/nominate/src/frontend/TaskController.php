<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/23
 * Time: 6:46 PM
 */

namespace Yunshop\Nominate\frontend;


use app\common\components\ApiController;
use Yunshop\Nominate\models\MemberChild;
use Yunshop\Nominate\models\NominateLevel;
use Yunshop\Nominate\models\ShopMember;
use Yunshop\Nominate\models\ShopMemberLevel;
use Yunshop\Nominate\models\UserTask;

class TaskController extends ApiController
{
    public function test()
    {
        $taskLevelId = 14;
        $ret = MemberChild::select()
            ->whereHas('shopMember', function ($shopMember) use ($taskLevelId) {
                // 有会员等级
                $shopMember->whereHas('shopMemberLevel', function ($shopMemberLevel) use ($taskLevelId) {
                    // 条件限制
                    $shopMemberLevel->where('id', $taskLevelId);
                });
            })
            ->where('member_id', \YunShop::app()->getMemberId())
            ->whereLevel(1)
            ->get();
        dd($ret);
        exit;
    }

    public function enable()
    {
        $set = \Setting::get('plugin.nominate');
        return $this->successJson('成功', [
            'is_open' => $set['is_open_task']
        ]);
    }

    // plugin.nominate.frontend.task.index
    public function index()
    {
        $uid = \YunShop::app()->getMemberId();
        $member = ShopMember::select(['member_id', 'level_id', 'validity'])
            ->with([
                'shopMemberLevel' => function ($shopMemberLevel) {
                    $shopMemberLevel->select('id', 'level_name');
                },
                'hasOneMember' => function ($member) {
                    $member->select('uid', 'avatar', 'nickname');
                }
            ])
            ->where('member_id', $uid)
            ->first();
        $levelId = $member->level_id;
        $nominateLevel = NominateLevel::select()
            ->where('level_id', $levelId)
            ->first();
        $info = [
            'level_name' => $member->shopMemberLevel->level_name,
            'nickname' => $member->hasOneMember->nickname,
            'avatar' => $member->hasOneMember->avatar,
            'validity' => $member->validity,
            'task' => []
        ];
        foreach ($nominateLevel->task as $item) {

            $taskLevel = $this->getTaskLevel($item['level_id']);
            $data = [
                'level_name' => $taskLevel->level_name,
                'member_num' => $item['member_num'],
                'amount' => $item['amount'],
            ];
            $codeName = '余额';
            $unit = '元';
            if ($item['code'] == 1) {
                $codeName = '有效期';
                $unit = '天';
            }
            $data['code_name'] = $codeName;
            $data['unit'] = $unit;
            $data['ret'] = $this->getChildCount($item['level_id'], $uid);
            $data['status'] = 0;
            $ret = $this->getTaskRet($uid, $levelId, $item);
            if ($ret) {
                $data['status'] = 1;
            }
            $info['task'][] = $data;
        }

        return $this->successJson('成功', $info);
    }

    private function getTaskLevel($taskLevelId)
    {
        return ShopMemberLevel::select(['level_name'])
            ->where('id', $taskLevelId)
            ->first();
    }

    private function getTaskRet($uid, $levelId, $task)
    {
        return UserTask::select()
            ->where('uid', $uid)
            ->where('level_id', $levelId)
            ->where('task_level_id', $task['level_id'])
            ->where('type', $task['code'])
            ->where('num', $task['amount'])
            ->first();
    }

    private function getChildCount($taskLevelId, $uid)
    {
        return MemberChild::select(['child_id', 'member_id'])
            ->whereHas('shopMember', function ($shopMember) use ($taskLevelId) {
                // 有会员等级
                $shopMember->whereHas('shopMemberLevel', function ($shopMemberLevel) use ($taskLevelId) {
                    // 条件限制
                    $shopMemberLevel->where('id', $taskLevelId);
                });
            })
            ->whereLevel(1)
            ->where('member_id', $uid)
            ->count();
    }
}