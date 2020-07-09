<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/12/5
 * Time: 2:07 PM
 */

namespace Yunshop\Love\Common\Jobs;


use app\common\facades\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yunshop\Love\Common\Services\LoveActivationService;
use Yunshop\Love\Common\Services\LoveReturnService;

class LoveReturnJob  implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $accountId;

    public function __construct($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->accountId;

        (new LoveReturnService())->handle();
    }
}