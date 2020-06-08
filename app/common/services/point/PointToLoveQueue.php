<?php
/****************************************************************
 * Author:  king -- LiBaoJia
 * Date:    2020/4/14 6:11 PM
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * IDE:     PhpStorm
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/


namespace app\common\services\point;


use app\common\facades\Setting;
use app\common\models\UniAccount;
use app\framework\Support\Facades\Log;
use app\Jobs\PointToLoveJob;

class PointToLoveQueue
{
    private $pointSet;

    private $transferLoveSet;


    public function handle()
    {
        $uniAccount = UniAccount::getEnable() ?: [];

        foreach ($uniAccount as $u) {
            Setting::$uniqueAccountId = \YunShop::app()->uniacid = $u->uniacid;

            $this->pointSet = $this->pointSet();
            $this->transferLoveSet = $this->transferLoveSet();
            if ($this->isRun()) {
                (new PointToLoveJob($u->uniacid))->handle();

                //更新最后以及转入时间记录
                Setting::set('point.transfer_love', [
                    'last_month' => date('m'),
                    'last_week'  => date('W'),
                    'last_day'   => date('d')
                ]);
            }
        }
    }

    /**
     * 积分基础设置
     *
     * @return array
     */
    private function pointSet()
    {
        return Setting::get('point.set');
    }

    /**
     * 积分转入爱心值最后转入时间记录
     *
     * @return array
     */
    private function transferLoveSet()
    {
        return Setting::get('point.transfer_love');
    }

    /**
     * @return bool
     */
    private function isRun()
    {
        //爱心值插件是否开启
        if (!$this->lovePluginStatus()) {
            return false;
        }
        //是否开启积分转入爱心值
        if (!$this->transferLoveStatus()) {
            return false;
        }
        //转入周期：每天 / 每周，默认每天
        switch ($this->pointSet['transfer_cycle']) {
            case 1:
                return $this->weekly();
                break;
            default:
                return $this->everyDay();
        }
    }

    /**
     * 是否开启自动转入爱心值
     *
     * @return bool
     */
    private function transferLoveStatus()
    {
        return isset($this->pointSet['transfer_love']) && $this->pointSet['transfer_love'] == 1;
    }

    private function lovePluginStatus()
    {
        return app('plugins')->isEnabled('love');
    }

    /**
     * 每天转入爱心值
     *
     * @return bool
     */
    private function everyDay()
    {
        return $this->transferHour();
    }

    /**
     * 是否满足每周转入爱心值
     *
     * @return bool
     */
    private function weekly()
    {
        $lastWeek = $this->transferLoveSet['last_week'];

        if (isset($lastWeek) && $lastWeek == date('W')) {
            Log::info('========积分转入爱心值UNIACID:' . Setting::$uniqueAccountId . ',本年度第' . date('W') . '周已经激活========');
            return false;
        }

        $setWeek = $this->pointSet['transfer_time_week'];

        if ($setWeek != date('w')) {
            Log::info('========积分转入爱心值UNIACID:' . Setting::$uniqueAccountId . ',转入时间周' . $setWeek . '========');
            return false;
        }
        return $this->transferHour();
    }

    /**
     * 是否满足转入时间
     *
     * @return bool
     */
    private function transferHour()
    {
        $transferHour = $this->pointSet['transfer_time_hour'] - 1;

        if ($transferHour != (int)date('H')) {
            Log::info('========积分转入爱心值UNIACID:' . Setting::$uniqueAccountId . ',转入时间' . $transferHour . '点========');
            return false;
        }
        return $this->isTransferred();
    }

    /**
     * 今日是否已经转入
     *
     * @return bool
     */
    private function isTransferred()
    {
        $lastDay = $this->transferLoveSet['last_day'];

        if ($lastDay && $lastDay == date('d')) {
            Log::info('========积分转入爱心值UNIACID:' . \YunShop::app()->uniacid . ',' . date('d') . '日已经转入========');
            return false;
        }
        return true;
    }

}
