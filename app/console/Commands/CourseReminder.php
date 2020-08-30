<?php
namespace app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use app\Jobs\SendTemplateMsgJob;

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
     * 公众号和小程序配置信息
     * @var array
     */
    protected $options = [];

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

        // 公众号
        $wechat_account = DB::table('account_wechats')
            ->select('key', 'secret')
            ->where('uniacid', 39)
            ->first();
        $this->options['wechat'] = [
            'app_id' => $wechat_account['key'],
            'secret' => $wechat_account['secret'],
        ];

        // 小程序
        $wxapp_account = DB::table('account_wxapp')
            ->select('key', 'secret')
            ->where('uniacid', 45)
            ->first();
        $this->options['wxapp'] = [
            'app_id' => $wxapp_account['key'],
            'secret' => $wxapp_account['secret'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Log::info('------------------------ 小程序直播提醒定时任务 BEGIN -------------------------------');

        $time_now = time();
        $wait_seconds = 60 * 15;
        $check_time_range = [$time_now + $wait_seconds, $time_now + $wait_seconds + 60];

        // 1、查询距离当前时间点n~n+1分钟之间即将发布的视频
        $replay_publish_soon = DB::table('yz_appletslive_replay')
            ->select('id', 'rid', 'title', 'doctor', 'publish_time')
            ->where('delete_time', 0)
            ->whereBetween('publish_time', $check_time_range)
            ->get()->toArray();

        // Log::info('即将发布课程视频', $replay_publish_soon);

        if (!empty($replay_publish_soon)) {

            // 2、查询即将发布的视频关联的课程
            $rela_room = DB::table('yz_appletslive_room')
                ->whereIn('id', array_unique(array_column($replay_publish_soon, 'rid')))
                ->where('delete_time', 0)
                ->pluck('name', 'id')->toArray();

            // Log::info('视频关联的课程', $rela_room);

            // 3、查询关注了这些课程的所有小程序用户信息(openid)
            $subscribed_user = DB::table('yz_appletslive_room_subscription')
                ->select('user_id', 'room_id')
                ->whereIn('room_id', array_keys($rela_room))
                ->get()->toArray();

            // Log::info('订阅了课程的用户', $subscribed_user);

            if (!empty($subscribed_user)) {

                // 3.1、存在已关注课程的用户，查询用户openid
                $subscribed_uid = array_unique(array_column($subscribed_user, 'user_id'));
                $wxapp_user = DB::table('diagnostic_service_user')
                    ->select('ajy_uid', 'openid', 'unionid')
                    ->whereIn('ajy_uid', $subscribed_uid)
                    ->get()->toArray();
                $subscribed_unionid = array_column($wxapp_user, 'unionid');
                $wechat_user = DB::table('mc_mapping_fans')
                    ->select('uid', 'unionid', 'openid')
                    ->where('follow', 1)
                    ->where('uniacid', 39)
                    ->whereIn('unionid', $subscribed_unionid)
                    ->get()->toArray();
                array_walk($subscribed_user, function (&$item) use ($wxapp_user, $wechat_user) {
                    foreach ($wxapp_user as $user) {
                        if ($user['ajy_uid'] == $item['user_id']) {
                            $item['unionid'] = $user['unionid'];
                            $item['wxapp_openid'] = $user['openid'];
                            break;
                        }
                    }
                    $item['wechat_openid'] = '';
                    foreach ($wechat_user as $user) {
                        if ($user['unionid'] == $item['unionid']) {
                            $item['wechat_openid'] = $user['openid'];
                            break;
                        }
                    }
                });
            }

            // 4、组装队列数据
            $job_list = [];
            foreach ($replay_publish_soon as $replay) {
                // 4.1、当前课程有哪些订阅用户
                foreach ($subscribed_user as $user) {
                    if ($user['room_id'] == $replay['rid']) {
                        $type = ($user['wechat_openid'] != '') ? 'wechat' : 'wxapp';
                        $openid = ($user['wechat_openid'] != '') ? $user['wechat_openid'] : $user['wxapp_openid'];
                        $job_param = $this->makeJobParam($type, $rela_room[$replay['rid']], $replay);
                        array_push($job_list, [
                            'type' => $type,
                            'openid' => $openid,
                            'options' => $job_param['options'],
                            'template_id' => $job_param['template_id'],
                            'notice_data' => $job_param['notice_data'],
                            'page' => $job_param['page'],
                        ]);
                    }
                }
            }

            // 5、添加消息发送任务到消息队列
            foreach ($job_list as $job_item) {
                $job = new SendTemplateMsgJob($job_item['type'], $job_item['options'], $job_item['template_id'], $job_item['notice_data'],
                    $job_item['openid'], '', $job_item['page']);
                $dispatch = dispatch($job);
                Log::info("模板消息内容:", $job_item);
                if ($job_item['type'] == 'wechat') {
                    Log::info("队列已添加:发送公众号模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                } elseif ($job_item['type'] == 'wxapp') {
                    Log::info("队列已添加:发送小程序订阅模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                }
            }
        }

        // 6. 查询即将开播的特卖直播
        $live_start_soon = DB::table('yz_appletslive_liveroom')
            ->select('id', 'name', 'anchor_name', 'start_time')
            ->where('live_status', 102)
            ->whereBetween('start_time', $check_time_range)
            ->get()->toArray();

        // Log::info('即将开始的小程序直播间', $live_start_soon);

        $replay_publish_soon = empty($live_start_soon) ? [] : DB::table('yz_appletslive_replay')
            ->select('id', 'rid', 'room_id')
            ->whereIn('room_id', array_column($live_start_soon, 'id'))
            ->where('delete_time', 0)
            ->get()->toArray();
        if (empty($replay_publish_soon)) {
            $replay_publish_soon = [];
        }
        array_walk($replay_publish_soon, function (&$item) use ($live_start_soon) {
            foreach ($live_start_soon as $live) {
                if ($item['room_id'] == $live['id']) {
                    $item['title'] = $live['name'];
                    $item['doctor'] = $live['anchor_name'];
                    $item['publish_time'] = $live['start_time'];
                    break;
                }
            }
        });

        // Log::info('关联特卖直播', $replay_publish_soon);

        if (!empty($replay_publish_soon)) {

            // 7. 查询关联的特卖专辑
            $rela_room = empty($replay_publish_soon) ? [] : DB::table('yz_appletslive_room')
                ->whereIn('id', array_unique(array_column($replay_publish_soon, 'rid')))
                ->where('delete_time', 0)
                ->pluck('name', 'id')->toArray();

            // Log::info('关联特卖专辑', $rela_room);

            if (!empty($rela_room)) {

                // 8. 查询订阅了相关特卖专辑的用户
                $subscribed_user = DB::table('yz_appletslive_room_subscription')
                    ->select('user_id', 'room_id')
                    ->whereIn('room_id', array_keys($rela_room))
                    ->get()->toArray();

                // Log::info('订阅了特卖专辑的用户', $subscribed_user);

                if (!empty($subscribed_user)) {

                    // 8.1、存在已关注特卖专辑的用户，查询openid
                    $subscribed_uid = array_unique(array_column($subscribed_user, 'user_id'));
                    $wxapp_user = DB::table('diagnostic_service_user')
                        ->select('ajy_uid', 'openid', 'unionid')
                        ->whereIn('ajy_uid', $subscribed_uid)
                        ->get()->toArray();
                    $subscribed_unionid = array_column($wxapp_user, 'unionid');
                    $wechat_user = DB::table('mc_mapping_fans')
                        ->select('uid', 'unionid', 'openid')
                        ->where('follow', 1)
                        ->where('uniacid', 39)
                        ->whereIn('unionid', $subscribed_unionid)
                        ->get()->toArray();
                    array_walk($subscribed_user, function (&$item) use ($wxapp_user, $wechat_user) {
                        foreach ($wxapp_user as $user) {
                            if ($user['ajy_uid'] == $item['user_id']) {
                                $item['unionid'] = $user['unionid'];
                                $item['wxapp_openid'] = $user['openid'];
                                break;
                            }
                        }
                        $item['wechat_openid'] = '';
                        foreach ($wechat_user as $user) {
                            if ($user['unionid'] == $item['unionid']) {
                                $item['wechat_openid'] = $user['openid'];
                                break;
                            }
                        }
                    });

                }

                // 9、组装队列数据
                $job_list = [];
                foreach ($replay_publish_soon as $replay) {
                    // 9.1、当前直播有哪些订阅用户
                    foreach ($subscribed_user as $user) {
                        if ($user['room_id'] == $replay['rid']) {
                            $type = ($user['wechat_openid'] != '') ? 'wechat' : 'wxapp';
                            $openid = ($user['wechat_openid'] != '') ? $user['wechat_openid'] : $user['wxapp_openid'];
                            $job_param = $this->makeJobParam($type, $rela_room[$replay['rid']], $replay);
                            array_push($job_list, [
                                'type' => $type,
                                'openid' => $openid,
                                'options' => $job_param['options'],
                                'template_id' => $job_param['template_id'],
                                'notice_data' => $job_param['notice_data'],
                                'page' => $job_param['page'],
                            ]);
                        }
                    }
                }

                // 10、添加消息发送任务到消息队列
                foreach ($job_list as $job_item) {
                    $job = new SendTemplateMsgJob($job_item['type'], $job_item['options'], $job_item['template_id'], $job_item['notice_data'],
                        $job_item['openid'], '', $job_item['page']);
                    $dispatch = dispatch($job);
                    Log::info("模板消息内容:", $job_item);
                    if ($job_item['type'] == 'wechat') {
                        Log::info("队列已添加:发送公众号模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                    } elseif ($job_item['type'] == 'wxapp') {
                        Log::info("队列已添加:发送小程序订阅模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                    }
                }
            }
        }

        // Log::info("------------------------ 小程序直播提醒定时任务 END -------------------------------\n");
    }

    /**
     * 组装Job任务需要的参数
     * @param $type
     * @param $room_name
     * @param $replay_info
     * @return array
     */
    private function makeJobParam($type, $room_name, $replay_info)
    {
        define('COURSE_PATH', '/pages/course/CouRse/index');
        define('LIVE_PATH', '/pages/course/liveBroadcast/liveBroadcast');

        $param = [];
        $jump_page = '/pages/template/rumours/index?share=1&shareUrl=';
        $jump_tail = COURSE_PATH . '?tid=' . $replay_info['rid'];

        if ($type == 'wechat') {

            $first_value = '尊敬的用户,您订阅的课程有新视频要发布啦~';
            $remark_value = '最新视频【' . $replay_info['title'] . '】将于' . date('Y-m-d H:i', $replay_info['publish_time']) . '倾情发布!';
            if ($replay_info['room_id'] > 0) {
                $jump_tail = LIVE_PATH . '?tid=' . $replay_info['rid'];
                $first_value = '尊敬的用户,您订阅的特卖直播马上开始直播啦~';
                $remark_value = '最新直播【' . $replay_info['title'] . '】将于' . date('Y-m-d H:i', $replay_info['publish_time']) . '倾情发布!';
            }

            $param['options'] = $this->options['wechat'];
            $param['page'] = $jump_page . urlencode($jump_tail);
            $param['template_id'] = 'c-tYzcbVnoqT33trwq6ckW_lquLDPmqySXvntFJEMhE';
            $param['notice_data'] = [
                'first' =>  ['value' => $first_value, 'color' => '#173177'],
                'keyword1' => ['value' => '【' . $room_name . '】', 'color' => '#173177'],
                'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
                'keyword3' => ['value' => '更新中', 'color' => '#173177'],
                'remark' => ['value' => $remark_value, 'color' => '#173177'],
            ];

        } elseif ($type == 'wxapp') {

            $thing1_value = '课程更新';
            if ($replay_info['room_id'] > 0) {
                $jump_tail = LIVE_PATH . '?tid=' . $replay_info['rid'];
                $thing1_value = '品牌特卖开播提醒';
            }

            $param['options'] = $this->options['wxapp'];
            $param['page'] = $jump_page . urlencode($jump_tail);
            $param['template_id'] = 'ABepy-L03XH_iU0tPd03VUV9KQ_Vjii5mClL7Qp8_jc';
            $param['notice_data'] = [
                'thing1' => ['value' => $thing1_value, 'color' => '#173177'],
                'thing2' => ['value' => '【' . $room_name . '】', 'color' => '#173177'],
                'name3' => ['value' => $replay_info['doctor'], 'color' => '#173177'],
                'thing4' => ['value' => date('Y-m-d H:i', $replay_info['publish_time']), 'color' => '#173177'],
            ];
        }

        return $param;
    }
}
