<?php


namespace app\common\services\tencentlive;

require_once base_path('vendor/tencentcloud/vendor/autoload.php');

use app\common\services\tencentlive\LiveSetService;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Live\V20180801\LiveClient;
use TencentCloud\Live\V20180801\Models\DescribeLiveStreamStateRequest;

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

    public function getDescribeLiveStreamState($streamName = '')
    {
        $live_setting = LiveSetService::getSetting();

        $req = new DescribeLiveStreamStateRequest();

        $params = array(
            "StreamName" => self::SATREAM_NAME_RRE . $streamName,
            "DomainName" => $live_setting['push_domain'],
            "AppName" => self::APPNAME,
        );

        $client = $this->sendRequest($req, $params);

        if ($client) {
            $resp = $client->DescribeLiveStreamState($req);
            return $resp->StreamState;
        } else {
            return '';
        }
    }

    protected function sendRequest($req, $params)
    {
        try {
            $client = $this->getClient();
            $req->fromJsonString(json_encode($params));
            return $client;
        } catch (TencentCloudSDKException $e) {
            \Log::error("LiveService sendRequest,error:" . $e->getMessage());
            return '';
        }
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