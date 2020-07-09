<?php

namespace Yunshop\Love\Common\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Yunshop\Love\Common\Services\TimedTaskAwardService;

class addCommissionAwardJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $memberId;
    protected $loveGive;
    protected $uniacId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($memberId, $loveGive, $uniacId)
    {
        $this->memberId = $memberId;
        $this->loveGive = $loveGive;
        $this->uniacId = $uniacId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new TimedTaskAwardService())->setCommissionAward($this->memberId, $this->loveGive,$this->uniacId);
    }
}
