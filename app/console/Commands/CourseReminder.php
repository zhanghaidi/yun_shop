<?php
namespace app\Console\Commands;

use Illuminate\Console\Command;
use app\Jobs\CourseRemindMsgJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseReminder extends Command
{

    protected $signature = 'command:coursereminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '课程提醒命令行工具';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("------------------------ LOG BEGIN -------------------------------");

        $time_now = time();
        $time_check_point = $time_now + 900;
        $time_check_where = [$time_check_point, $time_check_point + 600];
        $replay_publish_soon = DB::table('appletslive_replay')
            ->select('id', 'rid', 'title', 'publish_time')
            ->whereBetween('publish_time', $time_check_where)
            ->get()->toArray();

        Log::info('time_now: ' . $time_now);
        Log::info('time_check_where: ', $time_check_where);
        Log::info('replay_publish_soon: ', $replay_publish_soon);

        if (empty($replay_publish_soon)) {
            Log::info('no live publish soon.');
        } else {

            $rela_room = DB::table('appletslive_room')
                ->whereIn('id', array_column($replay_publish_soon, 'rid'))
                ->pluck('name', 'id')->toArray();
            foreach ($replay_publish_soon as $replay) {

                Log::info('live: ' . $rela_room[$replay['rid']] . ' - ' . $replay['title']);

                $subscribed_uid = DB::table('appletslive_room_subscription')
                    ->where('room_id', $replay['rid'])
                    ->pluck('user_id')->toArray();
                if (empty($subscribed_uid)) {
                    Log::info('send live publish remind message to nobody.');
                } else {
                    $to_user_openid = DB::table('diagnostic_service_user')
                        ->whereIn('ajy_uid', $subscribed_uid)
                        ->pluck('openid')->toArray();
                    foreach ($to_user_openid as $openid) {
                        $job = new CourseRemindMsgJob($openid, $rela_room, $replay);
                        dispatch($job);
                    }
                }
            }
        }


        $room = ['id' => 1, 'name' => '测试课程'];
        $replay = ['id' => 1, 'title' => '测试录播视频', 'publish_time' => strtotime('+15 minutes')];
        $openid = 'owVKQwWK2G_K6P22he4Fb2nLI6HI';
        $job = new CourseRemindMsgJob($openid, $room, $replay);
        dispatch($job);

        Log::info("------------------------ LOG END -------------------------------\n");
    }

}
