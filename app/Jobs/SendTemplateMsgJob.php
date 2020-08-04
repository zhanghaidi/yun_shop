<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use EasyWeChat\Foundation\Application;
use app\common\services\notice\sendSubscribeMessage;

class SendTemplateMsgJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $config;

    public function __construct($type, $option, $template_id, $notice_data, $openid, $url = '', $page = '')
    {
        $this->config = [
            'type' => $type,
            'option' => $option,
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
            Log::info('发送模板消息:', ['config' => $this->config, 'result' => $result]);
        } elseif ($this->config['type'] == 'wxapp') {
            if ($this->config['page'] != '') {
                $this->config['notice_data']['page'] = $this->config['page'];
            }
            $service = new sendSubscribeMessage();
            $service->sendSubscribeMessage($this->config['template_id'], $this->config['notice_data'], $this->config['openid']);
        }
    }
}
