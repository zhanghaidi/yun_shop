<?php
/**
 * Created by PhpStorm.
 * User: 芸众网
 * Date: 2019/6/17
 * Time: 9:34
 */

namespace Yunshop\Designer\Common\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yunshop\Designer\services\SyncDiyMarketService;

class DiyMarkJob implements ShouldQueue
{

    use InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        (new SyncDiyMarketService($this->data))->handle();
    }
}

