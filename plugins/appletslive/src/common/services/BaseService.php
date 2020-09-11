<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-02-28
 * Time: 12:45
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Appletslive\common\services;

use Illuminate\Support\Facades\DB;
use app\common\facades\Setting;
use app\common\exceptions\AppException;
use app\common\services\qcloud\Api;

class BaseService
{
    protected $appId;
    protected $secret;

    public function __construct()
    {
        $set = Setting::get('plugin.appletslive');
        if (empty($set)) {
            $wxapp_account = DB::table('account_wxapp')
                ->select('key', 'secret')
                ->where('uniacid', 45)
                ->first();
            $this->appId = $wxapp_account['key'];
            $this->secret = $wxapp_account['secret'];
        } else {
            $this->appId = $set['appId'];
            $this->secret = $set['secret'];
        }
    }

    public function downloadImgFromCos($filepath)
    {
        global $_W;

        $fileinfo = explode('/', $filepath);
        $filename = $fileinfo[count($fileinfo) - 1];

        // 获取云存储配置信息
        $uni_setting = app('WqUniSetting')->get()->toArray();
        if (!empty($uni_setting['remote']) && iunserializer($uni_setting['remote'])['type'] != 0) {
            $setting['remote'] = iunserializer($uni_setting['remote']);
            $remote = $setting['remote']['cos'];
        } else {
            $remote = $_W['setting']['remote']['cos'];
        }

        try {

            $uniqid = uniqid();
            $localpath = ATTACHMENT_ROOT . 'image/' . $uniqid . $filename;

            $config = [
                'app_id' => $remote['appid'],
                'secret_id' => $remote['secretid'],
                'secret_key' => $remote['secretkey'],
                'region' => $remote['local'],
                'timeout' => 60,
            ];
            $cosApi = new Api($config);
            $ret = $cosApi->download($remote['bucket'], $filepath, $localpath);

            $message = $localpath;
            if ($ret['code'] != 0) {
                switch ($ret['code']) {
                    case -62:
                        $message = '输入的appid有误';
                        break;
                    case -79:
                        $message = '输入的SecretID有误';
                        break;
                    case -97:
                        $message = '输入的SecretKEY有误';
                        break;
                    case -166:
                        $message = '输入的bucket有误';
                        break;
                }
            }

            return ['result_code' => $ret['code'], 'data' => $message, 'ret' => $ret];

        } catch (\Exception $e) {
            return ['result_code' => 1, 'data' => $e->getMessage()];
        }
    }

    public function uploadMedia($mediaPath, $type = 'image')
    {
        $token = $this->getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$token}&type={$type}";
        $data = ['media' => new \CURLFile($mediaPath)];
        $result = self::curlRequest($url, $data);
        unlink($mediaPath);
        return json_decode($result, true);
    }

    public function createRoom($data)
    {
        $token = $this->getToken();
        $url = "https://api.weixin.qq.com/wxaapi/broadcast/room/create?access_token={$token}";
        $headers = [
            "Content-Type: application/json",
            "Accept: application/json",
        ];
        $result = self::curlRequest($url, json_encode($data), $headers);
        return json_decode($result, true);
    }

    public function getRooms($start = 0, $limit = 100)
    {
        $token = $this->getToken();
        $url = 'https://api.weixin.qq.com/wxa/business/getliveinfo?access_token=' . $token;

        $post_data = [
            'start' => $start,
            'limit' => $limit,
        ];

        $result = self::curlRequest($url, json_encode($post_data));
        return json_decode($result, true);
    }

    public function getReplays($rid)
    {
        $token = $this->getToken();
        $url = 'https://api.weixin.qq.com/wxa/business/getliveinfo?access_token=' . $token;

        $post_data = [
            'action' => 'get_replay',
            'room_id' => $rid,
            'start' => 0,
            'limit' => 100,
        ];

        $result = self::curlRequest($url, json_encode($post_data), []);

        return json_decode($result, true);
    }

    public function getGoods($status = 0, $offset = 0, $limit = 100)
    {
        $token = $this->getToken();
        $url = "https://api.weixin.qq.com/wxaapi/broadcast/goods/getapproved?access_token={$token}&offset={$offset}&limit={$limit}&status={$status}";
        $result = self::curlRequest($url);
        return json_decode($result, true);
    }

    public function addGoods($data)
    {
        $token = $this->getToken();
        $url = "https://api.weixin.qq.com/wxaapi/broadcast/goods/add?access_token={$token}";
        $headers = [
            "Content-Type: application/json",
            "Accept: application/json",
        ];
        $result = self::curlRequest($url, json_encode($data), $headers);
        return json_decode($result, true);
    }

    public function getAuditStatus($goods_ids = [])
    {
        if (empty($goods_ids)) {
            return ['errcode' => 0, 'goods' => []];
        }

        $token = $this->getToken();
        $url = "https://api.weixin.qq.com/wxa/business/getgoodswarehouse?access_token={$token}";

        $post_data = ['goods_ids' => $goods_ids];
        $result = self::curlRequest($url, json_encode($post_data));

        return json_decode($result, true);
    }

    public function resetAudit($goods_id)
    {
        $token = $this->getToken();
        $url = "https://api.weixin.qq.com/wxaapi/broadcast/goods/resetaudit?access_token={$token}";

        $post_data = ['goodsId' => $goods_id];
        $result = self::curlRequest($url, json_encode($post_data));

        return json_decode($result, true);
    }

    public function audit($goods_id)
    {
        $token = $this->getToken();
        $url = "https://api.weixin.qq.com/wxaapi/broadcast/goods/audit?access_token={$token}";

        $post_data = ['goodsId' => $goods_id];
        $result = self::curlRequest($url, json_encode($post_data));

        return json_decode($result, true);
    }

    public function msgSecCheck($content)
    {
        // 文本检测
        $token = $this->getToken();
        $url = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token=' . $token;
        $post_data = ['content' => $content];
        $result = json_decode(self::curlRequest($url, json_encode($post_data), []), true);
        if (!$result || !is_array($result) || $result['errcode'] != 0) {
            return $result;
        }
        return true;
    }

    public function textCheck($content)
    {
        $filterStrs = DB::table('diagnostic_service_sns_filter')->get()->toArray();
        $keywords = array();
        foreach ($filterStrs as $k => $v) {
            $filterStrs[$k]['content'] = explode('-', $v['content']);
            if (empty($keywords)) {
                $keywords = $filterStrs[$k]['content'];
            } else {
                $keywords = array_merge($keywords, $filterStrs[$k]['content']);
            }
        }
        $keywords = array_unique($keywords);
        $lexicon = array_combine($keywords, array_fill(0, count($keywords), '*')); // 换字符
        $str = strtr($content, $lexicon);                 // 匹配替换
        return $str;
    }

    public function getToken()
    {
        if (empty($this->appId) || empty($this->secret)) {
            throw new AppException('请配置appId和secret');
        }
        $result = self::curlRequest($this->requestUrl($this->appId, $this->secret), '', []);
        $decode = json_decode($result, true);
        if ($decode['errcode'] != 0) {
            throw new AppException('appId或者secret错误' . $decode['errmsg']);
        }
        return $decode['access_token'];
    }

    private static function curlRequest($url, $post_data = [], $headers = [])
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (!empty($post_data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    protected function requestUrl($appId, $secret)
    {
        return 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $secret;
    }
}
