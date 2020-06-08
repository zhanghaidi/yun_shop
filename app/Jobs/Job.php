<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class Job implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 5;

    public $timeout = 120;


    protected $event;


    public function __construct($event)
    {
        $this->event = $event;
    }


}
