<?php

namespace Yunshop\Wechat\admin\material\controller;

use app\common\modules\wechat\WechatApplication;
use Yunshop\Wechat\common\model\WechatAttachment;
use Yunshop\Wechat\common\helper\Helper;
use app\common\helpers\Cache;
use app\platform\modules\application\models\CoreAttach;

class SyncWechatController extends MaterialController
{
    // 返回素材的数量
    const MATERIAL_COUNT = 20;
    // 缓存时间，分钟
    const CACHE_TIME = 10;

    public function index()
    {
        $type = request('type');
        $types = [
            WechatAttachment::ATTACHMENT_TYPE_IMAGE,
            WechatAttachment::ATTACHMENT_TYPE_VOICE,
            WechatAttachment::ATTACHMENT_TYPE_NEWS,
            WechatAttachment::ATTACHMENT_TYPE_VIDEO
        ];
        if (!in_array($type,$types)) {
            return $this->errorJson('类型参数错误!');
        }
        $wechatApp = new WechatApplication();
        // 素材，
        $material = $wechatApp->material;

        // 获取素材计数
        // 缓存取计数器
        if (!Cache::has('wechat_material_stats')) {
            try {
                $stats = $material->stats();
            } catch (\Exception $exception) {
                return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }
            Cache::put('wechat_material_stats',$stats,self::CACHE_TIME);
        }
        $stats = Cache::get('wechat_material_stats');

        if ($type == WechatAttachment::ATTACHMENT_TYPE_IMAGE) {
            // 图片素材
            if (!$this->syncWechatMediaImage($material,$stats['image_count'],WechatAttachment::ATTACHMENT_TYPE_IMAGE)) {
                return $this->errorJson('图片同步失败!');
            }
        } else if ($type == WechatAttachment::ATTACHMENT_TYPE_VOICE) {
            // 语音素材
            if (!$this->syncWechatMediaVoice($material,$stats['voice_count'],WechatAttachment::ATTACHMENT_TYPE_VOICE)) {
                return $this->errorJson('语音同步失败!');
            }
        } else if ($type == WechatAttachment::ATTACHMENT_TYPE_NEWS) {
            // 图文素材
//            if (!$this->syncWechatMediaNews($material,$stats['news_count'],WechatAttachment::ATTACHMENT_TYPE_NEWS)) {
//                return $this->errorJson('图文同步失败!');
//            }
            if (!$this->syncWechatMediaNewsV2($material,$stats['news_count'],WechatAttachment::ATTACHMENT_TYPE_NEWS)) {
                return $this->errorJson('图文同步失败!');
            }
        } else if ($type == WechatAttachment::ATTACHMENT_TYPE_VIDEO) {
            // 视频素材
            if (!$this->syncWechatMediaVideo($material,$stats['video_count'],WechatAttachment::ATTACHMENT_TYPE_VIDEO)) {
                return $this->errorJson('视频同步失败!');
            }
        } else {
            return $this->errorJson('类型错误,同步失败!');
        }
        return $this->successJson('同步成功!');
    }

    public function syncWechatMediaImage(\EasyWeChat\Material\Material $material,$count,$mediaType)
    {
        $loopCount = intval($count/self::MATERIAL_COUNT) + 1;
        for ($i=0;$i < $loopCount; $i++) {
            // 它会返回media_id,拿来对比即可
            try {
                $medias = $material->lists($mediaType, self::MATERIAL_COUNT * $i,self::MATERIAL_COUNT);
            } catch (\Exception $exception) {
                return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }
            // 循环每个图片，查询数据库是否有该media_id
            foreach ($medias['item'] as $media) {
                $tempWechatAttachment = WechatAttachment::getWechatAttachmentByMediaId($media['media_id']);
                if (empty($tempWechatAttachment)) {
                    // 存储数据库
                    $result = WechatAttachment::saveWechatAttachment($media,$mediaType,null);
                    if ($result['status'] != 1) {
                        return $this->errorJson($result['message']);
                    }
                }
            }
        }
        return true;
    }

