<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/12 上午9:26
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Poster\services;


use app\common\helpers\ImageHelper;
use app\common\services\Utils;
use GuzzleHttp\Client;
use app\common\models\Member;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use Yunshop\Poster\Jobs\MemberPosterCreateJob;
use Yunshop\Poster\models\MemberPoster;
use Yunshop\Poster\models\Qrcode;
use Yunshop\Poster\models\Poster;
use Yunshop\Poster\models\PosterQrcode;

use Illuminate\Support\Facades\Log;

use EasyWeChat\Foundation\Application;

class CreatePosterService
{
    use DispatchesJobs;
    const WE_CHAT_SHOW_QR_CODE_URL = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';
    /**
     * @var Member
     */
    private $memberModel;

    private $posterModel;

    private $type;

    public function __construct($memberId, $postId, $type = 1)
    {
        $this->getMemberModel($memberId);
        $this->getPosterModel($postId);
        $this->type = $type == 2?2:1; //判断是是公众号还是小程序
    }


    public function getMemberPosterPath()
    {
        // 已存在
        $full_path = $this->getPosterFileFullPath();

        if (is_file($full_path)) {
            return $full_path;
        }

        $memberPoster = MemberPoster::where('uid', $this->memberModel->uid)->first();
        if (isset($memberPoster) && $memberPoster->status == 1) {
            if(DB::table('jobs')->where('id',$memberPoster->job_id)->count()){
                // 队列生成中
                return '';
            }
        }

        // 未生成
        $jobId = $this->dispatch(new MemberPosterCreateJob($this->memberModel->uid, \YunShop::app()->uniacid, request()->getSchemeAndHttpHost(), $this->type));

        if (!isset($memberPoster)) {
            MemberPoster::create(['uid' => $this->memberModel->uid, 'status' => 1, 'job_id' => $jobId]);
        } else {
            $memberPoster->status = 1;
            $memberPoster->job_id = $jobId;
            $memberPoster->save();
        }

        return '';
    }


    /**
     * 推广海报储存路径 绝对路径
     * @return string
     */
    public function getPosterPath()
    {
        $path = storage_path('app/public/poster/' . \YunShop::app()->uniacid) . "/";

        Utils::mkdirs($path);

        return $path;
    }


    /**
     * 推广海报文件储存全路径 绝对路径
     * @return string
     */
    private function getPosterFileFullPath()
    {
        return $this->getPosterPath() . $this->getPosterFileName();
    }

    public function getPosterFileRealName()
    {
        return $this->getPosterPath() . $this->getPosterFileName();
    }

    /**
     * 获取 会员 ID + 海报内容 生成的海报名称
     * @return string
     */
    private function getPosterFileName()
    {
        $file_name = md5(json_encode([
            'memberId' => $this->memberModel->uid,
            'posterId' => $this->posterModel->id,
            'uniacid' => $this->posterModel->uniacid,
            'background' => $this->posterModel->background,
            'style_data' => $this->posterModel->style_data,
        ]));
        return $file_name.'_'.$this->type.'.png';
    }


    /**
     * 会员指定海报下生成海报记录实例
     * @return mixed
     */
    private function getExistedPoster()
    {
        return PosterQrcode::getUserExistedPoster($this->memberModel->uid, $this->posterModel->id);
    }


    /**
     * 通过 $posterId 获取 model 实例
     * @param $postId
     * @return mixed
     */
    private function getPosterModel($posterId)
    {
        return $this->posterModel = Poster::uniacid()->where('id', $posterId)->with('supplement')->first();
    }


    /**
     * 通过 $memberId 获取 model 实例
     * @param $memberId
     * @return mixed
     */
    private function getMemberModel($memberId)
    {
        return $this->memberModel = Member::uniacid()->select('uid', 'avatar', 'nickname')->ofUid($memberId)->with('yzMember')->first();
    }


