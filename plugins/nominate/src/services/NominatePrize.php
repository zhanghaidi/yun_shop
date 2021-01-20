<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/17
 * Time: 2:09 PM
 */

namespace Yunshop\Nominate\services;


use Yunshop\Nominate\models\MemberParent;
use Yunshop\Nominate\models\NominateBonus;
use Yunshop\Nominate\models\ShopMember;

class NominatePrize
{
    private $levelListener;
    // 已获得直推极差奖的奖励金额
    private $nominatePoorPrizeAmount = 0;
    // 已获得直推极差奖的会员ID
    private $nominatePoorPrizeUid = 0;

    public function __construct($levelListener)
    {
        $this->levelListener = $levelListener;
    }

    public function handle()
    {
        \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]监听开始");
        $parent = $this->getParent($this->levelListener->memberModel->member_id);
        if (!$parent) {
            \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]没有上级");

            return;
        }
        $parentModel = ShopMember::select(['member_id', 'level_id', 'parent_id'])
            ->with(['shopMemberLevel'])
            ->whereHas('shopMemberLevel')
            ->where('member_id', $parent->parent_id)
            ->first();
        if (!$parentModel) {
            \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]pid:[".$parent->parent_id."]上级没有会员等级");

            return;
        }
        // 等级推荐奖励设置
        $nominateLevel = $parentModel->shopMemberLevel->nominateLevel;
        /*if (!$nominateLevel) {
            \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]pid:[".$parent->parent_id."]上级没有等级推荐奖励设置");

            return;
        }*/

        // 直推奖
        $this->nominatePrize($parentModel, $nominateLevel);

        // vip 获得直推奖 第一个合伙人获得直推极差奖  第二个合伙人获得团队奖
        // else
        // 不是vip 获得直推奖 第一个合伙人获得团队奖
        //dump($parentModel->parent_id);
        $greaterParentModel = $this->getParentGreaterLevelWeight($parentModel->parent_id);
        if (!$greaterParentModel) {
            return;
        }
        if ($parentModel->level_id == $this->levelListener->levelId) {
            // 直推级差奖
            $this->nominatePoorPrize($greaterParentModel, $greaterParentModel->shopMemberLevel->nominateLevel);

        } else {
            // 团队奖
            $this->teamPrize($greaterParentModel, $greaterParentModel->shopMemberLevel->nominateLevel);
        }
    }

    private function getParent($memberId)
    {
        return MemberParent::select()
            ->where('member_id', $memberId)
            ->where('level', '1')
            ->first();
    }

    private function getParentGreaterLevelWeight($memberId)
    {
        $parentModel = ShopMember::select(['member_id', 'level_id', 'parent_id'])
            ->with(['shopMemberLevel'])
            ->whereHas('shopMemberLevel')
            ->where('member_id', $memberId)
            ->first();
        if (!$parentModel) {
            \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]pid:[".$memberId."]上级没有会员等级");

            return false;
        }
        if ($parentModel->shopMemberLevel->level <= $this->levelListener->levelWeight) {
            return $this->getParentGreaterLevelWeight($parentModel->parent_id);
            //return false;
        } else {
            return $parentModel;
        }
    }

    private function getParentGreaterLevelWeightToTeamPrize($memberId)
    {
        $parentModel = ShopMember::select(['member_id', 'level_id', 'parent_id'])
            ->with(['shopMemberLevel'])
            ->whereHas('shopMemberLevel')
            ->where('member_id', $memberId)
            ->first();
        if (!$parentModel) {
            \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]pid:[".$memberId."]上级没有会员等级");

            return false;
        }
        if ($parentModel->shopMemberLevel->level <= $this->levelListener->levelWeight) {
            //return $this->getParentGreaterLevelWeight($parentModel->parent_id);
            return false;
        } else {
            return $parentModel;
        }
    }

    /**
     * @name 直推奖
     * @author
     * @param $parentModel
     * @param $nominateLevel
     */
    private function nominatePrize($parentModel, $nominateLevel)
    {
        dump('uid:'.$parentModel->member_id.'直推奖');

        \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]产生直推奖");
        // 等级设置的直推奖
        $amount = $nominateLevel->nominate_prize * $this->levelListener->number;
        if (!$amount) {
            \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]产生直推奖-没有奖励金额[np:".$nominateLevel->nominate_prize."nb:".$this->levelListener->number."]");
            return;
        }

        \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]产生直推奖-pid:[".$parentModel->member_id."]plid:[".$parentModel->level_id."]amount:[".$amount."]");

        // 产生直推奖
        $nominateBonusModel = NominateBonus::store([
            'uniacid'   => $this->levelListener->memberModel->uniacid,
            'uid'       => $parentModel->member_id,
            'level_id'  => $parentModel->level_id,
            'source_id' => $this->levelListener->memberModel->member_id,
            'amount'    => $amount,
            'status'    => NominateBonus::STATUS_SUCCESS,
            'type'      => NominateBonus::NOMINATE_PRIZE
        ]);
        // 已获得直推奖的奖励金额 赋值
        $this->nominatePoorPrizeAmount = $nominateBonusModel->amount;

        $this->nominatePoorPrizeUid = $parentModel->member_id;
    }

    /**
     * @name 直推级差奖
     * @author
     * @param $parentModel
     * @param $nominateLevel
     */
    private function nominatePoorPrize($parentModel, $nominateLevel)
    {
        dump('uid:'.$parentModel->member_id.'直推极差奖');
        \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]产生直推级差奖");
        // 奖励金额
        $amount = $nominateLevel->nominate_prize * $this->levelListener->number - $this->nominatePoorPrizeAmount;
        if ($amount <= 0) {

            \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]产生直推级差奖-没有奖励金额[np:".$nominateLevel->nominate_prize."nb:".$this->levelListener->number."nppa:".$this->nominatePoorPrizeAmount."]");

            return;
        }

        \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]产生直推级差奖-pid:[".$parentModel->member_id."]plid:[".$parentModel->level_id."]amount:[".$amount."]");

        // 产生直推极差奖
        NominateBonus::store([
            'uniacid'   => $this->levelListener->memberModel->uniacid,
            'uid'       => $parentModel->member_id,
            'level_id'  => $parentModel->level_id,
            'source_id' => $this->nominatePoorPrizeUid,
            'amount'    => $amount,
            'status'    => NominateBonus::STATUS_SUCCESS,
            'type'      => NominateBonus::NOMINATE_POOR_PRIZE
        ]);
        // 清零,只产生一次
        $this->nominatePoorPrizeAmount = 0;

        // 团队奖
        $greaterParentModel = $this->getParentGreaterLevelWeightToTeamPrize($parentModel->parent_id);
        if (!$greaterParentModel) {
            return;
        }
        $this->teamPrize($greaterParentModel, $greaterParentModel->shopMemberLevel->nominateLevel);
    }

    /**
     * @name 团队奖
     * @author
     * @param $parentModel
     * @param $nominateLevel
     */
    private function teamPrize($parentModel, $nominateLevel)
    {
        dump('uid:'.$parentModel->member_id.'团队奖');
        \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]产生团队奖");

        // 等级权重要大于最小的等级权重
        if ($parentModel->shopMemberLevel->level <= $this->levelListener->levelWeight) {

            \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]产生团队奖-等级权重要大于最小的等级权重[pid:".$parentModel->member_id."plid:".$parentModel->level_id."]");

            return;
        }

        // 奖励金额
        $amount = $nominateLevel->team_prize  * $this->levelListener->number;
        if ($amount <= 0) {

            \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]产生团队奖-没有奖励金额[tp:".$nominateLevel->team_prize."nb:".$this->levelListener->number."]");

            return;
        }

        \Log::debug("uid:[".$this->levelListener->memberModel->member_id."]产生团队奖-pid:[".$parentModel->member_id."]plid:[".$parentModel->level_id."]amount:[".$amount."]");

        // 产生直推极差奖
        NominateBonus::store([
            'uniacid'   => $this->levelListener->memberModel->uniacid,
            'uid'       => $parentModel->member_id,
            'level_id'  => $parentModel->level_id,
            'source_id' => $this->levelListener->memberModel->member_id,
            'amount'    => $amount,
            'status'    => NominateBonus::STATUS_SUCCESS,
            'type'      => NominateBonus::TEAM_PRIZE
        ]);
    }
}