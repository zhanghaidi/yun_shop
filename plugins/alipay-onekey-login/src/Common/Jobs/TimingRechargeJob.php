<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/15 下午2:07
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yunshop\Love\Common\Services\TimedTaskRechargeService;

class TimingRechargeJob implements ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;
    protected $rechargeData;

    public function __construct($rechargeData)
    {
        $this->rechargeData = $rechargeData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new TimedTaskRechargeService())->updateRecharge($this->rechargeData);
        (new TimedTaskRechargeService())->addRecharge($this->rechargeData);
    }


}