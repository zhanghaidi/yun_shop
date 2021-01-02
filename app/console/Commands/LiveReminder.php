<?php

namespace app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use app\Jobs\SendTemplateMsgJob;
use Illuminate\Support\Facades\App;
use app\common\models\live\CloudLiveRoom;

class LiveReminder extends Command
{
    protected $signature = 'command:livereminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '云直播开播提醒命令行工具';

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

        /*Log::getMonolog()->popHandler();
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

        // 商城小程序
        $wxapp_account = DB::table('account_wxapp')
            ->select('key', 'secret')
            ->where('uniacid', 47)
            ->first();
        $this->options['wxapp'] = [
            'app_id' => $wxapp_account['key'],
            'secret' => $wxapp_account['secret'],
        ];*/
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

       /* $time_now = time();
        $wait_seconds = 60 * 2;
        $check_time_range = [$time_now, $time_now + $wait_seconds];
        //DB::enableQueryLog(); whereBetween('start_time', $check_time_range)->
        // 1、查询开始时间距离当前时间2分钟之内开播的直播 where('live_status', 101)暂时不卡播放状态 where('start_time', $check_time_range)->
        $startLiveRoom = CloudLiveRoom::select('uniacid','id','name','live_status','start_time','anchor_name')->get()->toArray();
        foreach ($startLiveRoom as $key=>$room){
            //$startLiveRoom[$key]['subscription']


            foreach ($subscription as $k => $v){
                $user = DB::table('diagnostic_service_user')->select('ajy_uid','shop_openid','unionid')->where('ajy_uid', $v['user_id'])->first();
                $fans = DB::table('mc_mapping_fans')->select('uid', 'openid')->where(['uid' => $value['user_id'], 'follow' => 1])->first();
                $user['openid'] = '';
                if($fans){
                    $user['openid'] = $fans['openid'];
                }

                $uniacid = $v['uniacid'];
                $type = $user['openid'] ? 'wechat' : 'wxapp';

                $openid = $user['openid'] ? $user['openid'] : $user['shop_openid'];

                $job_param = $this->makeJobParam($uniacid, $type, $room);
                //var_dump($job_param);die;

            }*/




        $time_now = time();
        $wait_seconds = 60 * 1;
        $check_time_range = [$time_now - $wait_seconds - 60,$time_now - $wait_seconds];

