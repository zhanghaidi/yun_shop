<?php
/**
 * Created by PhpStorm.
 * User: CHUWU
 * Date: 2019/5/17
 * Time: 19:14
 */

namespace Yunshop\Wechat\jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

// 同步会员
class SyncFansInfoJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $list = [];

    public function __construct($list)
    {
        $this->list = $list;
    }

    public function handle()
    {

    }

}

