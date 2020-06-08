<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2020/3/27
 * Time: 15:26
 */

namespace app\backend\modules\survey\listeners;

use app\backend\modules\survey\models\CronHeartbeat;
use app\backend\modules\survey\models\JobHeartbeatJob;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;

class HeartbeatStatusLogListener
{
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {
        $events->listen('cron.collectJobs', function () {
            \Cron::add('HeartbeatStatusLog', '*/1 * * * *', function() {
                $this->handle();
            });
        });
    }

    public function handle()
    {
        //保存定时任务时间
//        if (CronHeartbeat::count() > 100) {
//            CronHeartbeat::select()->delete();
//        }
        CronHeartbeat::insert(['execution_time' => time()]);


        $job = new JobHeartbeatJob();
        dispatch($job);
    }
}