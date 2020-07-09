<?php

namespace Yunshop\Poster\services;

use app\common\exceptions\AppException;
use app\common\helpers\ImageHelper;
use app\common\helpers\Url;
use app\common\services\Utils;
use app\frontend\modules\member\services\MemberOfficeAccountService;
use Yunshop\Poster\models\PostByWechat;
use Yunshop\Poster\models\Poster;
use Yunshop\Poster\models\PosterQrcode;
use Yunshop\Poster\models\Qrcode;
use EasyWeChat\Foundation\Application;
use app\common\models\Member;
use app\common\models\MemberShopInfo;
use app\common\models\McMappingFans;
use GuzzleHttp\Client;
use EasyWeChat\Message\Text;
use Illuminate\Support\Facades\Log;

//对微信文字类型的回复
class ResponseTextService extends ResponseService
{
    const WECHAT_GET_QRCODE_INTERFACE = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';

    public static function index($msg)
    {
        $update_wechat = false;
        $keyword = $msg['content'];
        $openid = $msg['fromusername'];
        $uniacid = \YunShop::app()->uniacid;
        $moduleName = \Config::get('app.module_name');

        $poster = Poster::getPosterByKeyword($keyword);
        if (empty($poster)){
            $notice = array(
                'type' => 'text',
                'content' => '没有找到海报,请检查关键词是否正确',
            );
            return $notice;
        }
        if ($poster->status != 1){
            $notice = array(
                'type' => 'text',
                'content' => '该海报已经失效, 请尝试其它海报',
            );
            return $notice;
        }

        $recommenderMemberShopInfo = MemberShopInfo::getMemberShopInfoByOpenid($openid);

        if (is_null($recommenderMemberShopInfo)) {
            $wechatUserBasicInfo = self::getWechatUserBasicInfo($openid); //获取已关注公众号的用户的基本信息

            (new MemberOfficeAccountService())->memberLogin(json_decode(json_encode($wechatUserBasicInfo), true), 0);
            $recommenderMemberShopInfo = MemberShopInfo::getMemberShopInfoByOpenid($openid);
        }

        //$recommenderId = McMappingFans::getUId($uniacid, $openid)->uid;
        
        //改为直接使用yz_member的member_id，防止出现mc_mapping_fans表出现重复
        $recommenderId = $recommenderMemberShopInfo->member_id;


        //判断是否在活动时间内
        $type = $poster->type;
        if ($type == Poster::TEMPORARY_POSTER){ //活动海报
            $status = self::checkTime($poster->time_start, $poster->time_end);
            if ($status == Poster::NOT_YET_START){
                $remind = $poster->supplement->not_start_reminder;
                $remind = empty($remind) ? '活动将于 [starttime] 开始，请耐心等候...' : $remind;
                $remind = self::dynamicTime($poster, $remind);

                $notice = array(
                    'type' => 'text',
                    'content' => $remind,
                );
                return $notice;
            } elseif ($status == Poster::ALREADY_FINISHED){
                $remind = $poster->supplement->finish_reminder;
                $remind = empty($remind) ? '活动已于 [endtime] 结束, 谢谢您的关注!' : $remind;
                $remind = self::dynamicTime($poster, $remind);

                $notice = array(
                    'type' => 'text',
                    'content' => $remind,
                );
                return $notice;
            }

            //场景值 (后面生成临时二维码需要用到)
            $maxSceneId = Qrcode::getMaxSceneIdofTempQrcode();
            $scene = empty($maxSceneId) ? 100001 : $maxSceneId + 1; //1~100000 是预留给"永久二维码"

        } else { //长期海报

            //场景字符串 (后面生成永久二维码需要用到)
            $sumOfForeverQrcode = Qrcode::getSumOfForeverQrcode();
            if ($sumOfForeverQrcode >= Qrcode::MAX_FOREVER_QRCODE_LIMIT){
                $notice = array(
                    'type' => 'text',
                    'content' => '无法生成二维码(超出总数限制), 请反馈给网站',
                );
                return $notice;
            }

            //为了方便统一"永久二维码"和"临时二维码"的逻辑, 后续并没有使用"场景字符串"里的 $recommenderId 来定位"推荐者"
            //而是使用"场景值"和"场景字符串"来确定二维码 ID, 然后在 yz_poster_qrcode 数据表中定位"推荐者"
            //这里"永久二维码"只使用"场景字符串"是为了和微擎的逻辑统一, 也避免与"可能采用'场景值'也可能采用'场景字符串'"的第三方冲突
            //PS. "场景字符串"开头不能为数字, 否则会被微擎框架解读为"场景值"
            $scene = strtoupper($moduleName) . '_' . $uniacid . '_' . $poster->id . '_' . $recommenderId;

        }

        //判断用户是否有发展下线的资格
        $recommenderMemberShopInfo = MemberShopInfo::getMemberShopInfo($recommenderId);
      
        $isAgent = $recommenderMemberShopInfo->is_agent;
        if ($isAgent == 0 && $poster->is_open == 0){
            $remind = $poster->supplement->not_open_reminder;
            if (empty($remind)){
                $remind = '您还没有发展下线的资格，努力去拥有资格获得专属海报吧!';
            }
            $remindUrl = $poster->supplement->not_open_reminder_url;
            if(!empty($remindUrl)){
                $remind .= "\n\n<a href='$remindUrl'>点击查看详细说明</a>";
            }

            $notice = array(
                'type' => 'text',
                'content' => $remind,
            );
            return $notice;
        }

        //海报正在生成时的提示 (因为生成海报的时间较长, 所以放在生成海报的代码之前)
        $waitNotice = $poster->supplement->wait_reminder;
        if (empty($waitNotice)){
            $waitNotice = '您的专属海报正在拼命生成中...';
        }
        $message = new Text(['content' => $waitNotice]);
        Message::sendNotice($openid, $message);

        //判断之前是否生成过二维码
        $existedPoster = PosterQrcode::getUserExistedPoster($recommenderId, $poster->id);
        if(!$existedPoster) { //之前没有生成二维码
            switch($type){
                case Poster::TEMPORARY_POSTER:
                    $expire = $poster->time_end - time();
                    break;
                case Poster::FOREVER_POSTER:
                    $expire = 0;
                    break;
                default:
                    $expire = 0;
                    break;
            }
            $qrcode = self::createQR($scene, $expire); //生成二维码
            $posterCreateTime = time();

            //记录到微擎框架的二维码数据表
            $moduleName = \Config::get('app.module_name');
            $data = array(
                'uniacid' => $uniacid,
                'acid' => $uniacid, //todo
                'type' => 'scene', //带参数二维码
                'extra' => 0,
                'qrcid' => is_numeric($scene) ? $scene : 0,
                'scene_str' => is_numeric($scene) ? 0 : $scene,
                'name' => strtoupper($moduleName) .'_POSTER_QRCODE',
                'keyword' => $keyword,
                'model' => $type,
                'ticket' => $qrcode->ticket,
                'url' => $qrcode->url,
                'expire' => $expire,
                'subnum' => 0,
                'createtime' => $posterCreateTime,
                'status' => 1,
            );
            $qrcodeId = Qrcode::createAndGetInsertId($data);

            //记录到芸众插件的"海报-二维码关联"数据表
            $data = array(
                'uniacid' => $uniacid,
                'poster_id' => $poster->id,
                'qrcode_id' => $qrcodeId,
                'memberid' => $recommenderId,
            );
            PosterQrcode::create($data);
        } else { //之前生成过二维码
            $qrcode = Qrcode::getQrcodeById($existedPoster->qrcode_id);
        }

        //生成用户的专属海报(使用微信news图文格式)
        $member = Member::getMemberById($recommenderId);

        if (is_null($qrcode)) {
            throw new AppException('二维码不存在');
        }

        $qrcodeImgUrl = self::WECHAT_GET_QRCODE_INTERFACE . $qrcode->ticket;
        $posterImg = self::createPoster($poster, $qrcodeImgUrl, $member);
        \Log::debug('-----posterImg-----'.$posterImg);
        if (!PostByWechat::hasFile($posterImg)) {
            \Log::debug('-----posterImg 不存在-----');
            $update_wechat = true;
        } else {
            $post_file = PostByWechat::getfile($posterImg);

            if ((!is_null($post_file) && $post_file->compare_at < $poster->updated_at) || (time() - strtotime($post_file->created_at)) > 3 * 24 * 3600) {
                \Log::debug('-----compare_at 小于 updated_at-----');
                $update_wechat = true;
            } else {
                \Log::debug('-----数据库返回 media_id-----');

                $mediaId = $post_file->media_id;

                if (0 == $post_file->uid && $recommenderId != 0) {
                    $post_file->uid = $recommenderId;
                    $post_file->save();
                }
            }
        }

        if ($update_wechat) {
            \Log::debug('-----上传图片到微信-----');
            //上传图片到微信
            $options = self::wechatConfig();
            try {
                $app       = new Application($options);
                $temporary = $app->material_temporary;
                $mediaId   = $temporary->uploadImage($posterImg)->media_id;
            } catch (\Exception $e) {
                \Log::debug('-----post upload wechat error----', [$e->getMessage()]);
            }

            if ($mediaId) {
                \Log::debug('------post_file_model-----');

                $file_data = [
                    'file_path'     => $posterImg,
                    'media_id'      => $mediaId,
                    'compare_at'    => strtotime($poster->updated_at),
                    'uid'           => $recommenderId
                ];
                \Log::debug('------file_data-----', $file_data);

                $post_file_model =  new PostByWechat();

                $post_file_model->fill($file_data);
                $post_file_model->save();
                \Log::debug('------post_file_model_save-----', [$post_file_model]);
            }
        }

        $image = array(
            'type' => 'image',
            'mediaid' => $mediaId,
        );
        return $image;

    }

