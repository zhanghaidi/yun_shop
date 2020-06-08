<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/3/27
 * Time: 15:33
 */

namespace app\backend\modules\survey\models;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobHeartbeatJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public function __construct()
    {

    }

    public function handle()
    {
        //保存定时任务时间
//        if (JobHeartbeat::count() > 100) {
//            JobHeartbeat::select()->delete();
//        }
        JobHeartbeat::insert(['execution_time' => time()]);

    }
}