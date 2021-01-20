<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/28
 * Time: 4:56 PM
 */

namespace Yunshop\Mryt\common\jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Yunshop\Mryt\services\OrderAwardService;

class OrderAwardJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $log;

    public function __construct($log)
    {
        $this->log = $log;
    }

    public function handle()
    {
        $service = new OrderAwardService($this->log);
        $service->handleAward();
    }
}