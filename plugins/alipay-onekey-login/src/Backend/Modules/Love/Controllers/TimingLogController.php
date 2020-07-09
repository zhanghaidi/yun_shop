<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/28
 * Time: 上午10:03
 */

namespace Yunshop\Love\Backend\Modules\Love\Controllers;


use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Love\Common\Models\LoveTimingQueueModel;
use Yunshop\Love\Common\Models\TimingLogModel;

class TimingLogController extends BaseController
{

    protected $pageSize = 10;

    public function index()
    {
        $search = \YunShop::request()->get('search');

        $list = TimingLogModel::getTimingLog($search)->orderBy('id', 'desc')->paginate($this->pageSize);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        $total = TimingLogModel::uniacid()->sum('amount');

        $queues = LoveTimingQueueModel::uniacid()->get();
        foreach ($queues as $queue) {

        }

         $recharge = $this->getRechargeNum(1);
         $noRecharge = $this->getRechargeNum(0);

        $loveName = \Yunshop\Love\Common\Services\SetService::getLoveName();
        return view('Yunshop\Love::Backend.Love.timing-log', [
            'list' => $list->toArray(),
            'pager' => $pager,
            'search' => $search,
            'memberLevels' => MemberLevel::getMemberLevelList(),
            'memberGroups' => MemberGroup::getMemberGroupList(),
            'loveName' => $loveName,
            'total' => $total,
            'recharge' => $recharge,
            'noRecharge' => $noRecharge,
        ])->render();
    }

    public function getRechargeNum($status)
    {
        $queues = LoveTimingQueueModel::uniacid()->where('status',$status)->get();
        $amount = 0;
        foreach ($queues as $queue) {
            $amount += $queue->change_value / 100 * $queue->timing_rate;
        }
        return $amount;
    }

    public function detail()
    {
        $id = \YunShop::request()->id;

        $timingLog = TimingLogModel::where('id', $id)->with('hasOneMember')->with('hasManyQueue')->first();

        $loveName = \Yunshop\Love\Common\Services\SetService::getLoveName();

        foreach ($timingLog->hasManyQueue as $key => &$item) {
            $item['period'] = $key + 1;
            $item['amount'] = $item->change_value / 100 * $item->timing_rate;
        }

        return view('Yunshop\Love::Backend.Love.timing-detail', [
            'item' => $timingLog->toArray(),
            'loveName' => $loveName,
        ])->render();

    }

}