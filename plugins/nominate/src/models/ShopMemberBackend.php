<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/23
 * Time: 7:52 PM
 */

namespace Yunshop\Nominate\models;


class ShopMemberBackend extends ShopMember
{
    protected $appends = [
        'nominate_prize_amount', 'nominate_poor_prize_amount', 'team_prize_amount', 'team_manage_prize_amount'
    ];

    public function getNominatePrizeAmountAttribute()
    {
        $nominatePrizeAmount = NominateBonus::select()
            ->where('uid', $this->member_id)
            ->where('type', NominateBonus::NOMINATE_PRIZE)
            ->sum('amount');
        return $nominatePrizeAmount;
    }

    public function getNominatePoorPrizeAmountAttribute()
    {
        $nominatePoorPrizeAmount = NominateBonus::select()
            ->where('uid', $this->member_id)
            ->where('type', NominateBonus::NOMINATE_POOR_PRIZE)
            ->sum('amount');
        return $nominatePoorPrizeAmount;
    }

    public function getTeamPrizeAmountAttribute()
    {
        // 团队奖累计金额
        $teamPrizeAmount = NominateBonus::select()
            ->where('uid', $this->member_id)
            ->where('type', NominateBonus::TEAM_PRIZE)
            ->sum('amount');
        return $teamPrizeAmount;
    }

    public function getTeamManagePrizeAmountAttribute()
    {
        // 团队业绩奖累计金额
        $teamManagePrizeAmount = TeamPrize::select()
            ->where('uid', $this->member_id)
            ->sum('amount');
        return $teamManagePrizeAmount;
    }
}