<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/15 下午2:07
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Jobs;


use app\common\facades\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yunshop\Love\Common\Services\LoveActivationService;

class LoveActivation implements ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;

    private $uniacid;

    public function __construct($uniacid)
    {
        $this->uniacid = $uniacid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->uniacid;

        (new LoveActivationService())->handleActivationQueue();
    }


}