    /*
     * 生成二维码
     */
    private static function createQR($scene, $expire='')
    {
        $options = self::wechatConfig();
        $app = new Application($options);
        $qrcode = $app->qrcode;

        if (empty($expire)){ //永久二维码
            $result = $qrcode->forever($scene);
        } else { //临时二维码
            $result = $qrcode->temporary($scene, $expire);
        }
        return $result;
    }


    /*
     * 生成海报 //生成时间较长
     */
    private static function createPoster(Poster $poster, $qrcodeUrl, $member)
    {
        $uniacid = \YunShop::app()->get('uniacid');
        $memberId = $member->uid;

        $path = storage_path('app/public/poster/'.$uniacid);

        Utils::mkdirs($path);

        $md5  = md5(json_encode ([
            'memberId' => $memberId,
            'posterId' => $poster->id,
            'background' => $poster->background,
            'style_data' => $poster->style_data,
        ]));
        $file = $md5 . '.png';
        if (!is_file($path . '/' . $file)) {
            set_time_limit(0);
            @ini_set('memory_limit', '256M');
            $target = imagecreatetruecolor(640, 1008);
            $white = imagecolorallocate($target, 255, 255, 255);
            imagefill($target,0,0,$white); //设置白色背景色
            //$size = getimagesize($poster->background);

            if ($poster->short_background) {
                $short_background =  yz_tomedia($poster->short_background,false,null,request()->getSchemeAndHttpHost());
//                if (config('APP_Framework') == 'platform') {
//                    $short_background = request()->getSchemeAndHttpHost() . '/static/upload/'. $poster->short_background;
//                } else {
//                    $short_background = request()->getSchemeAndHttpHost() . '/attachment/'. $poster->short_background;
//                }
            } else {
                $short_background = $poster->background;
            }

            $size = getimagesize($short_background);
            $width = $size[0]?:640;
            $height = $size[1]?:1008;
            //$bg = imagecreatefromstring(file_get_contents(tomedia($poster->background)));
            $bg = imagecreatefromstring(\Curl::to($short_background)->get());
            imagecopy($target, $bg, 0, 0, 0, 0, $width, $height);
            imagedestroy($bg);

            $data = json_decode(str_replace('&quot;', '\'', $poster->style_data), true);
            foreach ($data as $d) {
                $d = self::getRealData($d);
                if ($d['type'] == 'head') {
                    $client = new Client;
                    $member['avatar'] = ImageHelper::fix_wechatAvatar($member['avatar']);
                    if($member['avatar'] && preg_match('/^[http|https]/', $member['avatar'])){
                        $res = $client->request('GET',$member['avatar']);
                        $avatarImg = imagecreatefromstring($res->getBody());
                        $target = self::mergeImage($target, $d, $avatarImg);
                    }
                } elseif ($d['type'] == 'img') {
                    $srcImg = imagecreatefromstring(\Curl::to($d['src'])->get());
                    $target = self::mergeImage($target, $d, $srcImg);
                } elseif ($d['type'] == 'qr') {
                    $client = new Client;
                    $res = $client->request('GET',$qrcodeUrl);
                    $qrImg = imagecreatefromstring($res->getBody());
                    $target = self::mergeImage($target, $d, $qrImg);
                } elseif ($d['type'] == 'qr_shop') {
                    $res = \QrCode::format('png')->size(120)->margin(0)->generate(yzAppFullUrl('home',['mid'=>$member['uid']]));
                    $qr_shopImg = imagecreatefromstring($res);
                    $target = self::mergeImage($target, $d, $qr_shopImg);
                } elseif ($d['type'] == 'qr_app_share') {

                    if (app('plugins')->isEnabled('app-set')) {

                        $app_set = \Setting::get('shop_app.pay');

                        if (!empty($app_set['app_links'])) {
                            $res = \QrCode::format('png')->size(120)->margin(0)->generate($app_set['app_links']);
                            $qr_shopImg = imagecreatefromstring($res);
                            $target = self::mergeImage($target, $d, $qr_shopImg);
                        }
                    }
                } elseif ($d['type'] == 'nickname') {
                    $target = self::mergeText($target, $d, $member['nickname']);
                }
            }
            imagepng($target, $path. '/' . $file);
            imagedestroy($target);
        }
        $img = base_path() . "/storage/app/public/poster/" . $uniacid . "/" . $file;

        return $img;
    }

