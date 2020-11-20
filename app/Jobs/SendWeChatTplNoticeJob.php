<?php

namespace app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class SendWeChatTplNoticeJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $config;

    /**
     * SendWeChatTplNoticeJob constructor.
     * @param $openid
     * @param $options
     * @param $template_id
     * @param $notice_data
     * @param string $url
     * @param $topcolor
     * @param array $miniprogram
     */
    public function __construct($openid, $options, $template_id, $notice_data, $url = '', $topcolor, $miniprogram = array())
    {
        $this->config = [
            'openid' => $openid,
            'options' => $options,
            'template_id' => $template_id,
            'notice_data' => $notice_data,
            'url' => $url,
            'topcolor' => $topcolor,
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

        Log::info("-----SendWeChatTplNoticeJob". $environment ."-----发送公众号模板消息 ". $this->config['openid'] ." BEGIN-----" );

        $this->sendTplNotice($this->config['openid'], $this->config['template_id'], $this->config['notice_data'], $this->config['url'], $this->config['topcolor'], $this->config['miniprogram']);

        Log::info("------------------------ 发送公众号模板消息模板消息 END -------------------------------\n");

    }

    /**
     * 获取小程序access_token
     * @return bool|mixed|null
     */
    private function getAccessToken()
    {
        //fixbyzhd-2020-10-29 改写小程序统一调用生产access_token
        $url = "https://www.aijuyi.net/api/accesstoken.php?type=4&appid=%s&secret=%s";
        $url = sprintf($url, $this->config['options']['app_id'], $this->config['options']['secret']);
        $response = ihttp_request($url);
        $result = @json_decode($response['content'], true);

        return $result['accesstoken'];

    }

    //公众号模板消息发送接口
    private function sendTplNotice($touser, $template_id, $postdata, $url = '', $topcolor = '#FF683F', $miniprogram = array('appid' => '', 'pagepath' => '')) {

        if (empty($touser)) {
            Log::info($touser."error：参数错误,粉丝openid不能为空");
        }
        if (empty($template_id)) {

            Log::info("error：参数错误,模板标示不能为空");
        }
        if (empty($postdata) || !is_array($postdata)) {
            Log::info($touser.'参数错误,请根据模板规则完善消息内容');
        }
        $token = $this->getAccessToken();
        if (!$token) {
            Log::info($touser.'token有误'.$token);
        }

        $data = array();
        if (!empty($miniprogram['appid']) && !empty($miniprogram['pagepath'])) {
            $data['miniprogram'] = $miniprogram;
        }
        $data['touser'] = $touser;
        $data['template_id'] = trim($template_id);
        $data['url'] = trim($url);
        $data['topcolor'] = trim($topcolor);
        $data['data'] = $postdata;
        $data = json_encode($data);
        $post_url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}";
        $response = ihttp_request($post_url, $data);
        if (is_error($response)) {
            Log::info($touser."error:访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response['content'], true);
        if (empty($result)) {
            Log::info($touser."error:接口调用失败, 元数据: {$response['meta']}");
        } elseif (!empty($result['errcode'])) {
            Log::info($touser."error: 访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']},信息详情：{$result['errcode']}");
        }

        return true;
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

}
