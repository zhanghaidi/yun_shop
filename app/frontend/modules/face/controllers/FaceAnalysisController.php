<?php

namespace app\frontend\modules\face\controllers;

use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\services\api\WechatApi;
use app\common\modules\wechat\UnifyAccesstoken;
use GuzzleHttp\Client as GuzzleHttp;

class FaceAnalysisController extends ApiController
{
    protected $publicAction = ['demo'];
    protected $ignoreAction = ['demo'];

    public function demo($request)
    {
        $url = trim($request->input('url', ''));
        if (!isset($url[1])) {
            return $this->errorJson('参数错误');
        }

        $faceRs = $this->detectFace('owVKQwRiOFsxd3JmwQ9PJTJ1fRhU', $url);
        if (!isset($faceRs['code']) || $faceRs['code'] != 0 || !isset($faceRs['data'])) {
            return $this->errorJson(isset($faceRs['msg']) ? $faceRs['msg'] : '未知错误');
        }
        $faceRs = json_decode($faceRs['data'], true);

        // $faceRs = '{"ImageWidth":800,"ImageHeight":800,"FaceInfos":[{"X":223,"Y":126,"Width":289,"Height":388,"FaceAttributesInfo":{"Gender":99,"Age":27,"Expression":14,"Hat":false,"Glass":true,"Mask":false,"Hair":{"Length":1,"Bang":1,"Color":0},"Pitch":6,"Yaw":0,"Roll":-2,"Beauty":77,"EyeOpen":true},"FaceQualityInfo":{"Score":100,"Sharpness":67,"Brightness":62,"Completeness":{"Eyebrow":99,"Eye":99,"Nose":99,"Cheek":99,"Mouth":99,"Chin":99}}}],"RequestId":"18a9de42-b210-4111-8857-3802ea4abe34"}';
        // $faceRs = json_decode($faceRs, true);
        if (!isset($faceRs['FaceInfos']) || !isset($faceRs['FaceInfos'][0])) {
            return $this->errorJson('人脸检测分析数据获取错误');
        }
        $faceRs = $faceRs['FaceInfos'][0];
        if (!isset($faceRs['X']) || !isset($faceRs['FaceAttributesInfo'])) {
            return $this->errorJson('人脸检测分析数据解析错误');
        }
        return $this->successJson('分析成功', [
            'attribute' => $faceRs['FaceAttributesInfo'],
            'quality' => $faceRs['FaceQualityInfo'],
        ]);
    }

    private function detectFace($openid, $url)
    {
        $settingRs = Setting::get('plugin.min_app');
        if (!isset($settingRs['key']) || !isset($settingRs['secret'])) {
            return ['code' => 1, 'msg' => '小程序APPID和APPSECRET获取错误'];
        }

        $accessToken = UnifyAccesstoken::getAccessToken($settingRs['key']);
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
            // 'headers' => [
            //     'Wx-ServiceMarket-AppID' => $settingRs['key'],
            //     'Wx-ServiceMarket-OpenID' => $openid,
            //     'Wx-ServiceMarket-ClientIP' => '125.40.25.39'
            // ],
            'body' => json_encode($postBody),
        ];

        $http = new GuzzleHttp();
        $response = $http->request('POST', $detectFaceUrl, $requestOption);
        if ($response->getStatusCode() != 200) {
            return ['code' => 1, 'msg' => '接口请求响应错误'];
        }
        $responseBody = $response->getBody()->getContents();
        $responseBody = json_decode($responseBody, true);
        if (
            !isset($responseBody['errcode']) || $responseBody['errcode'] != 0 ||
            !isset($responseBody['data'])
        ) {
            return ['code' => 1, 'msg' => '接口请求状态错误'];
        }

        return ['code' => 0, 'data' => $responseBody['data']];
    }
}
