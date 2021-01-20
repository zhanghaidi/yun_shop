<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/22
 * Time: 6:50 PM
 */

namespace Yunshop\Nominate\frontend;


use app\common\components\ApiController;
use Yunshop\Nominate\models\NominateBonus;
use Yunshop\Nominate\models\NominateLevel;
use Yunshop\Nominate\models\ShopMember;
use Yunshop\Nominate\models\TeamPrize;

class HomeController extends ApiController
{
    public function test()
    {
        $memberInfo = $this->getMemberInfo();
        dd($memberInfo);
        exit;
    }

    // plugin.nominate.frontend.home.index
    public function index()
    {
        $set = \Setting::get('plugin.nominate');
        $memberInfo = $this->getMemberInfo();
        $levelInfo = $this->getLevelInfo();
        $statistics = $this->getStatistics();
        $data = [
            'plugin_name' => $set['plugin_name'] ?: '推荐奖励',
            'member' => [
                'name' => $memberInfo['nickname'],
                'id' => $memberInfo['uid'],
                'level' => $memberInfo['level_name'],
                'avatar' => $memberInfo['avatar']
            ],
            // 直推奖
            'nominate_prize' => $levelInfo['nominate_prize'],
            'nominate_prize_name' => $set['nominate_prize_name']?:'直推奖',
            // 团队奖
            'team_prize' => $levelInfo['team_prize'],
            'team_prize_name' => $set['team_prize_name']?:'团队奖',
            // 团队业绩奖比例
            'team_manage_prize' => $levelInfo['team_manage_prize'],
            'team_manage_prize_name' => $set['team_manage_prize_name']?:'团队业绩奖',
            // 累计总金额
            'all_amount' => $statistics['amountTotal'],
            'rewards' => [
                [
                    'reward_name' => $set['nominate_prize_name']?:'直推奖',
                    'reward_amount' => $statistics['nominatePrizeAmount'],
                    'icon' => 'mryt_a',
                    'identifying' => 'DirectReward'
                ],
                [
                    'reward_name' => $set['nominate_poor_prize_name']?:'直推极差奖',
                    'reward_amount' => $statistics['nominatePoorPrizeAmount'],
                    'icon' => 'mryt_b',
                    'identifying' => 'PoorPrize'
                ],
                [
                    'reward_name' => $set['team_prize_name']?:'团队奖',
                    'reward_amount' => $statistics['teamPrizeAmount'],
                    'icon' => 'mryt_c',
                    'identifying' => 'TeamAward'
                ],
                [
                    'reward_name' => $set['team_manage_prize_name']?:'团队业绩奖',
                    'reward_amount' => $statistics['teamManagePrizeAmount'],
                    'icon' => 'mryt_d',
                    'identifying' => 'TeamPerformance'
                ]
            ]
        ];
        return $this->successJson('成功', $data);
    }

    private function getMemberInfo()
    {
        $uid = \YunShop::app()->getMemberId();
        $member = ShopMember::select(['member_id', 'level_id'])
            ->with([
                'shopMemberLevel' => function ($memberLevel) {
                    $memberLevel->select(['id', 'level_name']);
                },
                'hasOneMember' => function ($member) {
                    $member->select(['uid', 'avatar', 'nickname']);
                }
            ])->where('member_id', $uid)
            ->first();
        return [
            'level_name' => $member->shopMemberLevel->level_name,
            'avatar' => $member->hasOneMember->avatar,
            'nickname' => $member->hasOneMember->nickname,
            'uid' => $uid
        ];
    }

    private function getLevelInfo()
    {
        $uid = \YunShop::app()->getMemberId();
        $member = ShopMember::select(['member_id', 'level_id'])
            ->where('member_id', $uid)
            ->first();
        $levelId = $member->level_id;
        $nominateLevel = NominateLevel::select()
            ->where('level_id', $levelId)
            ->first();
        return [
            // 直推奖
            'nominate_prize' => $nominateLevel->nominate_prize,
            // 团队奖
            'team_prize' => $nominateLevel->team_prize,
            // 团队业绩奖比例
            'team_manage_prize' => $nominateLevel->team_manage_prize
        ];
    }

    private function getStatistics()
    {
        $uid = \YunShop::app()->getMemberId();
        // 直推奖累计金额
        $nominatePrizeAmount = NominateBonus::select()
            ->where('uid', $uid)
            ->where('type', NominateBonus::NOMINATE_PRIZE)
            ->sum('amount');
        // 直推极差奖累计金额
        $nominatePoorPrizeAmount = NominateBonus::select()
            ->where('uid', $uid)
            ->where('type', NominateBonus::NOMINATE_POOR_PRIZE)
            ->sum('amount');
        // 团队奖累计金额
        $teamPrizeAmount = NominateBonus::select()
            ->where('uid', $uid)
            ->where('type', NominateBonus::TEAM_PRIZE)
            ->sum('amount');
        // 团队业绩奖累计金额
        $teamManagePrizeAmount = TeamPrize::select()
            ->where(['uid'=>$uid,'status'=>TeamPrize::SUCCESS])
            ->sum('amount');
        // 累计奖励金额
        $amountTotal = $nominatePrizeAmount + $nominatePoorPrizeAmount + $teamPrizeAmount + $teamManagePrizeAmount;

        return [
            'amountTotal' => $amountTotal,
            'nominatePrizeAmount' => $nominatePrizeAmount,
            'nominatePoorPrizeAmount' => $nominatePoorPrizeAmount,
            'teamPrizeAmount' => $teamPrizeAmount,
            'teamManagePrizeAmount' => $teamManagePrizeAmount,
        ];
    }
}