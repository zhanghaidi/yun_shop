<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/28
 * Time: 4:50 PM
 */

namespace Yunshop\Mryt\common\jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Yunshop\Mryt\services\UpgrateAwardService;

class UpgrateAwardJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $uid;
    public $uniacid;
    public $orderId;

    public function __construct($uid, $uniacid, $orderId)
    {
        $this->uid = $uid;
        $this->uniacid = $uniacid;
        $this->orderId = $orderId;
    }

    public function handle()
    {
        $service = new UpgrateAwardService($this->uid, $this->uniacid, $this->orderId);
        $service->handleAward();
    }
}