    // 音频，先保存本地，再获取
    public function syncWechatMediaVoice(\EasyWeChat\Material\Material $material,$count,$mediaType)
    {
        $loopCount = intval($count/self::MATERIAL_COUNT) + 1;
        for ($i=0;$i < $loopCount; $i++) {
            try {
                $medias = $material->lists($mediaType, self::MATERIAL_COUNT * $i,self::MATERIAL_COUNT);
            } catch (\Exception $exception) {
                return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }
            foreach ($medias['item'] as $media) {
                $tempWechatAttachment = WechatAttachment::getWechatAttachmentByMediaId($media['media_id']);
                if (empty($tempWechatAttachment)) {
                    $resource = $this->getWechatMediaByMediaId($media['media_id']);
                    $ext = strtolower(pathinfo($media['name'], PATHINFO_EXTENSION));
                    $coreAttach = $this->saveCoreAttachment($resource,$media['name'],$ext,CoreAttach::VOICE_TYPE,Helper::VOICE_FOLDER_NAME);
                    $result = WechatAttachment::saveWechatAttachment($media,WechatAttachment::ATTACHMENT_TYPE_VOICE,$coreAttach);
                    if ($result['status'] != 1) {
                        return $this->errorJson($result['message']);
                    }
                }
            }
        }
        return true;
    }

    public function test()
    {
        //$url = "http://203.205.158.77/vweixinp.tc.qq.com/1007_88d859e1a5984aeaa7f6dfd88ad26084.f10.mp4?vkey=E59F47DE9DF7E596179EEADFFE328E29E743F731634EBD5CA855D4BA3DB45D40532DA0A833AE9D831C63A64E1C69F99648E7A1C4673A7A9A18663600D16F49E949E0D479EB7F0DFFCB87E5B01A33D56D370BC2BA2FF5D625&sha=0&save=1";
$url2 = "http://203.205.158.74/vweixinp.tc.qq.com/1007_88d859e1a5984aeaa7f6dfd88ad26084.f10.mp4?vkey=D80DF09BE7AFFAD08BF34AF3F590347613F2BDBFEAD28CEE4EDDB1BE9433001568FD0D6BBFA30AB41ABE360C234CF4DECBB6ED2706BC5D733B7350BE808A376F5B7FB772436C117F46736DDFE8801F20D38F6CB64E47A1ED&sha=0&save=1";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($curl);
        var_dump(curl_error( $curl));
        var_dump(curl_getinfo($curl));
        //var_dump($data);
        curl_close($curl);
    }

    // 同步微信图文
    public function syncWechatMediaNews(\EasyWeChat\Material\Material $material,$count,$mediaType)
    {
        $loopCount = intval($count/self::MATERIAL_COUNT) + 1;
        for ($i=0;$i < $loopCount; $i++) {
            // 它会返回media_id,拿来对比即可
            try {
                $medias = $material->lists($mediaType, self::MATERIAL_COUNT * $i,self::MATERIAL_COUNT);
            } catch (\Exception $exception) {
                return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }
            // 循环每个图片，查询数据库是否有该media_id
            foreach ($medias['item'] as $media) {
                $tempWechatAttachment = WechatAttachment::getWechatAttachmentByMediaId($media['media_id']);
                if (empty($media['media_id'])){
                    continue;
                }
                if (empty($tempWechatAttachment)) {
                    // 图文，先保存wechat_attachment表，再保存wechat_news表
                    \Illuminate\Support\Facades\DB::beginTransaction();
                    $result = WechatAttachment::saveWechatAttachment($media,$mediaType,null);
                    if ($result['status'] != 1) {
                        return $this->errorJson($result['message']);
                    }
                    foreach ($media['content']['news_item'] as $news ) {
                        if (empty($news['thumb_media_id'])){
                            continue;
                        }
                        // 保存wechat_news表
                        $newsModel = new \Yunshop\Wechat\common\model\WechatNews();
                        $newsModel->fill($news);
                        $newsModel->uniacid = \YunShop::app()->uniacid;
                        $newsModel->attach_id = $result['data']->id;
                        $validate = $newsModel->validator();
                        if ($validate->fails()) {
                            \Illuminate\Support\Facades\DB::rollBack();
                            return $this->errorJson($mediaType.':'.$news['thumb_media_id'].':'.$validate->messages());
                        }
                        if (!$newsModel->save()) {
                            \Illuminate\Support\Facades\DB::rollBack();
                            return $this->errorJson($mediaType.':'.$news['thumb_media_id'].':'.'保存失败!');
                        }
                    }
                    \Illuminate\Support\Facades\DB::commit();
                }
            }
        }
        return true;
    }