    /**
     * 生成海报，返回海报绝对路径
     * @return string
     */
    public function createMemberPoster($host)
    {
        set_time_limit(0);
        @ini_set('memory_limit', '256M');

        $target = imagecreatetruecolor(640, 1008);
        $white = imagecolorallocate($target, 255, 255, 255);
        //设置白色背景色
        imagefill($target, 0, 0, $white);

        $short_background = yz_tomedia($this->posterModel->short_background, false, null, $host);
        /*if (config('APP_Framework') == 'platform') {
            $short_background = $host . '/static/upload/' . $this->posterModel->short_background;
        } else {
            $short_background = $host . '/attachment/' . $this->posterModel->short_background;
        }*/
        $short_background = $this->posterModel->short_background ? $short_background : $this->posterModel->background;
        \Log::error('$short_background', $short_background);

        $size = getimagesize($short_background);
        $width = $size[0] ?: 640;
        $height = $size[1] ?: 1008;
        $bg = imagecreatefromstring(\Curl::to($short_background)->get());
        \Log::error('$bg', $bg);

        imagecopy($target, $bg, 0, 0, 0, 0, $width, $height);
        imagedestroy($bg);

        $params = json_decode(str_replace('&quot;', '\'', $this->posterModel->style_data), true);
        //dd($params);
        foreach ($params as $item) {
            $item = $this->getRealParams($item);

            switch ($item['type']) {
                case 'head':
                    $avatar = ImageHelper::fix_wechatAvatar($this->memberModel->avatar);
                    if ($avatar && preg_match('/^[http|https]/', $avatar)) {
                        $target = $this->mergeHeadImage($target, $item);
                    }
                    break;
                case 'img':
                    if ($item['src']) {
                        $target = $this->mergeOtherImage($target, $item);
                    }
                    break;
                case 'qr':
                    $target = $this->mergeQrImage($target, $item);
                    break;
                case 'qr_shop':
                    $target = $this->mergeQr_shopImage($target, $item, $host);
                    break;
                case 'qr_app_share':
                    $target = $this->mergeAppShareImage($target, $item, $host);
                    break;
                case 'nickname':
                    if ($this->memberModel->nickname) {
                        $target = $this->mergeText($target, $item, $this->memberModel->nickname);
                    }
                    break;
            }
        }

        imagepng($target, $this->getPosterFileFullPath());
        imagedestroy($target);

        return $this->getPosterFileFullPath();
    }


    /**
     * 添加会员头像
     * @param $target
     * @param $item
     * @return mixed
     */
    private function mergeHeadImage($target, $item)
    {
        $client = new Client;
        $res = $client->request('GET', $this->memberModel->avatar);

        $avatarImg = imagecreatefromstring($res->getBody());
        return $this->mergeImage($target, $item, $avatarImg);
    }


    /**
     * 添加其他图片
     * @param $target
     * @param $item
     * @return mixed
     */
    private function mergeOtherImage($target, $item)
    {
        $srcImg = imagecreatefromstring(file_get_contents($item['src']));
        return $this->mergeImage($target, $item, $srcImg);
    }


    /**
     * 添加二维码
     * @param $target
     * @param $item
     * @return mixed
     */
    private function mergeQrImage($target, $item)
    {
        $client = new Client;
//        $res = $client->request('GET', $this->getQrCodeUrl());
        if ($this->type == 1) {
            $res = $client->request('GET', $this->getQrCodeUrl());

            $qrImg = imagecreatefromstring($res->getBody());

        } else {
            $res = $this->getWxacode();

            if ($res === false) {return $target;}

            $qrImg = imagecreatefromstring($res->getBody());
            \Log::debug('------------小程序二维码-----------', $qrImg);
        }

        if ($qrImg === false) {return $target;}

        return $this->mergeImage($target, $item, $qrImg);
    }

    //生成小程序二维码
    function getWxacode()
    {
        $token = $this->getToken();

        if ($token === false) {return false;}

        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$token;
        $json_data = [
            "scene" => 'mid='.$this->memberModel->uid,
            "page"  => 'pages/index/index'
        ];
        $client = new Client;
        $res = $client->request('POST', $url, ['json'=>$json_data]);
        $data = json_decode($res->getBody()->getContents(), JSON_FORCE_OBJECT);

        //$path_file = $this->getPosterPath().'ceshi.png';
        //file_put_contents($path_file, $data);

        if (isset($data['errcode'])) {
            \Log::debug('===生成小程序二维码获取失败====='. self::class, $data);
            return false;
        }

        return $res;
    }

