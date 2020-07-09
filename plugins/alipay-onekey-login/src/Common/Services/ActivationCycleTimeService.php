<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-20
 * Time: 14:51
 */

namespace Yunshop\Love\Common\Services;


use Carbon\Carbon;

class ActivationCycleTimeService
{
    public function getCycleTime()
    {
        return $this->cycleTime();
    }

    private function cycleTime()
    {
        $activationTime = SetService::getActivationTime();

        switch ($activationTime) {
            case 1:
                return $this->yesterdayTimeSolt();
                break;
            case 2:
                return $this->lastWeekTimeSolt();
                break;
            case 3:
                return $this->LastMonthTimeSolt();
                break;
            default:
                return [0, 0];
        }
    }

    private function yesterdayTimeSolt()
    {
        $startTime = Carbon::yesterday()->startOfDay()->timestamp;
        $endTime = Carbon::yesterday()->endOfDay()->timestamp;

        return [$startTime, $endTime];
    }

    private function lastWeekTimeSolt()
    {
        $startTime = Carbon::now()->subWeek(1)->startOfWeek()->timestamp;
        $endTime = Carbon::now()->subWeek(1)->endOfWeek()->timestamp;

        return [$startTime, $endTime];
    }

    private function LastMonthTimeSolt()
    {
        $startTime = (new Carbon('first day of last month'))->startOfDay()->timestamp;
        $endTime = (new Carbon('last day of last month'))->endOfDay()->timestamp;

        return [$startTime, $endTime];
    }

}
