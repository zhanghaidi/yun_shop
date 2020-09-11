<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Yunshop\Appletslive\common\models\LiveRoom;

class SyncWxappLiveRoom extends Command
{

    protected $signature = 'command:syncwxappliveroom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步微信小程序直播间';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        Log::getMonolog()->popHandler();
        Log::useFiles(storage_path('logs/schedule.run.log'), 'info');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        LiveRoom::refresh(true);
    }
}