    //获取数值
    private static function getRealData($data)
    {
        $data['left'] = intval(str_replace('px', '', $data['left'])) * 2;
        $data['top'] = intval(str_replace('px', '', $data['top'])) * 2;
        $data['width'] = intval(str_replace('px', '', $data['width'])) * 2;
        $data['height'] = intval(str_replace('px', '', $data['height'])) * 2;
        $data['size'] = intval(str_replace('px', '', $data['size'])) * 2;
        $data['src'] = tomedia($data['src']);
        return $data;
    }

    //合并图片
    private static function mergeImage($target, $data, $img)
    {
        $w = imagesx($img);
        $h = imagesy($img);
        imagecopyresized($target, $img, $data['left'], $data['top'], 0, 0, $data['width'], $data['height'], $w, $h);
        imagedestroy($img);
        return $target;
    }

    //合并文字
    private static function mergeText($target, $data, $text)
    {
        $font = base_path() . DIRECTORY_SEPARATOR . "static" . DIRECTORY_SEPARATOR . "fonts" . DIRECTORY_SEPARATOR . "source_han_sans.ttf";
        $colors = self::hex2rgb($data['color']);
        $color = imagecolorallocate($target, $colors['red'], $colors['green'], $colors['blue']);
        imagettftext($target, $data['size'], 0, $data['left'], $data['top'] + $data['size'], $color, $font, $text);
        return $target;
    }

    //颜色
    private static function hex2rgb($colour)
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
        Log::debug('red: '.$r.' green: '.$g.' blue: '.$b);
        return array('red' => $r, 'green' => $g, 'blue' => $b);
    }

    //获取已关注用户的基本信息
    public static function getWechatUserBasicInfo($openid)
    {
        $options = self::wechatConfig();
        $app = new Application($options);
        $userService = $app->user;
        $userInfo = $userService->get($openid);

        return $userInfo;
    }
}
