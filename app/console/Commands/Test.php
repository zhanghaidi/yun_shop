<?php

namespace app\Console\Commands;


use app\common\facades\Setting;

use app\common\models\AccountWechats;
use app\Jobs\DispatchesJobs;
use app\Jobs\MessageNoticeJob;
use app\Jobs\SendTemplateMsgJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Test extends Command
{

    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试群发消息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        parent::__construct();

        Log::getMonolog()->popHandler();
        Log::useFiles(storage_path('logs/template.push.log'), 'info');

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
//        Setting::$uniqueAccountId = \YunShop::app()->uniacid = 9;
//        $job = new MessageNoticeJob(1, [], '', '');
//        DispatchesJobs::dispatch($job,DispatchesJobs::LOW);
        global $template_id;
        global $jump_page;
        global $notice_data;

        $jump_page = '/pages/template/rumours/index?share=1&shareUrl=';
        $template_id = 'c-tYzcbVnoqT33trwq6ckW_lquLDPmqySXvntFJEMhE';
        $notice_data = [
            'first' => ['value' => '尊敬的用户,您订阅的课程有新视频要发布啦~', 'color' => '#173177'],
            'keyword1' => ['value' => '【和大师一起学艾灸】', 'color' => '#173177'],
            'keyword2' => ['value' => '长期有效', 'color' => '#173177'],
            'keyword3' => ['value' => '更新中', 'color' => '#173177'],
            'remark' => [
                'value' => '最新视频【每次艾灸几个穴位合适】将于' . date('Y-m-d H:i', strtotime('+15 minutes')) . '震撼发布!',
                'color' => '#173177',
            ],
        ];

        //查询公众号粉丝 发送模板消息
        DB::table('mc_mapping_fans')->where('follow',1)->orderBy('fanid')
            ->chunk(1000,function ($mapping_fans_list) {
                foreach ($mapping_fans_list as $mapping_fans) {
                    $job = new SendTemplateMsgJob('wechat', $this->options['wechat'], $GLOBALS['template_id'], $GLOBALS['notice_data'],
                        $mapping_fans['openid'], '', $GLOBALS['jump_page']);
                    $dispatch = dispatch($job);
                    Log::info("队列已添加:发送公众号模板消息", ['uid'=>$mapping_fans['uid'],'job' => $job, 'dispatch' => $dispatch]);
                }
            });
    }

}
