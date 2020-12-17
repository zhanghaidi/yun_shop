<?php

namespace Yunshop\MinApp\Backend\Controllers;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\modules\wechat\UnifyAccesstoken;
use GuzzleHttp\Client as GuzzleHttp;
use Illuminate\Support\Facades\DB;

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
                if (isset($page['isajax']) && $page['isajax'] == 1) {
                    return $this->errorJson('请选择小程序');
                } else {
                    return $this->message($this->error('请选择小程序'));
                }
            }
            !isset($page['path']) && $page['path'] = '';
            !isset($page['query']) && $page['query'] = '';
            if ($page['minid'] == 2) {
                isset($page['shopapp']['path']) && $page['path'] = $page['shopapp']['path'];
                isset($page['shopapp']['query']) && $page['query'] = $page['shopapp']['query'];
            } else {
                isset($page['mainapp']['path']) && $page['path'] = $page['mainapp']['path'];
                isset($page['mainapp']['query']) && $page['query'] = $page['mainapp']['query'];
            }
            if (empty($page['path'])) {
                if (isset($page['isajax']) && $page['isajax'] == 1) {
                    return $this->errorJson('请选择小程序的页面');
                } else {
                    return $this->message($this->error('请选择小程序的页面'));
                }
            }

            $minAppRs = Setting::get('plugin.min_app');
            if (!isset($minAppRs['key']) || !isset($minAppRs['secret'])) {
                if (isset($page['isajax']) && $page['isajax'] == 1) {
                    return $this->errorJson('小程序APPID和APPSECRET获取错误');
                } else {
                    return $this->message($this->error('小程序APPID和APPSECRET获取错误'));
                }
            }

            if ($page['minid'] == 2) {
                $accessToken = UnifyAccesstoken::getAccessToken($minAppRs['shop_key']);
            } else {
                $accessToken = UnifyAccesstoken::getAccessToken($minAppRs['key']);
            }
            if ($accessToken === false) {
                if (isset($page['isajax']) && $page['isajax'] == 1) {
                    return $this->errorJson('AccessToken获取错误');
                } else {
                    return $this->message($this->error('AccessToken获取错误'));
                }
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
                if (isset($page['isajax']) && $page['isajax'] == 1) {
                    return $this->errorJson('微信小程序搜索服务，请求响应错误');
                } else {
                    return $this->message($this->error('微信小程序搜索服务，请求响应错误'));
                }
            }
            $responseBody = $response->getBody()->getContents();
            $responseBody = json_decode($responseBody, true);
            if (isset($responseBody['errcode']) && $responseBody['errcode'] == 0) {
                if (isset($page['isajax']) && $page['isajax'] == 1) {
                    return $this->successJson('提交成功');
                } else {
                    return $this->message('提交成功', Url::absoluteWeb('plugin.min-app.Backend.Controllers.search.site-search'));
                }
            }

            if (isset($page['isajax']) && $page['isajax'] == 1) {
                return $this->errorJson(isset($responseBody['errmsg']) ? $responseBody['errmsg'] . ' PARAMS:' . json_encode([
                    'minid' => $page['minid'],
                    'token' => $accessToken,
                ]) : '未知错误');
            } else {
                return $this->message($this->error(isset($responseBody['errmsg']) ?? '未知错误'));
            }
        }
        return view('Yunshop\MinApp::search.submit-pages')->render();
    }

    public function oneKey()
    {
        $type = intval(\YunShop::request()->type);

        if (in_array($type, [1, 2, 3, 4, 5])) {
            if ($type == 1) {
                $postRs = DB::table('diagnostic_service_post')->select('id', 'title')
                    ->orderBy('id', 'desc')->limit(10)->get()->toArray();
            } elseif ($type == 2) {
                $articleRs = DB::table('diagnostic_service_article')->select('id', 'title')
                    ->where('status', 1)->orderBy('id', 'desc')->get()->toArray();
            } elseif ($type == 3) {
                $roomRs = DB::table('yz_appletslive_room')->select('id', 'name')
                    ->where([
                        'type' => 1,
                        'delete_time' => 0,
                    ])->whereIn('display_type', [1, 2])
                    ->orderBy('id', 'desc')->get()->toArray();
            } elseif ($type == 4) {
                $goodsRs = DB::table('yz_goods')->select('id', 'title')
                    ->where([
                        'type' => 1,
                        'status' => 1,
                    ])->orderBy('id', 'desc')->get()->toArray();
            } elseif ($type == 5) {
                $acupointRs = DB::table('diagnostic_service_acupoint')->select('id', 'name')
                    ->where('status', 1)->orderBy('id', 'desc')->get()->toArray();
            }
        }

        return view('Yunshop\MinApp::search.one-key', [
            'type' => $type,
            'post' => isset($postRs) ? $postRs : [],
            'article' => isset($articleRs) ? $articleRs : [],
            'room' => isset($roomRs) ? $roomRs : [],
            'goods' => isset($goodsRs) ? $goodsRs : [],
            'acupoint' => isset($acupointRs) ? $acupointRs : [],
        ])->render();
    }
}
