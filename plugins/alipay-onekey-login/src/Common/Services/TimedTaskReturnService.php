<?php
/****************************************************************
 * Author:  LiBaoJia
 * Date:    2017/7/17 上午9:35
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;


use app\common\facades\Setting;
use app\common\models\UniAccount;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;
use Yunshop\Love\Common\Jobs\LoveReturnJob;

class TimedTaskReturnService
{
    use DispatchesJobs;

    /**
     * @var array
     */
    private $set;

    /**
     * @var array
     */
    private $setLog;


    public function handle()
    {
        set_time_limit(0);

        $this->_handle();
    }

    private function _handle()
    {
        foreach (UniAccount::getEnable() as $u) {
            Setting::$uniqueAccountId = \YunShop::app()->uniacid = $u->uniacid;

            if ($this->isReturn()) {
                $this->loveReturn();
            } else {
                Log::info("爱心值返现UNIACID:" . Setting::$uniqueAccountId . "未满足条件");
            }
        }
    }

    private function isReturn()
    {
        $this->set = Setting::get('plugin.love_return');
        $this->setLog = Setting::get('plugin.love_return_log');

        if (!$this->set || !$this->set['is_return']) {
            return false;
        }
        if (!$this->set['return_rate'] || $this->set['return_rate'] <= 0) {
            return false;
        }
        if ($this->set['return_times'] != date('H')) {
            return false;
        }
        if ($this->setLog['current_d'] == date('d')) {
            return false;
        }
        return true;
    }

    private function loveReturn()
    {
        Log::info("爱心值返现UNIACID:" . Setting::$uniqueAccountId . "加入队列");

        dispatch(new LoveReturnJob(Setting::$uniqueAccountId));

        $this->updateReturnTime();
    }

    private function updateReturnTime()
    {
        $this->setLog['current_d'] = date('d');

        Setting::set('plugin.love_return_log', $this->setLog);
    }

}
