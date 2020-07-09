<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/17 上午9:35
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;


use app\common\models\UniAccount;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\Love\Common\Services\LoveActivationService;
use Yunshop\Love\Common\Jobs\LoveActivation;

class TimedTaskActivationService
{
    use DispatchesJobs;

    private $set;

    public function handle()
    {
        set_time_limit(0);
        \Log::info('爱心值激活队列开始');
        //$this->dispatch(new LoveActivation());
        $this->handleQueue();
    }

    private function handleQueue()
    {
        $uniAccount = UniAccount::getEnable() ?: [];
        foreach ($uniAccount as $u) {

            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;

            $isActivation = $this->isActivation();
            if ($isActivation) {
                \Log::info('========爱心值激活UNIACID:'.$u->uniacid.'加入队列========');
                $this->dispatch(new LoveActivation($u->uniacid));
                
                \Setting::set('love.last_month_activation',date('m'));
                \Setting::set('love.last_week_activation',date('W'));
                \Setting::set('love.last_time_activation',date('d'));
            } else {
                //\Log::info('========爱心值激活UNIACID:'.$u->uniacid.'未满足激活条件========');
                continue;
            }
        }
    }


    private function isActivation()
    {
        $activation_time = SetService::getActivationTime();
        //dd($activation_time);
        switch ($activation_time) {
            case 1:
                return $this->everyDay();
                break;
            case 2:
                return $this->weekly();
                break;
            case 3:
                return $this->monthly();
                break;
            default:
                \Log::info('========爱心值激活UNIACID:'.\YunShop::app()->uniacid.'未开启激活========');
                return false;

        }
    }


    private function everyDay()
    {
        return $this->activationHour();
    }

    private function weekly()
    {
        $last_week_activation = \Setting::get('love.last_week_activation');
        if ($last_week_activation && $last_week_activation == date('W')) {
            \Log::info('========爱心值激活UNIACID:'.\YunShop::app()->uniacid.',本年度第'.date('W').'周已经激活========');
            return false;
        }

        $activation_time_week = SetService::getActivationWeek();
        if ($activation_time_week != date('w')) {
            \Log::info('========爱心值激活UNIACID:'.\YunShop::app()->uniacid.',激活时间周'.date('w').'========');
            return false;
        }
        return $this->activationHour();
    }

    private function monthly()
    {
        //return 'month';
        $last_month_activation = \Setting::get('love.last_month_activation');
        if ($last_month_activation && $last_month_activation == date('m')) {
            \Log::info('========爱心值激活UNIACID:'.\YunShop::app()->uniacid.',本月'.date('m').'已经激活========');
            return false;
        }
        if (date('d') != 1) {
            \Log::info('========爱心值激活UNIACID:'.\YunShop::app()->uniacid.',激活时间每月'.date('d').'日========');
            return false;
        }
        //每月激活，不能使用 date('d') 判断
        //return $this->activationHour();

        $activation_time_hour = SetService::getActivationHour();
        $activation_time_hour = $activation_time_hour - 1;
        if ($activation_time_hour == (int)date('H')) {
            return true;
        }
        \Log::info('========爱心值激活UNIACID:'.\YunShop::app()->uniacid.',激活时间'.$activation_time_hour.'点========');
        return false;
    }

    private function activationHour()
    {
        $activation_time_hour = SetService::getActivationHour();
        $activation_time_hour = $activation_time_hour - 1;
        if ($activation_time_hour == (int)date('H')) {
            return $this->isActivated();
        }
        \Log::info('========爱心值激活UNIACID:'.\YunShop::app()->uniacid.',激活时间'.$activation_time_hour.'点========');
        return false;
    }

    private function isActivated()
    {
        $last_time_activation = \Setting::get('love.last_time_activation');
        if ($last_time_activation && $last_time_activation == date('d')) {
            \Log::info('========爱心值激活UNIACID:'.\YunShop::app()->uniacid.','.date('d').'日已经激活========');
            return false;
        }
        return true;
    }

}
