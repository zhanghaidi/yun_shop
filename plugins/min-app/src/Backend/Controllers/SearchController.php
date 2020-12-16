<?php

namespace Yunshop\MinApp\Backend\Controllers;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\modules\wechat\UnifyAccesstoken;
use GuzzleHttp\Client as GuzzleHttp;

class SearchController extends BaseController
{
    public function siteSearch()
    {
        $search = \YunShop::request()->search;
        !isset($search['minid']) && $search['minid'] = 1;

        $minAppRs = Setting::get('plugin.min_app');
        if (!isset($minAppRs['key']) || !isset($minAppRs['secret'])) {
            return $this->message($this->error('小程序APPID和APPSECRET获取错误'));
        }

        if ($search['minid'] == 2) {
            $accessToken = UnifyAccesstoken::getAccessToken($minAppRs['shop_key']);
        } else {
            $accessToken = UnifyAccesstoken::getAccessToken($minAppRs['key']);
        }
        if ($accessToken === false) {
            return $this->message($this->error('AccessToken获取错误'));
        }

        $siteSearchUrl = 'https://api.weixin.qq.com/wxa/sitesearch?access_token=';
        $siteSearchUrl .= $accessToken;

        $requestBody = [
            'keyword' => isset($search['keyword']) ? $search['keyword'] : '养居益',
            'next_page_info' => '',
        ];

        $http = new GuzzleHttp();
        $response = $http->request('POST', $siteSearchUrl, ['body' => json_encode($requestBody)]);
        if ($response->getStatusCode() != 200) {
            return $this->message($this->error('微信小程序搜索服务，请求响应错误'));
        }
        $responseBody = $response->getBody()->getContents();
        $responseBody = json_decode($responseBody, true);

        return view('Yunshop\MinApp::search.site-search', [
            'search' => $search,
            'response' => $responseBody,
        ])->render();
    }

    public function submitPages()
    {
        $page = \YunShop::request()->page;
        if ($page) {
            if (!isset($page['minid'])) {
                return $this->message($this->error('请选择小程序'));
            }
            $page['path'] = '';
            $page['query'] = '';
            if ($page['minid'] == 2) {
                $page['path'] = $page['shopapp']['path'];
                $page['query'] = $page['shopapp']['query'];
            } else {
                $page['path'] = $page['mainapp']['path'];
                $page['query'] = $page['mainapp']['query'];
            }
            if (empty($page['path'])) {
                return $this->message($this->error('请选择小程序的页面'));
            }

            $minAppRs = Setting::get('plugin.min_app');
            if (!isset($minAppRs['key']) || !isset($minAppRs['secret'])) {
                return $this->message($this->error('小程序APPID和APPSECRET获取错误'));
            }

            if ($page['minid'] == 2) {
                $accessToken = UnifyAccesstoken::getAccessToken($minAppRs['shop_key']);
            } else {
                $accessToken = UnifyAccesstoken::getAccessToken($minAppRs['key']);
            }
            if ($accessToken === false) {
                return $this->message($this->error('AccessToken获取错误'));
            }

            $submitPagesUrl = 'https://api.weixin.qq.com/wxa/search/wxaapi_submitpages?access_token=';
            $submitPagesUrl .= $accessToken;

            $requestBody = [
                'access_token' => $accessToken,
                'pages' => [
                    [
                        'path' => $page['path'],
                        'query' => $page['query'],
                    ],
                ],
            ];

            $http = new GuzzleHttp();
            $response = $http->request('POST', $submitPagesUrl, ['body' => json_encode($requestBody)]);
            if ($response->getStatusCode() != 200) {
                return $this->message($this->error('微信小程序搜索服务，请求响应错误'));
            }
            $responseBody = $response->getBody()->getContents();
            $responseBody = json_decode($responseBody, true);
            if (isset($responseBody['errcode']) && $responseBody['errcode'] == 0) {
                return $this->message('提交成功', Url::absoluteWeb('plugin.min-app.Backend.Controllers.search.site-search'));
            }

            return $this->message($this->error(isset($responseBody['errmsg']) ?? '未知错误'));
        }
        return view('Yunshop\MinApp::search.submit-pages')->render();
    }
}
