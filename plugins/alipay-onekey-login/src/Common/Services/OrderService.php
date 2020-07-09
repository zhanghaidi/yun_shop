<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/11 下午1:59
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yunshop\Love\Common\Models\Member;
use Yunshop\Love\Common\Models\Order;
use Yunshop\Love\Common\Modules\LoveActivationRecord\LoveActivationRecordRepository;
use Yunshop\Love\Common\Modules\Repository;

class OrderService
{
    /**
     *
     * @return float
     */
    public function getMemberYesterdayCompleteOrderMoney()
    {
        $startTime = Carbon::yesterday()->startOfDay()->timestamp;
        $endTime = Carbon::yesterday()->endOfDay()->timestamp;

        return $this->getMemberTimeSlotCompleteOrderAmountData($startTime, $endTime);
    }

    /**
     *
     * @return float
     */
    public function getMemberLastWeekCompleteOrderMoney()
    {
        $startTime = Carbon::now()->subWeek(1)->startOfWeek()->timestamp;
        $endTime = Carbon::now()->subWeek(1)->endOfWeek()->timestamp;

        return $this->getMemberTimeSlotCompleteOrderAmountData($startTime, $endTime);
    }

    /**
     *
     * @return float
     */
    public function getMemberLastMonthCompleteOrderMoney()
    {
        $startTime = (new Carbon('first day of last month'))->startOfDay()->timestamp;
        $endTime = (new Carbon('last day of last month'))->endOfDay()->timestamp;

        return $this->getMemberTimeSlotCompleteOrderAmountData($startTime, $endTime);
    }

    /**
     * @param $startTime
     * @param $endTime
     * @return mixed
     */
    public function getMemberTimeSlotCompleteOrderAmountData($startTime, $endTime)
    {

        return Order::getQuery()->select(DB::raw('sum(`price`) as amount,uid'))->where('status', Order::COMPLETE)
            ->whereBetween('finish_time',[$startTime,$endTime])
            ->groupBy('uid')
            ->where('uniacid', \YunShop::app()->uniacid)
            ->get();
    }

    /**
     * @return float|int
     */
    public function getActivationCompleteOrderMoneyData()
    {
        $activationTime = SetService::getActivationTime();
        switch ($activationTime) {
            case 1:
                return $this->getMemberYesterdayCompleteOrderMoney();
                break;
            case 2:
                return $this->getMemberLastWeekCompleteOrderMoney();
                break;
            case 3:
                return $this->getMemberLastMonthCompleteOrderMoney();
                break;
            default:
                return 0;
        }
    }
}
