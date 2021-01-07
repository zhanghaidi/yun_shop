<?php

/**
 * Author: zhd 企业微伴助手基础接口封装
 * Date: 2017/11/24
 * Time: 下午4:06
 */

namespace Yunshop\EnterpriseWechat\services;

use Ixudra\Curl\Facades\Curl;
use app\common\exceptions\AppException;

class QyWeChatService
{
    /**
     * @param $corpid
     * @param $corpsecret
     * 封装企业微信accesstoken
     */
    public function getEnterpriseAccessToken($corpid, $corpsecret) {
        $cachekey = cache_system_key('enterprise_token', array('uniacid' => $this->w['uniacid']));
        $cache = cache_load($cachekey);

        if (!empty($cache) && !empty($cache['token']) && $cache['expire'] > TIMESTAMP) {
            //$account_access_token = $cache;

            return $cache['token'];
        }

        if (empty($corpid) || empty($corpsecret)) {
            return false;
        }
        //https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=ID&corpsecret=SECRET
        //$url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id={$this->account['key']}&client_secret={$this->account['secret']}";

        $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$corpid}&corpsecret={$corpsecret}";
        $content = ihttp_get($url);
        $token = @json_decode($content['content'], true);

        //var_dump($token);die;
        $record = array();
        $record['token'] = $token['access_token'];
        $record['expire'] = TIMESTAMP + $token['expires_in'] - 200;
        //$account_access_token = $record;
        cache_write($cachekey, $record);

        return $record['token'];
    }

}