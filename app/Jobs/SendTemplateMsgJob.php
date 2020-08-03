<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use app\common\models\AccountWechats;
use EasyWeChat\Foundation\Application;

class SendTemplateMsgJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $config;

    public function __construct($template_id, $notice_data, $type, $uniacid, $openid, $wxapp_path = '')
    {
        $this->config = [
            'template_id' => $template_id,
            'notice_data' => $notice_data,
            'type' => $type,
            'uniacid' => $uniacid,
            'openid' => $openid,
            'wxapp_path' => $wxapp_path,
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $miniprogram = [];
        if ($this->config['wxapp_path'] == '') {
            $miniprogram = ['miniprogram' => [
                'appid' => 'wxcaa8acf49f845662',
                'pagepath' => $this->config['wxapp_path'],
            ]];
        }
        $account = AccountWechats::getAccountByUniacid($this->config['uniacid']);
        $options = [
            'app_id' => $account['key'],
            'secret' => $account['secret'],
        ];
        if ($this->config['type'] == 'wechat') {
            $app = new Application($options);
            $app = $app->notice;
            $result = $app
                ->uses($this->config['template_id'])
                ->andData($this->config['notice_data'])
                ->andReceiver($this->config['openid'])
                ->andUrl('')
                ->send($miniprogram);
            Log::info('发送模板消息:', ['config' => $this->config, 'result' => $result]);
        } elseif ($this->config['type'] == 'wxapp') {
            $this->sendWxappTemplateMsg($account->getAccessToken());
        }
    }

    private function sendWxappTemplateMsg($access_token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $access_token;
        $post_data = [
            "touser" => $this->config['openid'],
            "template_id" => $this->config['template_id'],
            "data" => $this->config['notice_data'],
        ];
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_AUTOREFERER,true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);//设置超时时间
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($curl,CURLOPT_POST,true);
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($curl);
        if (false === $response) {
            Log::error('小程序模板消息接口调用失败:', ['config' => $this->config, 'error' => curl_error($curl)]);
        }
        curl_close($curl);
        $result = json_decode($response, true);
        if (!$result || !is_array($result)) {
            Log::error('小程序模板消息发送失败:', ['config' => $this->config, 'result' => $result]);
        } else {
            Log::info('小程序模板消息接口调用成功:', ['config' => $this->config, 'result' => $result]);
        }
    }
}
