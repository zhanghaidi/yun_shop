<?php

namespace Yunshop\FaceAnalysis\services;

use app\common\facades\Setting;
use app\common\modules\wechat\UnifyAccesstoken;
use GuzzleHttp\Client as GuzzleHttp;

class AnalysisService
{
    public function detectFace(string $url)
    {
        $minAppRs = Setting::get('plugin.min_app');
        if (!isset($minAppRs['key']) || !isset($minAppRs['secret'])) {
            return ['code' => 1, 'msg' => '小程序APPID和APPSECRET获取错误'];
        }

        $accessToken = UnifyAccesstoken::getAccessToken($minAppRs['key']);
        if ($accessToken === false) {
            return ['code' => 1, 'msg' => 'AccessToken获取错误'];
        }

        $detectFaceUrl = 'https://api.weixin.qq.com/wxa/servicemarket?access_token=';
        $detectFaceUrl .= $accessToken;

        $postBody = [
            'service' => 'wx2d1fd8562c42cebb',
            'api' => 'detectFace',
            'client_msg_id' => md5(uniqid()),
            'data' => [
                'Action' => 'DetectFace',
                'Url' => $url,
                'NeedFaceAttributes' => 1,
                'NeedQualityDetection' => 1,
            ]
        ];
        $requestOption = [
            'body' => json_encode($postBody),
        ];

        $http = new GuzzleHttp();
        $response = $http->request('POST', $detectFaceUrl, $requestOption);
        if ($response->getStatusCode() != 200) {
            return ['code' => 2, 'msg' => '人脸检测与分析服务，请求响应错误'];
        }
        $responseBody = $response->getBody()->getContents();
        $responseBody = json_decode($responseBody, true);
        if (
            !isset($responseBody['errcode']) || $responseBody['errcode'] != 0 ||
            !isset($responseBody['data'])
        ) {
            return ['code' => 2, 'msg' => '人脸检测与分析服务，请求状态错误'];
        }

        $faceRs = json_decode($responseBody['data'], true);
        if (!isset($faceRs['FaceInfos']) || !isset($faceRs['FaceInfos'][0])) {
            return ['code' => 3, 'msg' => '人脸检测与分析服务，数据解析错误'];
        }
        $faceRs = $faceRs['FaceInfos'][0];

        return ['code' => 0, 'data' => $faceRs];
    }
}
