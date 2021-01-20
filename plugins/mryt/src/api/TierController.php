<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/11/23
 * Time: 2:32 PM
 */

namespace Yunshop\Mryt\api;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use Carbon\Carbon;
use Yunshop\Mryt\common\models\TierAward;
use Yunshop\Mryt\services\CommonService;

class TierController extends ApiController
{
    public function index()
    {
        $today_time_start = strtotime(Carbon::today());
        $yesterday_time_start = strtotime(Carbon::yesterday());
        $this_week_time_start = strtotime(date('Y-m-d', strtotime("this week Monday", time())));
        $this_month_time_start = mktime(0,0,0,date('m'),1,date('Y'));
        $set = CommonService::getSet();

        $today = TierAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('created_at', '>', $today_time_start)
            ->sum('amount');
        $yesterday = TierAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->whereBetween('created_at', [$yesterday_time_start, $today_time_start])
            ->sum('amount');
        $this_week = TierAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('created_at', '>', $this_week_time_start)
            ->sum('amount');
        $this_month = TierAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('created_at', '>', $this_month_time_start)
            ->sum('amount');
        return $this->successJson('成功', [
            'this_month' => $this_month,
            'this_week' => $this_week,
            'today' => $today,
            'yesterday' => $yesterday,
            'tier_name' => $set['tier_name'],
        ]);
    }

    // plugin.mryt.api.tier.getList
    public function getList()
    {
        $list = TierAward::getListByApi(request()->status)
            ->where('uid', \YunShop::app()->getMemberId())
            ->orderBy('id', 'desc')
            ->paginate();
        return $this->successJson('成功', $list);
    }

    // plugin.mryt.api.tier.details
    public function details()
    {
        $award_id = intval(request()->award_id);
        if (!$award_id) {
            throw new AppException('[award_id]参数错误');
        }
        $award = TierAward::build()
            ->where('id', $award_id)
            ->where('uid', \YunShop::app()->getMemberId())
            ->first();
        if ($award) {
            $award->type_name = '平级奖';
        }
        return $this->successJson('成功', $award);
    }
}