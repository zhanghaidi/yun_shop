<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/24
 * Time: 上午11:15
 */

namespace Yunshop\Mryt\api;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use Carbon\Carbon;
use Yunshop\Mryt\common\models\OrderParentingAward;
use Yunshop\Mryt\services\CommonService;

class TrainController extends ApiController
{
    // plugin.mryt.api.train.index
    public function index()
    {
        $today_time_start = strtotime(Carbon::today());
        $yesterday_time_start = strtotime(Carbon::yesterday());
        $this_week_time_start = strtotime(date('Y-m-d', strtotime("this week Monday", time())));
        $this_month_time_start = mktime(0,0,0,date('m'),1,date('Y'));
        $set = CommonService::getSet();

        $today = OrderParentingAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('created_at', '>', $today_time_start)
            ->sum('amount');
        $yesterday = OrderParentingAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->whereBetween('created_at', [$yesterday_time_start, $today_time_start])
            ->sum('amount');
        $this_week = OrderParentingAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('created_at', '>', $this_week_time_start)
            ->sum('amount');
        $this_month = OrderParentingAward::select()
            ->where('uid', \YunShop::app()->getMemberId())
            ->where('created_at', '>', $this_month_time_start)
            ->sum('amount');
        return $this->successJson('成功', [
            'this_month' => $this_month,
            'this_week' => $this_week,
            'today' => $today,
            'yesterday' => $yesterday,
            'parenting_name' => $set['parenting_name'],
        ]);
    }

    // plugin.mryt.api.train.getList
    public function getList()
    {
        $list = OrderParentingAward::getListByApi(request()->status)
            ->where('uid', \YunShop::app()->getMemberId())
            ->orderBy('id', 'desc')
            ->paginate();
        return $this->successJson('成功', $list);
    }

    // plugin.mryt.api.train.details
    public function details()
    {
        $award_id = intval(request()->award_id);
        if (!$award_id) {
            throw new AppException('[award_id]参数错误');
        }
        $award = OrderParentingAward::build()
            ->where('id', $award_id)
            ->where('uid', \YunShop::app()->getMemberId())
            ->first();
        if ($award) {
            $award->type_name = '育人奖';
        }
        return $this->successJson('成功', $award);
    }
}