    //发送获取token请求,获取token(有效期2小时)
    public function getToken()
    {
        $set = \Setting::get('plugin.min_app');

        $paramMap = [
            'grant_type' => 'client_credential',
            'appid' => $set['key'],
            'secret' => $set['secret'],
        ];
        //获取token的url参数拼接
        $strQuery="";
        foreach ($paramMap as $k=>$v){
            $strQuery .= strlen($strQuery) == 0 ? "" : "&";
            $strQuery.=$k."=".urlencode($v);
        }

        $getTokenUrl = "https://api.weixin.qq.com/cgi-bin/token?". $strQuery; //获取token的url

        $client = new Client;
        $res = $client->request('GET', $getTokenUrl);
       // $res = $this->curl_post($getTokenUrl, '', $options = array());

        $data = json_decode($res->getBody()->getContents(), JSON_FORCE_OBJECT);

        if (isset($data['errcode'])) {
            \Log::debug('===生成小程序二维码获取token失败====='. self::class, $data);
            return false;
        }
        return $data['access_token'];

    }

    /**
     * @param $target
     * @param $item
     * @param $host
     * @return mixed
     */
    private function mergeQr_shopImage($target, $item, $host)
    {
        $url = $host . yzAppUrl('home', ['mid' => $this->memberModel->uid]);

        $res = \QrCode::format('png')->size(120)->margin(0)->generate($url);
        $qr_shopImg = imagecreatefromstring($res);
        return $this->mergeImage($target, $item, $qr_shopImg);
    }

    /**
     * 生成并合并
     * @param $target
     * @param $item
     * @param $host
     * @return mixed
     */
    private function mergeAppShareImage($target,$item,$host)
    {
        $url = $host . yzAppUrl('member/scaneditmobile', ['mid' => $this->memberModel->uid]);
        $res = \QrCode::format('png')->size(120)->margin(0)->generate($url);
        $qr_shopImg = imagecreatefromstring($res);
        return $this->mergeImage($target, $item, $qr_shopImg);
    }


    /**
     * 二维码连接
     * @return string
     */
    private function getQrCodeUrl()
    {
        return static::WE_CHAT_SHOW_QR_CODE_URL . $this->getPosterTicket();
    }

    /**
     * 二维码连接
     * @return string
     */
    private function getQr_shopCodeUrl()
    {
        return yzAppFullUrl('home');
    }


    private function getPosterTicket()
    {
        $existedPoster = $this->getExistedPoster();
        if ($existedPoster) {
            $qrcodeModel = Qrcode::getQrcodeById($existedPoster->qrcode_id);
            if ($qrcodeModel) {
                if ($qrcodeModel->ticket) {
                    return $qrcodeModel->ticket;
                }
                $qrcodeModel->delete();
            }
            $existedPoster->delete();
        }
        return $this->getTicket();
    }

    private function getTicket()
    {
        $scene = $this->getScene();
        $expire = $this->getExpire();

        $qrcode = self::createQR($scene, $expire);


        //记录到微擎框架的二维码数据表
        $data = array(
            'uniacid' => \YunShop::app()->uniacid,
            'acid' => \YunShop::app()->uniacid, //todo
            'type' => 'scene',
            'extra' => 0,
            'qrcid' => is_numeric($scene) ? $scene : 0,
            'scene_str' => is_numeric($scene) ? 0 : $scene,
            'name' => strtoupper(\Config::get('app.module_name')) . '_POSTER_QRCODE',
            'keyword' => $this->posterModel->keyword,
            'model' => $this->posterModel->type,
            'ticket' => $qrcode->ticket,
            'url' => $qrcode->url,
            'expire' => $expire,
            'subnum' => 0,
            'createtime' => time(),
            'status' => 1,
        );
        $qrcodeId = Qrcode::createAndGetInsertId($data);

        //记录到芸众插件的"海报-二维码关联"数据表
        $data = array(
            'uniacid' => \YunShop::app()->uniacid,
            'poster_id' => $this->posterModel->id,
            'qrcode_id' => $qrcodeId,
            'memberid' => $this->memberModel->uid,
        );
        PosterQrcode::create($data);
        return $qrcode->ticket;
    }


