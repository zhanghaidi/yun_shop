<?php

/**
 * Author: zhd 企业微伴助手基础接口封装
 * Date: 2017/11/24
 * Time: 下午4:06
 */

namespace Yunshop\EnterpriseWechat\services;

use Ixudra\Curl\Facades\Curl;
use app\common\exceptions\AppException;

class QyWeiBanService
{
    private $order;
    private $accessToken;
    public function __construct(){

    }

    /**
     * 获取微伴助手access_token
     * @return mixed
     */
    private function getAccessToken()
    {

        $set = \Setting::get('plugin.enterprise-wechat');
        if(!$set){
            \Log::info('企业微信配置',$set);
            return false;

        }
        if(!$set['weiban_corpid']){
            \Log::info('企业微信微伴企业corpid不存在',$set);
            return false;
        }
        if(!$set['weiban_secret']){
            \Log::info('企业微信微伴Secret不存在',$set);
            return false;
        }


        //企业微伴access_token获取地址
        $url =  'https://open.weibanzhushou.com/open-api/access_token/get';
        $data = array(
            'corp_id' => $set['weiban_corpid'],
            'secret' => $set['weiban_secret']
        );

        $result = Curl::to($url)->withData(json_encode($data))->withContentType('application/json')->asJsonResponse(true)->post();
        if($result['errcode']!= 0){
            return false;
            \Log::info('企业微信微伴token获取失败',$result);
        }
        return $result['access_token'];
    }

    //订单同步
    public static function importOrder($order){

        if(!$order){
            throw new AppException("订单不能为空");
        }

        //订单信息同步接口https://open.weibanzhushou.com/open-api/order/import_order
        $accessToken = self::getAccessToken();
        if(!$accessToken){
            throw new AppException("access_token调用失败");
        }

        $url = "https://open.weibanzhushou.com/open-api/order/import_order?access_token={$accessToken}";

        $response = Curl::to($url)->withData(json_encode($order))->withContentType('application/json')->asJsonResponse(true)->post();
        if($response['errcode']!= 0){
            throw new AppException("订单上报失败", $response['errmsge']);
            \Log::info('企业微信微伴订单上报失败',$response);
        }

        return $response;

    }




}