        // 1、查询开始时间距离当前时间2分钟之内开播的直播 where('live_status', 101)暂时不卡播放状态
        $startLiveRoom = CloudLiveRoom::whereBetween('start_time', $check_time_range)->select('uniacid','id','name','live_status','start_time','anchor_name')->get()->toArray();
        Log::info("获取的待开播直播间:", $startLiveRoom);
        //查询订阅开播直播间的用户
        foreach ($startLiveRoom as $room) {
            $subscription = DB::table('yz_cloud_live_room_subscription')
                ->select('id','uniacid','room_id','user_id')
                ->whereNull('deleted_at')
                ->where(['uniacid'=> $room['uniacid'], 'room_id'=>$room['id']])
                ->get()->toArray();

            foreach ($subscription as $value){
                $user = DB::table('diagnostic_service_user')->select('ajy_uid','shop_openid','unionid')->where('ajy_uid', $value['user_id'])->first();
                $fans = DB::table('mc_mapping_fans')->select('uid', 'openid')->where(['uid' => $value['user_id'], 'follow' => 1])->first();
                $user['openid'] = '';
                if($fans){
                    $user['openid'] = $fans['openid'];
                }

                $type = $user['openid'] ? 'wechat' : 'wxapp';

                $openid = $user['openid'] ? $user['openid'] : $user['shop_openid'];

                $job_param = $this->makeJobParam($value['uniacid'], $type, $room);
                Log::info("订阅用户:", $user);
                Log::info("模板消息内容:" . $type . $openid, $job_param);

                $job = new SendTemplateMsgJob($type, $job_param['options'], $job_param['template_id'], $job_param['notice_data'], $openid, '', $job_param['page'], $job_param['miniprogram']);
                $dispatch = dispatch($job);

                Log::info("队列已添加:".$type, ['job' => $job, 'dispatch' => $dispatch]);

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
    private function makeJobParam($uniacid, $type, $room)
    {
        $param = [];

        //查询平台配置的公众号和小程序
        $wechat_setting = DB::table('yz_setting')->select('id', 'uniacid', 'key', 'value')
            ->where([
                'uniacid' => $uniacid,
                'group' => 'plugin',
                'key' => 'wechat',
            ])->first();

        $wxapp_setting = DB::table('yz_setting')->select('id', 'uniacid', 'key', 'value')
            ->where([
                'uniacid' => $uniacid,
                'group' => 'plugin',
                'key' => 'min_app',
            ])->first();

        $wechat = unserialize($wechat_setting['value']);
        $wxapp = unserialize($wxapp_setting['value']);

        $param['wechat'] = [
            'app_id' => $wechat['app_id'],
            'secret' => $wechat['app_secret'],
        ];
        $param['wxapp'] = [
            'app_id' => $wxapp['key'],
            'secret' => $wxapp['secret'],
        ];


        //查询云直播配置的模板
        $wechat_template_setting = DB::table('yz_setting')->select('id', 'uniacid', 'key', 'value')
            ->where([
                'uniacid' => $uniacid,
                'group' => 'shop',
                'key' => 'live',
            ])->first();

        $wechat_template = unserialize($wechat_template_setting['value']);

        $wxapp_template_setting = DB::table('yz_setting')->select('id', 'uniacid', 'key', 'value')
            ->where([
                'uniacid' => $uniacid,
                'group' => 'shop',
                'key' => 'live',
            ])->first();

        $wxapp_template = unserialize($wechat_template_setting['value']);

        //define('CLOUD_LIVE_PATH', '/pages/cloud-live/live-player/live-player?tid='); //云直播间

        //$jump_page = '/pages/template/shopping/index?share=1&shareUrl=';

        //$jump_tail = CLOUD_LIVE_PATH . $room['id']; //直播间路径

        if ($type == 'wechat') {
            //todo 组装微信公众号模板变量

           //$first_value = '尊敬的用户,您订阅的直播间开始直播啦~';
           //$remark_value = '【' . $room['name'] . '】正在进行中,观看直播互动享更多福利优惠~';

           $param['options'] = $param['wechat'];
           $param['page'] = '';
           $param['template_id'] = 'c-tYzcbVnoqT33trwq6ckW_lquLDPmqySXvntFJEMhE'; //课程进度提醒模板
            $param['notice_data'] = [
                'first' =>  ['value' => $first_value, 'color' => '#173177'],
                'keyword1' => ['value' => '【' . $room['name'] . '】', 'color' => '#173177'],
                'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
                'keyword3' => ['value' => '开播中', 'color' => '#173177'],
                'remark' => ['value' => $remark_value, 'color' => '#173177'],
            ];
            $param['miniprogram'] =[
                'miniprogram' => [
                    'appid' => $this->options['wxapp']['app_id'],
                    'pagepath' => $param['page']
                ]
            ];


        } elseif ($type == 'wxapp') {
            //todo 组装小程序模板变量

            /*$thing1_value = '直播间开播提醒';

            $param['options'] = $this->options['wxapp'];
            $param['page'] = $jump_tail;
            $param['template_id'] = 'ABepy-L03XH_iU0tPd03VUV9KQ_Vjii5mClL7Qp8_jc';
            $param['notice_data'] = [
                'thing1' => ['value' => $thing1_value, 'color' => '#173177'],
                'thing2' => ['value' => '【' . $room['name'] . '】', 'color' => '#173177'],
                'name3' => ['value' => $room['anchor_name'], 'color' => '#173177'],
                'thing4' => ['value' => date('Y-m-d H:i', $room['start_time']), 'color' => '#173177'],
            ];*/
        }

        return $param;
    }

    /**
     * 获取独立的小程序，其上配置的小程序APPID和公众号APPID
     */
    public static function getAloneMiniApp()
    {
        $listRs = DB::table('yz_setting')->select('id', 'uniacid', 'key', 'value')

            ->whereIn('key', ['min_app', 'wechat'])
            ->where([
                'group' => 'plugin',
                'type' => 'array',
            ])->orderBy('id', 'asc')->get()->toArray();
        $miniAppRs = array();
        foreach ($listRs as $v) {
            if (!isset($v['id'])) {
                continue;
            }
            !isset($miniAppRs[$v['uniacid']]) && $miniAppRs[$v['uniacid']] = [];

            $v['value'] = unserialize($v['value']);
            if ($v['value'] == false) {
                continue;
            }
            $v['value'] = array_filter($v['value']);

            $miniAppRs[$v['uniacid']][$v['key']] = $v;
        }

        foreach ($miniAppRs as $k => $v) {
            if (!isset($v['min_app']) || !isset($v['wechat'])) {
                unset($miniAppRs[$k]);
            }
            if (!isset($v['min_app']['value']['key']) ||
                !isset($v['min_app']['value']['secret'])
            ) {
                unset($miniAppRs[$k]);
            }
            if (!isset($v['wechat']['value']['app_id']) ||
                !isset($v['wechat']['value']['app_secret'])
            ) {
                unset($miniAppRs[$k]);
            }

        }
        return $miniAppRs;
    }
}
