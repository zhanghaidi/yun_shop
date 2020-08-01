<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use app\common\models\AccountWechats;
use EasyWeChat\Foundation\Application;

class CourseRemindMsgJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $timeout = 120;

    protected $config;

    public function __construct($openid, $room, $replay)
    {
        $this->config = [
            'tempId' => '121JxM8yyYPeCYSqPEgVPmcuLVOjx88qYtQ_cR0oTho',
            'noticeData' => [
                'first' => ['value' => '尊敬的用户,您订阅的课程【' . $room['name'] . '】有新视频要发布啦~', 'color' => '#173177'],
                'keyword1' => ['value' => date('Y-m-d H:i'), 'color' => '#173177'],
                'keyword2' => ['value' => '测试的^.^', 'color' => '#173177'],
                'keyword3' => ['value' => '测试的^.^', 'color' => '#173177'],
                'keyword4' => ['value' => '测试的^.^', 'color' => '#173177'],
                'remark' => ['value' => '最新视频【' . $replay['title'] . '】将在' . date('Y-m-d H:i') . '震撼发布!', 'color' => '#173177'],
            ],
            'openid' => $openid,
            'miniApp' => ['miniprogram' => [
                'appid' => 'wxcaa8acf49f845662',
                'pagepath' => '$this->pagepath',
            ]],
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $account = AccountWechats::getAccountByUniacid(39);
        $options = [
            'app_id' => $account['key'],
            'secret' => $account['secret'],
        ];
        $app = new Application($options);
        $app = $app->notice;
        $result = $app
            ->uses($this->config['tempId'])
            ->andData($this->config['noticeData'])
            ->andReceiver($this->config['openid'])
            ->andUrl('')
            ->send($this->config['miniApp']);
        Log::info('发送课程提醒模板消息:', $result);
    }
}
