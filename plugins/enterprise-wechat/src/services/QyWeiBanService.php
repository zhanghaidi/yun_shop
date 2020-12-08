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

    /**
     * 获取微伴助手access_token
     * @return mixed
     */
    private function getAccessToken()
    {

        $set = \Setting::get('plugin.enterprise-wechat');
        if (!$set) {
            \Log::info('企业微信配置', $set);
            return false;

        }
        if (!$set['weiban_corpid']) {
            \Log::info('企业微信微伴企业corpid不存在', $set);
            return false;
        }
        if (!$set['weiban_secret']) {
            \Log::info('企业微信微伴Secret不存在', $set);
            return false;
        }


        //企业微伴access_token获取地址
        $url = 'https://open.weibanzhushou.com/open-api/access_token/get';
        $data = array(
            'corp_id' => $set['weiban_corpid'],
            'secret' => $set['weiban_secret']
        );

        $result = Curl::to($url)->withData(json_encode($data))->withContentType('application/json')->asJsonResponse(true)->post();
        if ($result['errcode'] != 0) {
            return false;
            \Log::info('企业微信微伴token获取失败', $result);
        }
        return $result['access_token'];
    }

    //单订单导入
    public static function importOrder($order)
    {

        if (!$order) {
            throw new AppException("订单不能为空");
        }

        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            throw new AppException("access_token调用失败");
        }

        //订单信息同步接口https://open.weibanzhushou.com/open-api/order/import_order
        $url = "https://open.weibanzhushou.com/open-api/order/import_order?access_token={$accessToken}";

        $response = Curl::to($url)->withData(json_encode($order))->withContentType('application/json')->asJsonResponse(true)->post();
        if ($response['errcode'] != 0) {
            throw new AppException("订单上报失败", $response['errmsge']);
            \Log::info('企业微信微伴订单上报失败', $response);
        }

        return $response;

    }

    //获取用户订单
    public static function getOrderList()
    {
        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            throw new AppException("access_token调用失败");
        }
        //获取订单列表数据
        $url = "https://open.weibanzhushou.com/open-api/order/list?access_token={$accessToken}";
        $response = Curl::to($url)->asJsonResponse(true)->get();
        if ($response['errcode'] != 0) {
            throw new AppException("订单获取失败", $response['errmsge']);
            \Log::info('企业微信微伴订单获取失败', $response);
        }

        return $response;
    }

    //移除订单
    public static function removeOrder($order_id)
    {
        if (!$order_id) {
            throw new AppException("订单编号不能为空");
        }
        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            throw new AppException("access_token调用失败");
        }

        //移除订单 https://open.weibanzhushou.com/open-api/order/remove
        $url = "https://open.weibanzhushou.com/open-api/order/remove?access_token={$accessToken}&order_id={$order_id}";
        $response = Curl::to($url)->asJsonResponse(true)->get();
        if ($response['errcode'] != 0) {
            throw new AppException("订单删除失败", $response['errmsge']);
            \Log::info('企业微信微伴订单删除失败', $response);
        }

        return $response;
    }

    //订单信息批量导入
    public static function sendOrderList($orderList)
    {

        if (empty($orderList)) {
            throw new AppException("订单列表不能为空");
        }
        if (count($orderList) > 100) {
            throw new AppException("最多支持传入100个订单");
        }

        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            throw new AppException("access_token调用失败");
        }

        //批量订单信息同步接口https://open.weibanzhushou.com/open-api/order/batch_import_order
        $url = "https://open.weibanzhushou.com/open-api/order/batch_import_order?access_token={$accessToken}";

        $response = Curl::to($url)->withData(json_encode($orderList))->withContentType('application/json')->asJsonResponse(true)->post();
        if ($response['errcode'] != 0) {
            throw new AppException("批量订单上报失败", $response['errmsge']);
            \Log::info('企业微信微伴订单批量上报失败', $response);
        }

        return $response;

    }


}