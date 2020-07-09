<?php

namespace Yunshop\ClockIn\jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yunshop\ClockIn\services\TimedTaskRewardService;

class addClockInRewardJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $rewardLogData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($rewardLogData)
    {
        $this->rewardLogData = $rewardLogData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new TimedTaskRewardService())->addClockRewardLog($this->rewardLogData);
    }

}
