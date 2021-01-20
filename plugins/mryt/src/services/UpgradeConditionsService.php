<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/28
 * Time: 16:18
 */

namespace Yunshop\Mryt\services;


use app\common\models\member\ChildrenOfMember;
use Yunshop\Mryt\models\MrytMemberModel;
use Yunshop\SalesCommission\models\SalesCommission;

class UpgradeConditionsService
{
    /**
     * 个人直推升级
     * @param $uid
     * @param $levelModel
     * @return bool
     */
    public function directVip($uid, $levelModel)
    {
        if (empty($levelModel['upgraded'][1]['direct_vip'])) {
            return true;
        }

        $member = [];
        $childs = ChildrenOfMember::uniacid()
            ->where('member_id', $uid)
            ->where('level', 1)
            ->select('child_id')
            ->get()
            ->toArray();

        foreach ($childs as $key => $child) {
            $member[] = $child['child_id'];
        }

        $mrytMember = MrytMemberModel::uniacid()
            ->whereIn('uid', $member)
            ->where('level',0)
            ->count();

        if ($mrytMember >= $levelModel['upgraded'][1]['direct_vip']) {
            return true;
        }

        return false;

    }

    /**
     * 团队Vip
     * @param $uid
     * @param $levelModel
     * @return bool
     */
    public static function teamVip($uid, $levelModel)
    {
        if (empty($levelModel['upgraded'][1]['team_vip'])) {
            return true;
        }
        $member = [];
        $childs = ChildrenOfMember::uniacid()
            ->where('member_id', $uid)
            ->select('child_id')
            ->get()
            ->toArray();

        foreach ($childs as $key => $child) {
            $member[] = $child['child_id'];
        }

        $mrytMember = MrytMemberModel::uniacid()
            ->whereIn('uid', $member)
            ->where('level',0)
            ->count();

        if ($mrytMember >= $levelModel['upgraded'][1]['team_vip']) {
            return true;
        }

        return false;

    }

    /**
     * 个人销售金额
     * @param $uid
     * @param $levelModel
     * @return bool
     */
    public static function settleMoney($uid, $levelModel)
    {
        if (empty($levelModel['upgraded'][1]['settle_money'])) {
            return true;
        }

        $dividend_amount = SalesCommission::sumDividendAmountByUid($uid);

        if ($dividend_amount >= $levelModel['upgraded'][1]['settle_money']) {
            return true;
        }
        return false;
    }

    /**
     * 团队销售金额、人数
     * @param $uid
     * @param $levelModel
     * @return bool
     */
    public static function teamCostCount($uid, $levelModel)
    {
        if (empty($levelModel['upgraded'][1]['team_cost_num']) || empty($levelModel['upgraded'][1]['team_cost_count'])) {
            return true;
        }
        $member = [];
        $team_cost_num = 0;
        $childs = ChildrenOfMember::uniacid()
            ->where('member_id', $uid)
            ->select('child_id')
            ->get()
            ->toArray();

        foreach ($childs as $key => $child) {
            $member[] = $child['child_id'];
        }

        $dividend_amount = SalesCommission::uniacid()
            ->whereIn('member_id', $childs)
            ->selectRaw('sum(dividend_amount) as dividend')->groupBy('member_id')->get()->toArray();

        foreach ($dividend_amount as $value) {
            if ($value['dividend'] >= $levelModel['upgraded']['team_cost_count']) {
                $team_cost_num ++;
            }
        }

        if ($team_cost_num >= $levelModel['upgraded']['team_cost_num']) {
            return true;
        }
        return false;
    }

    /**
     * 直推level 人数
     * @param $uid
     * @param $levelModel
     * @return bool
     */
    public static function level($uid, $levelModel)
    {
        if (empty($levelModel['upgraded'][1]['level'])) {
            return true;
        }
        $count = true;

        $childs = ChildrenOfMember::uniacid()
            ->where('member_id', $uid)
            ->where('level', 1)
            ->select('child_id')
            ->get()
            ->toArray();

        foreach ($childs as $child) {
            $member[] = $child['child_id'];
        }

        foreach ($levelModel['upgraded'][1]['level'] as $key => $level) {
            if ($level <= 0) {
                continue;
            }

            $mrytMember = MrytMemberModel::uniacid()
                ->whereIn('uid', $member)
                ->where('level',$key)
                ->count();
            if ($mrytMember < $level) {
                $count = false;
                break;
            }
        }
        return $count;
    }

    public static function team($uid, $levelModel)
    {
        if (empty($levelModel['upgraded'][1]['team'])) {
            return true;
        }

        $count = true;

        $childs = ChildrenOfMember::uniacid()
            ->where('member_id', $uid)
            ->select('child_id')
            ->get()
            ->toArray();

        foreach ($childs as $child) {
            $member[] = $child['child_id'];
        }

        foreach ($levelModel['upgraded'][1]['team'] as $key => $level) {
            if ($level <= 0 || empty($level)) {
                continue;
            }

            $mrytMember = MrytMemberModel::uniacid()
                ->whereIn('uid', $member)
                ->where('level',$key)
                ->count();

            if ($mrytMember < $level) {
                $count = false;break;
            }
        }
        return $count;
    }


}