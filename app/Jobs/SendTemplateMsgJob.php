<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Core\Exceptions\HttpException;
use Illuminate\Support\Facades\App;

class SendTemplateMsgJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $config;

    /**
     * SendTemplateMsgJob constructor.
     * @param $type
     * @param $options
     * @param $template_id
     * @param $notice_data
     * @param $openid
     * @param string $url
     * @param string $page
     * @param bool $refresh_miniprogram_access_token
     */
    public function __construct($type, $options, $template_id, $notice_data, $openid, $url = '', $page = '', $miniprogram = array())
    {
        $this->config = [
            'type' => $type,
            'options' => $options,
            'template_id' => $template_id,
            'notice_data' => $notice_data,
            'openid' => $openid,
            'url' => $url,
            'page' => $page,
            'miniprogram' => $miniprogram
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $environment = App::environment();
        Log::info('SendTemplateMsgJob environment:' . $environment);

        if ($this->config['type'] == 'wechat') {
            Log::info("------------------------ 发送公众号模板消息 BEGIN -------------------------------");

            if ($this->config['miniprogram']) {
                $miniprogram = $this->config['miniprogram'];
            }else{
                $miniprogram = ['miniprogram' => [
                    'appid' => 'wxcaa8acf49f845662', //小程序appid
                    'pagepath' => $this->config['page'],
                ]];
            }


            try {
                $app = new Application($this->config['options']);
                $app = $app->notice;
                $result = $app
                    ->uses($this->config['template_id'])
                    ->andData($this->config['notice_data'])
                    ->andReceiver($this->config['openid'])
                    ->andUrl($this->config['url'])
                    ->send($miniprogram);
                Log::info('发送模板消息成功:', ['config' => $this->config, 'result' => $result]);
            } catch (HttpException $e) {
                Log::info('发送模板消息失败:' . $e->getMessage());
            }
            Log::info("------------------------ 发送公众号模板消息 END -------------------------------\n");
        } elseif ($this->config['type'] == 'wxapp') {
            Log::info("------------------------ 发送小程序订阅模板消息 BEGIN -------------------------------");
            $template_id = $this->config['template_id'];
            $notice_data = $this->config['notice_data'];
            $openid = $this->config['openid'];
            $page = $this->config['page'];
            $this->sendMiniprogramSubscribeMsg($template_id, $notice_data, $openid, $page);
            Log::info("------------------------ 发送小程序订阅模板消息 END -------------------------------\n");
        } else {
            Log::info('未知的任务:');
        }
    }

    /**
     * 获取小程序access_token
     * @return bool|mixed|null
     */
    private function getMiniprogramAccessToken()
    {
        //fixbyzhd-2020-10-29 改写小程序统一调用生产access_token
        $url = "https://www.aijuyi.net/api/accesstoken.php?type=4&appid=%s&secret=%s";
        Log::debug('app/jobs/SendTemplateMsgJob:'.$url);
        $url = sprintf($url, $this->config['options']['app_id'], $this->config['options']['secret']);
        $response = self::curl_get($url);
        $result = @json_decode($response, true);

        return $result['accesstoken'];

    }



    /**
     * 发送小程序订阅消息
     * @param $template_id
     * @param $notice_data
     * @param $openid
     * @param string $page
     * @return mixed
     */
    private function sendMiniprogramSubscribeMsg($template_id, $notice_data, $openid, $page = '')
    {
        $access_token = $this->getMiniprogramAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=" . ($access_token ? $access_token : '');
        $post_data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'page' => $page,
            'data' => $notice_data,
        ];

        $environment = App::environment();
        if ($environment == 'dev') {
            $post_data['miniprogram_state'] = 'developer';
        }
        if ($environment == 'test') {
            $post_data['miniprogram_state'] = 'trial';
        }

        $state = array_key_exists('miniprogram_state', $post_data) ? $post_data['miniprogram_state'] : 'formal';
        Log::info("订阅消息跳转小程序类型:{$state}");

        $response = $this->curl_post($url, $post_data);
        if ($response === false) {
            Log::error('小程序模板消息接口调用失败:false', ['url' => $url]);
        }
        $result = json_decode($response, true);
        if (!$result || !is_array($result)) {
            Log::error('小程序订阅消息发送失败:', ['url' => $url, 'config' => [
                'template_id' => $template_id, 'notice_data' => $notice_data, 'openid' => $openid,
            ], 'result' => $result]);
        } else {
            Log::info('小程序订阅消息发送成功:', ['url' => $url, 'config' => [
                'template_id' => $template_id, 'notice_data' => $notice_data, 'openid' => $openid,
            ], 'result' => $result]);
        }
        return $result;
    }

    /**
     * PHP 处理 post数据请求
     * @param $url 请求地址
     * @param array $params 参数数组
     * @return mixed
     */
    private function curl_post($url,array $params = array()){
        $data_string = json_encode($params);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch,CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json'
            )
        );
        $data = curl_exec($ch);
        curl_close($ch);
        return ($data);
    }

    /**
     * @param string $url get请求地址
     * @param int $httpCode 返回状态码
     * @return mixed
     */
    private function curl_get($url,&$httpCode = 0){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        //不做证书校验，部署在linux环境下请改位true
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
        $file_contents = curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $file_contents;
    }
}
