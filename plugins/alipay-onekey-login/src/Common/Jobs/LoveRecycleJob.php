<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2018/11/7
 * Time: 3:12 PM
 */

namespace Yunshop\Love\Common\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use app\common\facades\Setting;
use Yunshop\Love\Common\Services\LoveRecycleService;

class LoveRecycleJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tradingModel;

    public function __construct($tradingModel)
    {
        $this->tradingModel = $tradingModel;
    }

    /**
     * 执行公司回购队列
     */
    public function handle()
    {
        Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->tradingModel->uniacid;

        (new LoveRecycleService())->recycle($this->tradingModel);
    }

}