    // 同步微信视频,视频不需要保存本地，只需要将mediaid和微信发送过来的内容进行存储
    // 有尝试通过down_url下载视频，但是报403错误
    // // 同步视频时出现302重定向的问题无法得到解决，最终决定同步时不下载视频文件至服务器
    public function syncWechatMediaVideo(\EasyWeChat\Material\Material $material,$count,$mediaType)
    {
        $loopCount = intval($count/self::MATERIAL_COUNT) + 1;
        for ($i=0;$i < $loopCount; $i++) {
            try {
                $medias = $material->lists($mediaType, self::MATERIAL_COUNT * $i,self::MATERIAL_COUNT);
            } catch (\Exception $exception) {
                return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }
            foreach ($medias['item'] as $media) {
                $tempWechatAttachment = WechatAttachment::getWechatAttachmentByMediaId($media['media_id']);
                if (empty($tempWechatAttachment)) {
                    $resource = $material->get($media['media_id']);
                    if (!empty($resource)) {
                        //$data = $this->getWechatVideoBycURL($resource['down_url']);
                        //$coreAttach = $this->saveCoreAttachment($data,$media['name'],'mp4',CoreAttach::VIDEO_TYPE,Helper::VIDEO_FOLDER_NAME);
                        $result = WechatAttachment::saveWechatAttachment($media,$mediaType,null,$resource);
                        if ($result['status'] != 1) {
                            return $this->errorJson($result['message']);
                        }
                    } else {
                        return $this->errorJson("media_id:".$media['media_id']."错误!");
                    }

                }
            }
        }
        return true;
    }

