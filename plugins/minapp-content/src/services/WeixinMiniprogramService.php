<?php

namespace Yunshop\MinappContent\services;

use app\common\facades\Setting;
use app\common\modules\wechat\UnifyAccesstoken;
use Exception;
use GuzzleHttp\Client as GuzzleHttp;

class WeixinMiniprogramService
{
    /**
     * 获取小程序码，适用于需要的码数量极多的业务场景。通过该接口生成的小程序码，永久有效，数量暂无限制。
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html
     */
    public static function getCodeUnlimit(string $scene, string $page = '', int $width = 430, array $option = [])
    {
        if (!preg_match('/[0-9a-zA-Z\!\#\$\&\'\(\)\*\+\,\/\:\;\=\?\@\-\.\_\~]{1,32}/', $scene)) {
            throw new Exception('场景值不合法');
        }
        if ($width < 280 || $width > 1280) {
            throw new Exception('二维码的宽度不合法');
        }

        $minAppRs = Setting::get('plugin.min_app');
        if (!isset($minAppRs['key']) || !isset($minAppRs['secret'])) {
            throw new Exception('小程序APPID和APPSECRET获取错误');
        }

        $accessToken = UnifyAccesstoken::getAccessToken($minAppRs['key']);
        if ($accessToken === false) {
            throw new Exception('AccessToken获取错误');
        }

        $postBody = [
            'scene' => $scene,
            'width' => $width,
        ];
        if (isset($page[0])) {
            $postBody['page'] = $page;
        }
        if (isset($option['auto_color']) && $option['auto_color'] == true) {
            $postBody['auto_color'] = true;
        }
        if (isset($option['line_color']['r']) && isset($option['line_color']['g']) &&
            isset($option['line_color']['b'])
        ) {
            $postBody['line_color'] = [
                'r' => $option['line_color']['r'],
                'g' => $option['line_color']['g'],
                'b' => $option['line_color']['b'],
            ];
            $postBody['auto_color'] = false;
        }
        if (isset($option['is_hyaline']) && $option['is_hyaline'] == true) {
            $postBody['is_hyaline'] = true;
        }
        $requestOption = [
            'body' => json_encode($postBody),
        ];

        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $accessToken;
        $http = new GuzzleHttp();
        $response = $http->request('POST', $url, $requestOption);
        if ($response->getStatusCode() != 200) {
            throw new Exception('访问公众平台接口失败');
        }
        $responseBody = $response->getBody()->getContents();
        return $responseBody;
    }
}
