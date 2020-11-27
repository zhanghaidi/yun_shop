<?php

namespace app\common\services\tencentlive;

use app\common\facades\Setting;
use app\common\services\tencentlive\TLSSigAPIv2;

class IMService
{

    const BASE_URL = 'https://console.tim.qq.com/v4/';
    const SDK_APPID = 1400453551;
    const APP_KEY = '464deac44508f37a57cd4cf309720826f8fddf2a55413ec499a1fe3047c4ca10';
    const IDENTIFIER = 'ajy';
    const GROUP_PREFIX = 'ajygroup-';

    public function getGroupList()
    {
        $url = self::getRequestUrl('group_open_http_svc','get_appid_group_list');
        $data = json_encode([
              "Limit"=> 1000,
              "Next"=> 0
        ]);
        return json_decode($this->curl_post($url,$data),true);
    }

    public function getGroupMsg($group_id)
    {
        $url = self::getRequestUrl('group_open_http_svc','group_msg_get_simple');
        $data = json_encode([
              "GroupId"=> $group_id,
              "ReqMsgNumber"=> 100
        ]);
        return json_decode($this->curl_post($url,$data),true);
    }

    public function createGroup($group_id,$name)
    {
        $url = self::getRequestUrl('group_open_http_svc','create_group');
        $data = json_encode([
              "Type"=> 'AVChatRoom',
              "GroupId"=> self::GROUP_PREFIX . $group_id,
              "Name"=> $name
        ]);
        return json_decode($this->curl_post($url,$data));
    }

    public function getGroupInfo($group_id)
    {
        $url = self::getRequestUrl('group_open_http_svc','get_group_info');
        $data = json_encode(['a'=>1,'b'=>2]);
        return json_decode($this->curl_post($url,$data),true);
    }

    public function getOnlineMemberNum($group_id)
    {
        if(!empty($group_id)){
            $url = self::getRequestUrl('group_open_http_svc','get_online_member_num');
            $data = json_encode(['GroupId'=>$group_id]);
            $resp = json_decode($this->curl_post($url,$data));
            if($resp->ErrorCode == 0){
                return $resp->OnlineMemberNum;
            }
        }
        return 0;
    }


    protected static function getRequestUrl($servicename,$command){
        $usersig = self::getSign();
        return self::BASE_URL . "{$servicename}/{$command}?sdkappid=" . self::SDK_APPID . '&Identifier=' . self::IDENTIFIER . '&usersig=' . $usersig . '&random=' . mt_rand(1000,1000000) . '&contenttype=json';
    }

    public static function getSign($userId = ''){
        $TLSSigAPIv2 = new TLSSigAPIv2(self::SDK_APPID,self::APP_KEY);
        if(empty($userId))
            $userId = self::IDENTIFIER;
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