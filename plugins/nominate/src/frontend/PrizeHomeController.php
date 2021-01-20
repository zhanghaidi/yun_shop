<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/23
 * Time: 2:57 PM
 */

namespace Yunshop\Nominate\frontend;


use Yunshop\Nominate\models\NominateBonus;
use Yunshop\Nominate\models\TeamPrize;

class PrizeHomeController extends CommonController
{
    /**
     * @name 直推奖统计
     * @url plugin.nominate.frontend.prize-home.nominate-prize-home
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function nominatePrizeHome()
    {
        $uid = \YunShop::app()->getMemberId();
        $this->setTime();
        // 今天
        $today = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::NOMINATE_PRIZE)
            ->where('created_at', '>', $this->today_time_start)
            ->sum('amount');
        // 昨天
        $yesterday = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::NOMINATE_PRIZE)
            ->whereBetween('created_at', [$this->yesterday_time_start, $this->today_time_start])
            ->sum('amount');
        // 本周
        $this_week = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::NOMINATE_PRIZE)
            ->where('created_at', '>', $this->this_week_time_start)
            ->sum('amount');
        // 本月
        $this_month = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::NOMINATE_PRIZE)
            ->where('created_at', '>', $this->this_month_time_start)
            ->sum('amount');

        return $this->successJson('成功', [
            'today' => $today,
            'yesterday' => $yesterday,
            'this_week' => $this_week,
            'this_month' => $this_month,
        ]);
    }

    /**
     * @name 直推奖列表
     * @url plugin.nominate.frontend.prize-home.nominate-prize-list
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function nominatePrizeList()
    {
        $uid = \YunShop::app()->getMemberId();
        // -1->全部,0->未发放,1->已发放
        $status = intval(request()->status);
        if (!in_array($status, [0, 1, -1])) {
            return $this->errorJson();
        }
        $list = NominateBonus::select()
            ->with([
                'sourceMember' => function ($member) {
                    $member->select(['uid', 'avatar', 'nickname']);
                }
            ])
            ->where('uid', $uid)
            ->byType(NominateBonus::NOMINATE_PRIZE)
            ->byStatusToApiList($status)
            ->orderBy('id', 'desc')
            ->paginate();

        return $this->successJson('成功', [
            'list' => $list
        ]);
    }

    /**
     * @name 直推极差奖统计
     * @url plugin.nominate.frontend.prize-home.nominate-poor-prize-home
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function nominatePoorPrizeHome()
    {
        $uid = \YunShop::app()->getMemberId();
        $this->setTime();
        // 今天
        $today = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::NOMINATE_POOR_PRIZE)
            ->where('created_at', '>', $this->today_time_start)
            ->sum('amount');
        // 昨天
        $yesterday = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::NOMINATE_POOR_PRIZE)
            ->whereBetween('created_at', [$this->yesterday_time_start, $this->today_time_start])
            ->sum('amount');
        // 本周
        $this_week = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::NOMINATE_POOR_PRIZE)
            ->where('created_at', '>', $this->this_week_time_start)
            ->sum('amount');
        // 本月
        $this_month = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::NOMINATE_POOR_PRIZE)
            ->where('created_at', '>', $this->this_month_time_start)
            ->sum('amount');

        return $this->successJson('成功', [
            'today' => $today,
            'yesterday' => $yesterday,
            'this_week' => $this_week,
            'this_month' => $this_month,
        ]);
    }

    /**
     * @name 直推极差奖列表
     * @url plugin.nominate.frontend.prize-home.nominate-prize-list
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function nominatePoorPrizeList()
    {
        $uid = \YunShop::app()->getMemberId();
        // -1->全部,0->未发放,1->已发放
        $status = intval(request()->status);
        if (!in_array($status, [0, 1, -1])) {
            return $this->errorJson();
        }
        $list = NominateBonus::select()
            ->with([
                'sourceMember' => function ($member) {
                    $member->select(['uid', 'avatar', 'nickname']);
                }
            ])
            ->where('uid', $uid)
            ->byType(NominateBonus::NOMINATE_POOR_PRIZE)
            ->byStatusToApiList($status)
            ->orderBy('id', 'desc')
            ->paginate();

        return $this->successJson('成功', [
            'list' => $list
        ]);
    }

    /**
     * @name 团队奖统计
     * @url plugin.nominate.frontend.prize-home.team-prize-home
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamPrizeHome()
    {
        $uid = \YunShop::app()->getMemberId();
        $this->setTime();
        // 今天
        $today = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::TEAM_PRIZE)
            ->where('created_at', '>', $this->today_time_start)
            ->sum('amount');
        // 昨天
        $yesterday = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::TEAM_PRIZE)
            ->whereBetween('created_at', [$this->yesterday_time_start, $this->today_time_start])
            ->sum('amount');
        // 本周
        $this_week = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::TEAM_PRIZE)
            ->where('created_at', '>', $this->this_week_time_start)
            ->sum('amount');
        // 本月
        $this_month = NominateBonus::select()
            ->where('uid', $uid)
            ->byType(NominateBonus::TEAM_PRIZE)
            ->where('created_at', '>', $this->this_month_time_start)
            ->sum('amount');

        return $this->successJson('成功', [
            'today' => $today,
            'yesterday' => $yesterday,
            'this_week' => $this_week,
            'this_month' => $this_month,
        ]);
    }

    /**
     * @name 团队奖列表
     * @url plugin.nominate.frontend.prize-home.team-prize-list
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamPrizeList()
    {
        $uid = \YunShop::app()->getMemberId();
        // -1->全部,0->未发放,1->已发放
        $status = intval(request()->status);
        if (!in_array($status, [0, 1, -1])) {
            return $this->errorJson();
        }
        $list = NominateBonus::select()
            ->with([
                'sourceMember' => function ($member) {
                    $member->select(['uid', 'avatar', 'nickname']);
                }
            ])
            ->where('uid', $uid)
            ->byType(NominateBonus::TEAM_PRIZE)
            ->byStatusToApiList($status)
            ->orderBy('id', 'desc')
            ->paginate();

        return $this->successJson('成功', [
            'list' => $list
        ]);
    }

    /**
     * @name 团队业绩奖统计
     * @url plugin.nominate.frontend.prize-home.team-manage-prize-home
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamManagePrizeHome()
    {
        $uid = \YunShop::app()->getMemberId();
        $this->setTime();
        // 今天
        $today = TeamPrize::select()
            ->where(['uid'=>$uid,'status'=>TeamPrize::SUCCESS])
            ->where('created_at', '>', $this->today_time_start)
            ->sum('amount');
        // 昨天
        $yesterday = TeamPrize::select()
            ->where(['uid'=>$uid,'status'=>TeamPrize::SUCCESS])
            ->whereBetween('created_at', [$this->yesterday_time_start, $this->today_time_start])
            ->sum('amount');
        // 本周
        $this_week = TeamPrize::select()
            ->where(['uid'=>$uid,'status'=>TeamPrize::SUCCESS])
            ->where('created_at', '>', $this->this_week_time_start)
            ->sum('amount');
        // 本月
        $this_month = TeamPrize::select()
            ->where(['uid'=>$uid,'status'=>TeamPrize::SUCCESS])
            ->where('created_at', '>', $this->this_month_time_start)
            ->sum('amount');

        return $this->successJson('成功', [
            'today' => $today,
            'yesterday' => $yesterday,
            'this_week' => $this_week,
            'this_month' => $this_month,
        ]);
    }

    /**
     * @name 团队业绩奖列表
     * @url plugin.nominate.frontend.prize-home.team-manage-prize-list
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamManagePrizeList()
    {
        $uid = \YunShop::app()->getMemberId();
        // -1->全部,0->未发放,1->已发放,2->已失效
        $status = intval(request()->status);
        if (!in_array($status, [0, 1, -1, 2])) {
            return $this->errorJson();
        }
        $list = TeamPrize::select()
            ->with([
                'member' => function ($member) {
                    $member->select(['uid', 'avatar', 'nickname']);
                }
            ])
            ->where('uid', $uid)
            ->byStatusToApiList($status)
            ->orderBy('id', 'desc')
            ->paginate();

        return $this->successJson('成功', [
            'list' => $list
        ]);
    }
}