    // 通过MediaId获取image|video|audio的文件，MediaId正确则微信返回文件(通过$response->getBody()获取)，MediaId错误则返回错误原因描述的json数据，所以要先判断是不是json数据，是json数据则抛出错误，不是则返回文件
    // image|video|audio的文件類型就用這個方法，第三方easywechat的get方法在foreach ($response->getHeader('Content-Type') as $mime)的時候有问题，因为$response->getHeader('Content-Type')为null
    // 该方法是将easywechat的$material->get($mediaId)方法拿过来进行修改
    public function getWechatMediaByMediaId($media_id)
    {
        $wechatApp = new WechatApplication();
        $material = $wechatApp->material;
        try {
            $response = $material->getHttp()->json(\EasyWeChat\Material\Material::API_GET, ['media_id' => $media_id]);
        } catch (\EasyWeChat\Core\Exceptions\HttpException $exception) {
            return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        // 这个部分是对微信返回数据的处理
        if ($response instanceof \Psr\Http\Message\ResponseInterface) {
            $body = mb_convert_encoding($response->getBody(), 'UTF-8');
        }
        $body = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', trim($body));
        if (empty($body)) {
            return false;
        }
        $contents = json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
        \EasyWeChat\Support\Log::debug('API response decoded:', compact('contents'));
        \Log::debug('微信数据转换记录 '.'API response decoded:', compact('contents'));

        // json转换失败，说明是文件，不抛异常，而是直接返回$response->getBody()
        // 转化成功，说明是微信返回的错误，则抛出微信返回的错误信息
        if (JSON_ERROR_NONE !== json_last_error()) {
            // 获取文件，存在 $response->getBody()
            return $response->getBody();
        } else {
            if (isset($contents['errcode']) && 0 !== $contents['errcode']) {
                if (empty($contents['errmsg'])) {
                    $contents['errmsg'] = 'Unknown';
                }
                return $this->errorJson('errmsg:'.$contents['errmsg'].' errcode:'.$contents['errcode']);
            }
        }
        // 该return可能运行不到，但是为了保证正确调用，在这里留着
        return $response->getBody();
    }

    // 同步视频时出现302重定向的问题无法得到解决，最终决定同步时不下载视频文件至服务器
    public function getWechatVideoBycURL($url)
    {
        $response = \Curl::to($url)->withResponseHeaders()->returnResponseObject()->get();
        if ($response->status == 302 && !empty($response->headers['Location'])) {
            $this->getWechatVideoBycURL($response->headers['Location']);
        }

        $response = \Curl::to($url)->withResponseHeaders()->returnResponseObject()->get();
        if ($response->status != 200) {
            return $this->errorJson('错误代码:'.$response->status);
        }
        if (empty($response->content)) {
            return $this->errorJson('错误,提取内容为空!');
        }
        if (!empty($response->error)) {
            return $this->errorJson('错误信息:'.$response->error);
        }
        return $response->content;

        /*
        $resp = ihttp_get($url);
        if (is_error($resp)) {
            return $this->errorJson('提取文件失败, 错误信息: ' . $resp['message'].' 请稍后尝试!');
        }
        if (intval($resp['code']) != 200) {
            return $this->errorJson('提取文件失败: 未找到该资源文件.'.' 请稍后尝试!');
        }
        if (empty($resp['content'])) {
            return $this->errorJson('提取文件失败: 提取文件为空.'.' 请稍后尝试!');
        }
        return $resp['content'];
        */

        /*
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        $data = curl_exec($curl);
        $error = curl_error( $curl);
        //$info = curl_getinfo($curl);
        curl_close($curl);

        if ($info['http_code'] != 200) {
            return $this->errorJson("同步失败!http错误代码:".$info['http_code']);
        }

        if (!empty($error)) {
            return $this->errorJson("同步失败!http错误信息:".$error);
        }
        if (empty($data)) {
            return $this->errorJson("同步失败!接收数据为0");
        }
        return $data;
        */
    }


    /*
     * 优化同步素材
     */
    public function syncWechatMediaNewsV2(\EasyWeChat\Material\Material $material,$count,$mediaType)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        $wechatAttachment = new WechatAttachment();
        $tempWechatAttachmentModel = $wechatAttachment->getWechatAttachmentBy()->toArray();
        $first_names = array_column($tempWechatAttachmentModel, 'id');
        $wechatAttachment->uniacid()->whereIn('id',$first_names)->delete();//删除本地旧数据
        $news = \Yunshop\Wechat\common\model\WechatNews::uniacid()->whereIn('attach_id',$first_names)->delete();//删除本地旧数据

        $loopCount = intval($count/self::MATERIAL_COUNT) + 1;
        for ($i=0;$i < $loopCount; $i++) {
            // 它会返回media_id,拿来对比即可
            try {
                $medias = $material->lists($mediaType, self::MATERIAL_COUNT * $i,self::MATERIAL_COUNT);
            } catch (\Exception $exception) {
                return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }


            foreach ($medias['item'] as $media) {
                    // 图文，先保存wechat_attachment表，再保存wechat_news表
                    $result = WechatAttachment::saveWechatAttachment($media,$mediaType,null);
                    if ($result['status'] != 1) {
                        return $this->errorJson($result['message']);
                    }
                    foreach ($media['content']['news_item'] as $news ) {
//                        \Log::debug('图文',$news);
                        // 保存wechat_news表
                        $newsModel = new \Yunshop\Wechat\common\model\WechatNews();
                        $newsModel->fill($news);
                        $newsModel->uniacid = \YunShop::app()->uniacid;
                        $newsModel->attach_id = $result['data']->id;
                        $validate = $newsModel->validator();
                        if ($validate->fails()) {
                            \Illuminate\Support\Facades\DB::rollBack();
                            return $this->errorJson($mediaType.':'.$news['thumb_media_id'].':'.$validate->messages());
                        }
                        if (!$newsModel->save()) {
                            \Illuminate\Support\Facades\DB::rollBack();
                            return $this->errorJson($mediaType.':'.$news['thumb_media_id'].':'.'保存失败!');
                        }
                    }
            }
        }

        \Illuminate\Support\Facades\DB::commit();
        return true;
    }
}
