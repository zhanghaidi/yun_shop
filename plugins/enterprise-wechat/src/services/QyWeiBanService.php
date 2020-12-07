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
        if(!$set['corpid']){
            \Log::info('企业微信企业corpid不存在',$set);
            return;
        }
        if(!$set['corpsecret']){
            \Log::info('企业微信企业Secret不存在',$set);
            return;
        }

        $data = array(
            'corp_id' => $set['corpid'],
            'corpsecret' => $set['corpsecret']
        );
        $url =  'https://open.weibanzhushou.com/open-api/access_token/get';

        $response = Curl::to($url)->withData(['data' => json_encode($data)])->asJsonResponse(true)->post();

        return $response['access_token'];
    }


}