    private function getExpire()
    {
        switch ($this->posterModel->type) {
            case Poster::TEMPORARY_POSTER:
                $expire = $this->posterModel->time_end - time();
                break;
            case Poster::FOREVER_POSTER:
                $expire = 0;
                break;
            default:
                $expire = 0;
                break;
        }
        return $expire;
    }


    private function getScene()
    {
        if ($this->posterModel->type == Poster::TEMPORARY_POSTER) {
            $maxSceneId = Qrcode::getMaxSceneIdofTempQrcode();
            return empty($maxSceneId) ? 100001 : $maxSceneId + 1; //1~100000 是预留给"永久二维码"
        }
        return strtoupper(\Config::get('app.module_name')) . '_' . \YunShop::app()->uniacid . '_' . $this->posterModel->id . '_' . $this->memberModel->uid;
    }


    /**
     * 生成二维码
     * @param $scene
     * @param string $expire
     * @return mixed
     */
    private function createQR($scene, $expire = '')
    {
        $options = ResponseService::wechatConfig();
        $app = new Application($options);
        $qrcode = $app->qrcode;

        if (empty($expire)) { //永久二维码
            $result = $qrcode->forever($scene);
        } else { //临时二维码
            $result = $qrcode->temporary($scene, $expire);
        }
        return $result;
    }


    /**
     * 获取数据值
     * @param $params
     * @return mixed
     */
    private function getRealParams($params)
    {
        $params['left'] = intval(str_replace('px', '', $params['left'])) * 2;
        $params['top'] = intval(str_replace('px', '', $params['top'])) * 2;
        $params['width'] = intval(str_replace('px', '', $params['width'])) * 2;
        $params['height'] = intval(str_replace('px', '', $params['height'])) * 2;
        $params['size'] = intval(str_replace('px', '', $params['size'])) * 2;
        $params['src'] = tomedia($params['src']);
        return $params;
    }


    /**
     * 合并图片到 $target
     * @param $target
     * @param $params
     * @param $img
     * @return mixed
     */
    private function mergeImage($target, $params, $img)
    {
        $width = imagesx($img);
        $height = imagesy($img);
        imagecopyresized($target, $img, $params['left'], $params['top'], 0, 0, $params['width'], $params['height'], $width, $height);
        imagedestroy($img);

        return $target;
    }


    /**
     * 合并文字
     * @param $target
     * @param $params
     * @param $text
     * @return mixed
     */
    private function mergeText($target, $params, $text)
    {
        $font = base_path() . DIRECTORY_SEPARATOR . "static" . DIRECTORY_SEPARATOR . "fonts" . DIRECTORY_SEPARATOR . "source_han_sans.ttf";
        \Log::error('$short_background12345', $font);
        $colors = $this->hex2rgb($params['color']);
        $color = imagecolorallocate($target, $colors['red'], $colors['green'], $colors['blue']);
        imagettftext($target, $params['size'], 0, $params['left'], $params['top'] + $params['size'], $color, $font, $text);
        return $target;
    }


    /**
     * 附色（颜色）
     * @param $colour
     * @return array|bool
     */
    private function hex2rgb($colour)
    {
        if ($colour[0] == '#') {
            $colour = substr($colour, 1);
        }
        if (strlen($colour) == 6) {
            list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
        } elseif (strlen($colour) == 3) {
            list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
        } else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        Log::debug('red: ' . $r . ' green: ' . $g . ' blue: ' . $b);
        return array('red' => $r, 'green' => $g, 'blue' => $b);
    }


}
