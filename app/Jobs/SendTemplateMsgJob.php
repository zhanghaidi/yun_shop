<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use EasyWeChat\Foundation\Application;
use app\common\services\notice\SmallProgramNotice;

class SendTemplateMsgJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $config;

    public function __construct($type, $options, $template_id, $notice_data, $openid, $url = '', $page = '')
    {
        $this->config = [
            'type' => $type,
            'options' => $options,
            'template_id' => $template_id,
            'notice_data' => $notice_data,
            'openid' => $openid,
            'url' => $url,
            'page' => $page,
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->config['type'] == 'wechat') {
            Log::info("------------------------ 发送公众号模板消息 BEGIN -------------------------------");
            $miniprogram = [];
            if ($this->config['page'] != '') {
                $miniprogram = ['miniprogram' => [
                    'appid' => 'wxcaa8acf49f845662',
                    'pagepath' => $this->config['page'],
                ]];
            }
            $app = new Application($this->config['options']);
            $app = $app->notice;
            $result = $app
                ->uses($this->config['template_id'])
                ->andData($this->config['notice_data'])
                ->andReceiver($this->config['openid'])
                ->andUrl($this->config['url'])
                ->send($miniprogram);
            Log::info('发送模板消息成功:', ['config' => $this->config, 'result' => $result]);
            Log::info("------------------------ 发送公众号模板消息 END -------------------------------\n");
        } elseif ($this->config['type'] == 'wxapp') {
            Log::info("------------------------ 发送小程序订阅模板消息 BEGIN -------------------------------");
            $template_id = 'UKXQY-ReJezg0EHKvmp3yUQg-t644GNOaEIlV-Pqy84';
            $notice_data = [
                'thing1' => ['value' => '课程更新', 'color' => '#173177'],
                'thing2' => ['value' => '【和大师一起学艾灸】', 'color' => '#173177'],
                'time3' => ['value' => date('Y-m-d H:i', strtotime('+15 minutes')), 'color' => '#173177'],
            ];
            $openid = 'oP9ym5Bxp6D_sERpj340uIxuaUIo';
            $page = 'pages/template/rumours/index?room_id=5';
            // $template_id = $this->config['template_id'];
            // $notice_data = $this->config['notice_data'];
            // $openid = $this->config['openid'];
            // $page = $this->config['page'];
            $service = new SmallProgramNotice($this->config['options']);
            $service->sendSubscribeMessage($template_id, $notice_data, $openid, $page);
            Log::info("------------------------ 发送小程序订阅模板消息 END -------------------------------\n");
        } else {
            Log::info('未知的任务:');
        }
    }
}
