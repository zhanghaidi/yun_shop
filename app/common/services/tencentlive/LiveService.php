<?php


namespace app\common\services\tencentlive;

require_once base_path('vendor/tencentcloud/vendor/autoload.php');

use app\common\services\tencentlive\LiveSetService;
use app\common\models\live\CloudLiveRoom;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Live\V20180801\LiveClient;

class LiveService
{
    public static $StreamState = [0 => 'inactive', 1 => 'active', 2 => 'forbid']; //active：活跃，inactive：非活跃，forbid：禁播。

    /**
     * 获取推流地址
     * 如果不传key和过期时间，将返回不含防盗链的url
     * @param domain 您用来推流的域名
     * @param streamName 您用来区别不同推流地址的唯一流名称
     * @param time 过期时间 sample 2016-11-12 12:00:00
     * @return String url
     */
    public static function getPushUrl($room_id = '', $time = null)
    {
        if ($room_id && $time) {
            $live_setting = LiveSetService::getSetting();
            if (!empty($live_setting)) {
                $streamName = $live_setting['stream_name_pre'] . $room_id;
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
        }
        return '';
    }

    public static function getPullUrl($room_id = '')
    {
        if ($room_id) {
            $streamName = LiveSetService::getSetting('stream_name_pre') . $room_id;
            $domain = LiveSetService::getSetting('pull_domain');
            return "rtmp://" . $domain . "/live/" . $streamName;
        }

        return '';
    }

    public function getLiveList($page_size,$status = 0){
        $where = [
            ['end_time','>=',time()]
        ];

        return CloudLiveRoom::uniacid()->select('id','name','cover_img','live_status','start_time','end_time','anchor_name','share_img','pull_url','group_id','sort')->where($where)->orderby('sort','desc')->where(function ($query) use ($status){
            if($status > 0){
                $query->where('live_status',$status);
            }else{
                $query->whereIn('live_status',[101,102,105]);
            }
        })->paginate($page_size);
    }

    /*
     * 获取直播流状态
     */
    public function getDescribeLiveStreamState($streamName = '')
    {
        $params = array(
            "StreamName" => $streamName,
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
            "StreamName" => $streamName,
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
            "StreamName" => $streamName,
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
            "AppName" => $live_setting['app_name'],
        ];
        $send_params = array_merge($common_param, $params);

        $cred = new Credential($live_setting['secret_id'], $live_setting['secret_key']);

        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("live.tencentcloudapi.com");

        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);

        $client = new LiveClient($cred, "", $clientProfile);

        $req->fromJsonString(json_encode($send_params));

        try {
            $resp = call_user_func_array(array($client, $action), array($req));
            return $resp;
        } catch (TencentCloudSDKException $e) {
            \Log::error("LiveService callClient,error:" . $e->getMessage() . ",action:{$action}," . json_encode($send_params));
            return false;
        }

    }

}