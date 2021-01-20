<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/24
 * Time: 上午11:18
 */

namespace Yunshop\Mryt\api;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use Carbon\Carbon;
use Yunshop\Mryt\common\models\MemberTeamAward;
use Yunshop\Mryt\services\CommonService;

class ThanksgiverController extends ApiController
{
    // plugin.mryt.api.thanksgiver.index
    public function index()
    {
        $today_time_start = strtotime(Carbon::today());
        $yesterday_time_start = strtotime(Carbon::yesterday());
        $this_week_time_start = strtotime(date('Y-m-d', strtotime("this week Monday", time())));
        $this_month_time_start = mktime(0,0,0,date('m'),1,date('Y'));
        $set = CommonService::getSet();

        $today = MemberTeamAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('award_type', 2)
            ->where('created_at', '>', $today_time_start)
            ->sum('amount');
        $yesterday = MemberTeamAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('award_type', 2)
            ->whereBetween('created_at', [$yesterday_time_start, $today_time_start])
            ->sum('amount');
        $this_week = MemberTeamAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('award_type', 2)
            ->where('created_at', '>', $this_week_time_start)
            ->sum('amount');
        $this_month = MemberTeamAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('award_type', 2)
            ->where('created_at', '>', $this_month_time_start)
            ->sum('amount');
        return $this->successJson('成功', [
            'this_month' => $this_month,
            'this_week' => $this_week,
            'today' => $today,
            'yesterday' => $yesterday,
            'thanksgiving_name' => $set['thanksgiving_name'],
        ]);
    }

    // plugin.mryt.api.thanksgiver.getList
    public function getList()
    {
        $list = MemberTeamAward::getListByApi(request()->status)
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('award_type', 2)
            ->orderBy('id', 'desc')
            ->paginate();
        return $this->successJson('成功', $list);
    }

    // plugin.mryt.api.thanksgiver.details
    public function details()
    {
        $award_id = intval(request()->award_id);
        if (!$award_id) {
            throw new AppException('[award_id]参数错误');
        }
        $award = MemberTeamAward::build()
            ->where('id', $award_id)
            ->where('award_type', 2)
            ->where('uid', \YunShop::app()->getMemberId())
            ->first();
        if ($award) {
            $award->type_name = '感恩奖';
        }
        return $this->successJson('成功', $award);
    }
}