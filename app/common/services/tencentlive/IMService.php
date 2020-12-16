<?php

namespace app\common\services\tencentlive;

use app\common\facades\Setting;
use app\common\services\tencentlive\TLSSigAPIv2;
use app\common\services\tencentlive\LiveSetService;

class IMService
{
    const BASE_URL = 'https://console.tim.qq.com/v4/';

    public function getGroupList()
    {
        $url = self::getRequestUrl('group_open_http_svc', 'get_appid_group_list');
        $data = json_encode([
            "Limit" => 1000,
            "Next" => 0
        ]);
        return json_decode($this->curl_post($url, $data), true);
    }

    public function getGroupMsg($group_id)
    {
        $url = self::getRequestUrl('group_open_http_svc', 'group_msg_get_simple');
        $data = json_encode([
            "GroupId" => $group_id,
            "ReqMsgNumber" => 100
        ]);
        return json_decode($this->curl_post($url, $data), true);
    }


    //群组管理：在群组中发送普通消息
    public function sendGroupMsg($group_id, $text)
    {
        $url = self::getRequestUrl('group_open_http_svc', 'send_group_msg');

        $data = json_encode([
            "GroupId" => $group_id,
            "Random" => mt_rand(10000, 100000),
            "MsgBody" => [
                [
                    "MsgType" => "TIMTextElem",
                    "MsgContent" => [
                        "Text" => $text
                    ]
                ]
            ]
        ]);
        return json_decode($this->curl_post($url, $data), true);
    }

    public function sendSysGroupMsg($group_id, $text)
    {
        $url = self::getRequestUrl('group_open_http_svc', 'send_group_system_notification');
        $data = json_encode([
            "GroupId" => $group_id,
            "Content" => $text,
        ]);
        return json_decode($this->curl_post($url, $data), true);
    }

    //创建群组API
    public function createGroup($room_id, $name)
    {
        $url = self::getRequestUrl('group_open_http_svc', 'create_group');
        $data = json_encode([
            "Type" => 'AVChatRoom',
            "GroupId" => LiveSetService::getIMSetting('group_pre') . '-' . $room_id,
            "Name" => $name
        ]);
        return json_decode($this->curl_post($url, $data));
    }

    public function getGroupInfo($group_id)
    {
        $url = self::getRequestUrl('group_open_http_svc', 'get_group_info');
        $data = json_encode(['GroupIdList' => [$group_id]]);
        return json_decode($this->curl_post($url, $data), true);
    }

    public function getOnlineMemberNum($group_id)
    {
        if (!empty($group_id)) {
            $url = self::getRequestUrl('group_open_http_svc', 'get_online_member_num');
            $data = json_encode(['GroupId' => $group_id]);
            $resp = json_decode($this->curl_post($url, $data));
            if ($resp->ErrorCode == 0) {
                return $resp->OnlineMemberNum;
            }
        }
        return 0;
    }

    public function forbidSendMsg($group_id,$userId,$time = 0)
    {
        if (!empty($userId)) {
            $url = self::getRequestUrl('group_open_http_svc', 'forbid_send_msg');
            $data = json_encode(['GroupId'=>$group_id,'Members_Account' => [$userId],'ShutUpTime'=>$time]);
            $resp = json_decode($this->curl_post($url, $data));
            if ($resp->ErrorCode == 0) {
                return true;
            }
        }
        return false;
    }

    protected static function getRequestUrl($servicename, $command)
    {
        $usersig = self::getSign();
        return self::BASE_URL . "{$servicename}/{$command}?sdkappid=" . LiveSetService::getIMSetting('sdk_appid') . '&Identifier=' . LiveSetService::getIMSetting('identifier') . '&usersig=' . $usersig . '&random=' . mt_rand(1000, 1000000) . '&contenttype=json';
    }

    public static function getSign($userId = '')
    {
        $TLSSigAPIv2 = new TLSSigAPIv2(LiveSetService::getIMSetting('sdk_appid'), LiveSetService::getIMSetting('app_key'));
        if (empty($userId))
            $userId = LiveSetService::getIMSetting('identifier');
        return $TLSSigAPIv2->genUserSig($userId);
    }

    public function curl_post($url = '', $postdata = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}