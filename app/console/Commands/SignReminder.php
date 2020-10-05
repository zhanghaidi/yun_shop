<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use app\Jobs\SendTemplateMsgJob;
use Illuminate\Support\Facades\App;

class SignReminder extends Command
{
    protected $signature = 'command:signreminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '签到提醒命令行工具';

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
        // Log::info('------------------------ 签到播提醒定时任务 BEGIN -------------------------------');

        $time_now = strtotime(date('Y-m-d', time()));
        $betweenDaySign = 3;
        $startTimes = strtotime(date('Y-m-d', strtotime("-$betweenDaySign day")));
        $whereBetweenSign = [$startTimes, $time_now];
        //1  查询所有最近三天有签到过的会员 更新时间在三天之内的会员
        $sign_users = DB::table('yz_sign')
            ->select('id', 'uniacid', 'member_id', 'cumulative_number', 'updated_at')
            ->whereBetween('updated_at', $whereBetweenSign)
            ->get()->toArray();

         Log::info('近三天有签到过的会员', $sign_users);

        if (!empty($sign_users)) {

            // 2、查询签到用户的小程序用户信息(openid)
            $member_ids = array_unique(array_column($sign_users, 'member_id'));
            $wxapp_user = DB::table('diagnostic_service_user')
                ->select('ajy_uid', 'unionid', 'openid','nickname')
                ->whereIn('ajy_uid', $member_ids)
                ->get()->toArray();
            //如果小程序 公众号ID
            $subscribed_unionid = array_column($wxapp_user, 'unionid');
            $wechat_user = DB::table('mc_mapping_fans')
                ->select('uid', 'unionid', 'openid','nickname')
                ->where('follow', 1)
                ->where('uniacid', 39)
                ->whereIn('unionid', $subscribed_unionid)
                ->get()->toArray();
            //3 组装数据
            array_walk($sign_users, function (&$item) use ($wxapp_user, $wechat_user) {
                //小程序openid
                foreach ($wxapp_user as $user) {
                    if ($user['ajy_uid'] == $item['member_id']) {
                        $item['unionid'] = $user['unionid'];
                        $item['wxapp_openid'] = $user['openid'];
                        $item['wxapp_nickname'] = $user['nickname'];
                        break;
                    }
                }
                //公众号openid
                $item['wechat_openid'] = '';
                foreach ($wechat_user as $user) {
                    if ($user['unionid'] == $item['unionid']) {
                        $item['wechat_openid'] = $user['openid'];
                        $item['wechat_nickname'] = $user['nickname'];
                        break;
                    }
                }
            });

            // 4、组装队列数据
            $job_list = [];
            foreach ($sign_users as $user) {
                //优先公众号推送，公众号没有再推小程序
                $type = ($user['wechat_openid'] != '') ? 'wechat' : 'wxapp';
                $openid = ($user['wechat_openid'] != '') ? $user['wechat_openid'] : $user['wxapp_openid'];

                $job_param = $this->makeJobParam($type, $user);
                array_push($job_list, [
                    'type' => $type,
                    'openid' => $openid,
                    'options' => $job_param['options'],
                    'template_id' => $job_param['template_id'],
                    'notice_data' => $job_param['notice_data'],
                    'page' => $job_param['page'],
                ]);

            }

            // 5、添加消息发送任务到消息队列
            foreach ($job_list as $job_item) {
                $job = new SendTemplateMsgJob($job_item['type'], $job_item['options'], $job_item['template_id'], $job_item['notice_data'], $job_item['openid'], '', $job_item['page']);
                $dispatch = dispatch($job);
                Log::info("模板消息内容:", $job_item);
                if ($job_item['type'] == 'wechat') {
                    Log::info("队列已添加:发送公众号模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                } elseif ($job_item['type'] == 'wxapp') {
                    Log::info("队列已添加:发送小程序订阅模板消息", ['job' => $job, 'dispatch' => $dispatch]);
                }
            }
        }

         Log::info("------------------------ 小程序签到提醒定时任务 END -------------------------------\n");
    }

    /**
     * 组装Job任务需要的参数
     * @param $type
     * @param $room_name
     * @param $replay_info
     * @return array
     */
    private function makeJobParam($type, $users)
    {

        define('SIGN_PATH', 'pages/template/rumours/index?_source=share&_s_path=/pages/rumours/signin/index');//签到小程序跳转地址地址

        $param = [];
        $jump_page = SIGN_PATH ;

        if ($type == 'wechat') {

            $first_value = $users['wechat_nickname'].'您好,您签到领取的健康金到账啦，今天的健康金还没领取哦，赶快来签到~';
            $remark_value = '坚持签到即可奖励健康金，更多惊喜等着你~';

            $param['options'] = $this->options['wechat'];
            $param['page'] = $jump_page;
            $param['template_id'] = 'LeEHrJ8uCb6oB7VTzH-q8UZI9ISdo5o6SNZhezrCU4s';
            $param['notice_data'] = [
                'first' => ['value' => $first_value, 'color' => '#173177'],
                'keyword1' => ['value' => '领取健康金资格审核通过，点击领取守护家人健康~', 'color' => '#173177'],
                'keyword2' => ['value' => date('Y-m-d H:i', time()), 'color' => '#173177'],
                'remark' => ['value' => $remark_value, 'color' => '#173177'],
            ];

        } elseif ($type == 'wxapp') {

            $thing1_value = $users['wxapp_nickname'].'您好,您签到领取的健康金到账啦，今天的健康金还没领取哦，赶快来签到~';
            $thing2_value = '每天签到领取健康金啦，点击领取守护家人健康~';

            $param['options'] = $this->options['wxapp'];
            $param['page'] = $jump_page;
            $param['template_id'] = 'ZQzayZvME4-DaYnkHIBDzPNyttv738hpYkKA4iBbY5Y';
            $param['notice_data'] = [
                'thing1' => ['value' => $thing1_value, 'color' => '#173177'],
                'thing2' => ['value' => $thing2_value, 'color' => '#173177'],
                'name3' => ['value' => '坚持签到即可奖励健康金，更多惊喜等着你~', 'color' => '#173177'],
            ];
        }

        return $param;
    }
}
