<?php

/**
 * Author: zhd 企业微伴助手基础接口封装
 * Date: 2017/11/24
 * Time: 下午4:06
 */

namespace Yunshop\EnterpriseWechat\services;

use Ixudra\Curl\Facades\Curl;

class QyWeiBanService
{

    /**
     * 获取access_token
     * @return mixed
     */
    public function getAccessToken()
    {

        $set = \Setting::get('plugin.enterprise-wechat');
        if(!$set){
            \Log::info('企业微信配置',$set);
            return;

        }
        if(!$set['weiban_corpid']){
            \Log::info('企业微信微伴企业corpid不存在',$set);
            return;
        }
        if(!$set['weiban_secret']){
            \Log::info('企业微信微伴Secret不存在',$set);
            return;
        }


        $data = array(
            'corp_id' => $set['weiban_corpid'],
            'secret' => $set['weiban_secret']
        );
        $url =  'https://open.weibanzhushou.com/open-api/access_token/get';

        var_dump($data);die;
        $result = Curl::to($url)->withData(json_encode($data))->asJsonResponse(true)->post();
        //$response = ihttp_request($url, json_encode($data));
        //$result = @json_decode($response['content'], true);

        return $result['access_token'];
    }


}