<?php
namespace app\Console\Commands;

use Illuminate\Console\Command;
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
        Log::info("------------------------ 课程提醒定时任务 BEGIN -------------------------------");

        // 1、查询距离当前时间点10-15分钟之间即将发布的视频
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
            Log::info('未找到即将新发布的视频.');
        } else {

            // 2、查询即将发布的视频关联的课程
            $rela_room = DB::table('appletslive_room')
                ->whereIn('id', array_unique(array_column($replay_publish_soon, 'rid')))
                ->pluck('name', 'id')->toArray();

            // 3、查询关注了这些课程的所有小程序用户信息(openid)
            $subscribed_user = DB::table('appletslive_room_subscription')
                ->select('user_id', 'room_id')
                ->where('room_id', array_keys($rela_room))
                ->get()->toArray();
            if (empty($subscribed_user)) {
                Log::info('未找到订阅了课程的用户.');
            } else {
                $subscribed_uid = array_unique(array_column($subscribed_user, 'user_id'));
                // 3.1、存在已关注课程的用户，查询用户openid
                $wxapp_user = DB::table('diagnostic_service_user')
                    ->select('ajy_uid', 'openid', 'unionid')
                    ->whereIn('ajy_uid', $subscribed_uid)
                    ->get()->toArray();
                $subscribed_unionid = array_column($wxapp_user, 'unionid');
                $wechat_user = DB::table('mc_mapping_fans')
                    ->select('uid', 'openid', 'follow')
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
                        if ($user['unionid'] == $item['unionid'] && $user['follow'] == 1) {
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
                $current_subscribed_user = [];
                foreach ($subscribed_user as $user) {
                    if ($user['room_id'] == $replay['rid']) {
                        $type = ($user['wechat_openid'] != '') ? 'wechat' : 'wxapp';
                        $openid = ($user['wechat_openid'] != '') ? $user['wechat_openid'] : $user['wxapp_openid'];
                        $job_param = $this->makeJobParam($type, $rela_room[$replay['rid']], $replay);
                        $page = 'pages/template/rumours/index?room_id=' . $replay['rid'];
                        array_push($job_list, [
                            'type' => $type,
                            'options' => $job_param['options'],
                            'template_id' => $job_param['template_id'],
                            'notice_data' => $job_param['notice_data'],
                            'openid' => $openid,
                            'page' => $page,
                        ]);
                    }
                }
            }

            Log::info("队列数据组装完成", $job_list);

            // 5、添加消息发送任务到消息队列
            foreach ($job_list as $job) {
                $job = SendTemplateMsgJob($job['type'], $$job['options'], $job['template_id'], $job['notice_data'],
                    $job['openid'], '', $job['page']);
                $dispatch = dispatch($job);
                if ($job['type'] == 'wechat') {
                    Log::info("队列已添加:发送公众号模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                } elseif ($job['type'] == 'wxapp') {
                    Log::info("队列已添加:发送小程序订阅模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                }
            }
        }

        Log::info("------------------------ 课程提醒定时任务 END -------------------------------\n");
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
        $param = [];
        if ($type == 'wechat') {
            $param['options'] = $this->options['wechat'];
            $param['template_id'] = 'c-tYzcbVnoqT33trwq6ckW_lquLDPmqySXvntFJEMhE';
            $param['notice_data'] = [
                'first' => ['value' => '尊敬的用户,您订阅的课程有新视频要发布啦~', 'color' => '#173177'],
                'keyword1' => ['value' => '【' . $room_name . '】', 'color' => '#173177'],
                'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
                'keyword3' => ['value' => '更新中', 'color' => '#173177'],
                'remark' => [
                    'value' => '最新视频【' . $replay_info['title'] . '】将于' . date('Y-m-d H:i', $replay_info['publish_time']) . '震撼发布!',
                    'color' => '#173177',
                ],
            ];
        } elseif ($type == 'wxapp') {
            $param['options'] = $this->options['wxapp'];
            $param['template_id'] = 'ABepy-L03XH_iU0tPd03VUV9KQ_Vjii5mClL7Qp8_jc';
            $param['notice_data'] = [
                'thing1' => ['value' => '课程更新', 'color' => '#173177'],
                'thing2' => ['value' => '【和大师一起学艾灸】', 'color' => '#173177'],
                'name3' => ['value' => '艾居益灸师', 'color' => '#173177'],
                'thing4' => ['value' => '最新视频【每次艾灸几个穴位合适】将在' . date('Y-m-d H:i', strtotime('+15 minutes')) . '震撼发布!', 'color' => '#173177'],
            ];
        }
        return $param;
    }
}
