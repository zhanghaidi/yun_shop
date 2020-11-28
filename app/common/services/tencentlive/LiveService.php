<?php


namespace app\common\services\tencentlive;

require_once base_path('vendor/tencentcloud/vendor/autoload.php');

use app\common\services\tencentlive\LiveSetService;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Live\V20180801\LiveClient;

class LiveService
{
    const APPNAME = '110648';
    const SECRETID = 'AKIDXpsgGavRJvYy6zyz5vWx1s88wWr2cwvk'; //云 API 密钥 SecretId;
    const SECRETKEY = 'OXR4SciZMyMVO3as1HIAvvhEVVGQL0Vk'; //云 API 密钥 SecretKey;
    const SATREAM_NAME_RRE = 'ajylive';
    public static $StreamState = [0 => 'inactive', 1 => 'active', 2 => 'forbid']; //active：活跃，inactive：非活跃，forbid：禁播。

    /**
     * 获取推流地址
     * 如果不传key和过期时间，将返回不含防盗链的url
     * @param domain 您用来推流的域名
     * @param streamName 您用来区别不同推流地址的唯一流名称
     * @param time 过期时间 sample 2016-11-12 12:00:00
     * @return String url
     */
    public static function getPushUrl($streamName = '', $time = null)
    {
        if ($streamName && $time) {
            $live_setting = LiveSetService::getSetting();
            if (empty($live_setting)) {
                return '';
            }
            $streamName = self::SATREAM_NAME_RRE . $streamName;
            $key = $live_setting['push_key'];
            $domain = $live_setting['push_domain'];
            $txTime = strtoupper(base_convert(strtotime($time), 10, 16));
            $txSecret = md5($key . $streamName . $txTime);
            $ext_str = "?" . http_build_query(array(
                    "txSecret" => $txSecret,
                    "txTime" => $txTime
                ));
            return "rtmp://" . $domain . "/live/" . $streamName . (isset($ext_str) ? $ext_str : "");
        }
        return '';
    }

    public static function getPullUrl($streamName = '')
    {
        if ($streamName) {
            $streamName = self::SATREAM_NAME_RRE . $streamName;
            $domain = LiveSetService::getSetting('pull_domain');
            return "rtmp://" . $domain . "/live/" . $streamName;
        }

        return '';
    }

    /*
     * 获取直播流状态
     */
    public function getDescribeLiveStreamState($streamName = '')
    {
        $params = array(
            "StreamName" => self::SATREAM_NAME_RRE . $streamName,
        );

        $resp = $this->callClient('DescribeLiveStreamState', $params);

        if ($resp) {
            return $resp->StreamState;
        } else {
            return '';
        }
    }

    /*
     * 断开直播流
     */
    public function dropLiveStream($streamName = '')
    {
        $params = array(
            "StreamName" => self::SATREAM_NAME_RRE . $streamName,
        );

        $resp = $this->callClient('DropLiveStream', $params);

        if ($resp) {
            return $resp->RequestId;
        } else {
            return false;
        }
    }

    /*
     * 恢复直播流
     */
    public function resumeLiveStream($streamName = '')
    {
        $params = array(
            "StreamName" => self::SATREAM_NAME_RRE . $streamName,
        );

        $resp = $this->callClient('ResumeLiveStream', $params);

        if ($resp) {
            return $resp->RequestId;
        } else {
            return false;
        }
    }

    /**
     * 请求腾讯云直播接口
     * @param action 操作名称
     * @param params 操作需要参数
     * @return mixed resp
     */
    protected function callClient($action = '', $params = [])
    {
        require_once base_path('vendor/tencentcloud/src/TencentCloud/Live/V20180801/Models/' . ucfirst($action) . 'Request.php');
        $live_setting = LiveSetService::getSetting();

        $req_class = "TencentCloud" . "\\" . ucfirst("live") . "\\" . "V20180801\\Models" . "\\" . ucfirst($action) . "Request";
        $req = new $req_class();

        $common_param = [
            "DomainName" => $live_setting['push_domain'],
            "AppName" => self::APPNAME,
        ];
        $send_params = array_merge($common_param, $params);
        $client = $this->sendRequest($req, $send_params);

        try {
            $resp = call_user_func_array(array($client, $action), array($req));
            return $resp;
        } catch (TencentCloudSDKException $e) {
            \Log::error("LiveService callClient,error:" . $e->getMessage() . ",action:{$action}," . json_encode($send_params));
            return false;
        }

    }

    protected function sendRequest($req, $params)
    {
        $client = $this->getClient();
        $req->fromJsonString(json_encode($params));
        return $client;
    }

    protected function getClient()
    {
        $cred = new Credential(self::SECRETID, self::SECRETKEY);
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("live.tencentcloudapi.com");

        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new LiveClient($cred, "", $clientProfile);
        return $client;
    }

}