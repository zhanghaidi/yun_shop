<?php

namespace Yunshop\Wechat\admin\material\controller;

use app\common\components\BaseController;
use Yunshop\Wechat\admin\material\model\WechatAttachmentNews;
use app\common\modules\wechat\WechatApplication;
use app\platform\modules\application\models\CoreAttach;
use Yunshop\Wechat\common\helper\Helper;
use Yunshop\Wechat\common\model\WechatNews;
use Illuminate\Support\Facades\DB;

class NewsController extends MaterialController
{
    // 上传图片最大字节
    const IMAGE_SIZE_LIMIT = 1048576;

    public function index()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $data = WechatAttachmentNews::getAttachmentNews($page)->toArray();
        foreach ($data['data'] as $key =>&$attachment) {
            if (empty($attachment['has_many_wechat_news'])){
                array_splice($data['data'] , $key, 1);
            }
            foreach ($attachment['has_many_wechat_news'] as &$has_many_wechat_news) {
                $has_many_wechat_news['content'] = str_replace('data-src','src',$has_many_wechat_news['content']);
                if (strpos($has_many_wechat_news['thumb_url'],'http') === 0) {
                    $has_many_wechat_news['thumb_url'] = yzWebFullUrl('plugin.wechat.admin.material.controller.material.getWechatImageResource',['attachment'=>$has_many_wechat_news['thumb_url']]);
                } else if(strpos($attachment,'image') === 0) {
                    $has_many_wechat_news['thumb_url'] = yz_tomedia($has_many_wechat_news['thumb_url'],true);
                }
            }
        }
        return $this->successJson('success',$data);
    }

    public function getWechatNews()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $search = (int)request()->filename ?: '';
        $data = WechatAttachmentNews::getAttachmentWechatNews($page,$search)->toArray();
        foreach ($data['data'] as $key => &$attachment) {
            if (!$attachment['has_many_wechat_news']){
                array_splice($data['data'] , $key, 1);
            }
            foreach ($attachment['has_many_wechat_news'] as &$has_many_wechat_news) {
                if (strpos($has_many_wechat_news['thumb_url'],'http') === 0) {
                    $has_many_wechat_news['thumb_url'] = yzWebFullUrl('plugin.wechat.admin.material.controller.material.getWechatImageResource',['attachment'=>$has_many_wechat_news['thumb_url']]);
                } else if(strpos($attachment,'image') === 0) {
                    $has_many_wechat_news['thumb_url'] = yz_tomedia($has_many_wechat_news['thumb_url'],true);
                }
            }
        }
        return $this->successJson('success',$data);
    }

    public function getLocalNews()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $search = (int)request()->filename ?: '';
        $data = WechatAttachmentNews::getAttachmentLocalNews($page,$search)->toArray();
        foreach ($data['data'] as &$attachment) {
            foreach ($attachment['has_many_wechat_news'] as &$has_many_wechat_news) {
                if (strpos($has_many_wechat_news['thumb_url'],'http') === 0) {
                    $has_many_wechat_news['thumb_url'] = yzWebFullUrl('plugin.wechat.admin.material.controller.material.getWechatImageResource',['attachment'=>$has_many_wechat_news['thumb_url']]);
                } else if(strpos($attachment,'image') === 0) {
                    $has_many_wechat_news['thumb_url'] = yz_tomedia($has_many_wechat_news['thumb_url'],true);
                }
            }
        }
        return $this->successJson('success',$data);
    }

    // 通过media_id获取微信图片，下载到本地，然后上传微信
    public function uploadImage()
    {
        $type = request()->type;
        if ($type == 'wechat') { // 微信的图片，通过media_id获取图片，存储本地，然后上传，得到链接
            if (!empty(request()->media_id)) {
                try {
                    $resource = $this->getWechatMediaByMediaId(request()->media_id);
                } catch(\Exception $exception) {
                    return $this->errorJson( Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
                }
                // 保存至本地
                $path = $this->saveImageToLocal($resource);
                // 上传
                $url = $this->uploadNewsImageToWechat($path);
                return $this->successJson('成功!',['url' => $url]);

            } else {
                return $this->errorJson('参数错误!');
            }
        } elseif ($type == 'local') { // 本地图片，直接上传，得到链接
            $coreAttach = CoreAttach::uniacid()->find(request()->id);
            if (empty($coreAttach)) {
                return $this->errorJson('文件不存在或已删除!');
            }
            $path = Helper::getRootName().$coreAttach->attachment;
            $url = $this->uploadNewsImageToWechat($path);
            return $this->successJson('成功!',['url' => $url]);
        } elseif ($type == 'fetch') { // 远程链接，fetch到本地，然后上传，得到链接
            $fetchUrl = trim(request()->url);
            $attachment = $this->fetch($fetchUrl);
            $path = Helper::getRootName().$attachment;
            $url = $this->uploadNewsImageToWechat($path);
            return $this->successJson('成功!',['url' => $url]);
        } else {
            return $this->errorJson('上传方式错误!');
        }
    }

    public function saveImageToLocal($resource)
    {
        $rootName = Helper::getRootName();
        $dirName = Helper::getUploadDirName(Helper::IMAGE_FOLDER_NAME);
        do {
            $filename = Helper::getAttachmentFileName().'.jpeg';
        } while (file_exists($rootName.$dirName.$filename));
        if (!file_exists($rootName.$dirName) && !mkdir(rtrim($rootName.$dirName,DIRECTORY_SEPARATOR),0777,true)) {
            return $this->errorJson($rootName.$dirName.':'.'文件夹创建失败');
        }
        if (!file_put_contents($rootName.$dirName.$filename,$resource)) {
            return $this->errorJson($rootName.$dirName.$filename.'保存失败!');
        }
        return $rootName.$dirName.$filename;
    }

    public function uploadNewsImageToWechat($path)
    {
        $wechatApp = new WechatApplication();
        $material = $wechatApp->material;
        try {
            $result = $material->uploadArticleImage($path);
        } catch(\Exception $exception) {
            return $this->errorJson( Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        return $result->url;
    }

    // 先保存本地，再上传微信，最后更新本地mediaid和模式
    public function save()
    {
        $model = request('model');
        $form = request('form_data');
        if ($model ==  WechatAttachmentNews::ATTACHMENT_MODEL_WECHAT) {
            // 编辑
            if ($form['id']) {
                $ac = WechatAttachmentNews::uniacid()->find($form['id']);
                if (empty($ac)) {
                    return $this->errorJson('图文不存在或已删除!!');
                }
                if (!empty($ac['media_id'])) {// 更新微信图文
                    $this->updateToWechat($ac['media_id'],$form['has_many_wechat_news']);
                    $media_id = $ac['media_id'];
                } else { // 本地图文上传微信
                    $media_id = $this->uploadToWechat($form['has_many_wechat_news']);
                }
            } else { // 新增微信图文
                $media_id = $this->uploadToWechat($form['has_many_wechat_news']);
            }
            $wechatNewsList = $this->getWechatNewsList($media_id);
            DB::beginTransaction();
            $result = $this->saveAttachmentNews($model,$form['id'],$media_id);
            if ($result['status'] == 1) {
                $attachment = $result['data'];
                // 保存wechat_news表
                $result = $this->saveNews($attachment->id,$wechatNewsList,$form['has_many_wechat_news']);
                if ($result['status'] == 1) {
                    DB::commit();
                    return $this->successJson($result['message']);
                }
            }
            DB::rollBack();
            return $this->errorJson($result['message']);
        } else if ($model == WechatAttachmentNews::ATTACHMENT_MODEL_LOCAL) {
            //$wechatNewsList = $form['has_many_wechat_news'];
            DB::beginTransaction();
            $result = $this->saveAttachmentNews($model,$form['id'],'');
            if ($result['status'] == 1) {
                $attachment = $result['data'];
                // 保存wechat_news表
                $result = $this->saveNews($attachment->id,$form['has_many_wechat_news'],$form['has_many_wechat_news']);
                if ($result['status'] == 1) {
                    DB::commit();
                    return $this->successJson($result['message']);
                }
            }
            DB::rollBack();
            return $this->errorJson($result['message']);
        } else {
            return $this->errorJson('上传方式错误!');
        }
    }

    public function updateToWechat($media_id,$has_many_wechat_news)
    {
        $wechatApp = new WechatApplication();
        $material = $wechatApp->material;
        for ($i = 0; $i < count($has_many_wechat_news); $i++) {
            try {
                $material->updateArticle($media_id, $has_many_wechat_news[$i], $i);
            } catch(\Exception $exception) {
                $this->errorJson('更新微信数据失败!'.Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }
        }
        return true;
    }

    public function uploadToWechat($has_many_wechat_news)
    {
        // 重新查询图文列表hasManyWechatNews
        $wechatApp = new WechatApplication();
        $material = $wechatApp->material;
        $newsList = [];
        foreach ($has_many_wechat_news as $news) {
            $newsList[] = new \EasyWeChat\Message\Article($news);
        }
        if (empty($newsList)) {
            return $this->errorJson('缺少图文数据!');
        }
        try {
            $media = $material->uploadArticle($newsList);
        } catch(\Exception $exception) {
            return $this->errorJson('上传微信数据失败！'.Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        if (empty($media['media_id'] || empty($wechatNewsList))) {
            \Log::debug('--------wechat_message--------',$media);
            return $this->errorJson('上传微信失败,无法获取media_id');
        } else {
            return $media['media_id'];
        }
    }

    public function getWechatNewsList($media_id)
    {
        $wechatApp = new WechatApplication();
        $material = $wechatApp->material;
        try {
            $wechatNewsList = $material->get($media_id);
        } catch (\Exception $exception) {
            return $this->errorJson('获取微信图文列表异常!'.Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        return $wechatNewsList['news_item'];
    }

    public function saveAttachmentNews($model,$id,$media_id)
    {
        // 获取用户信息
        $user = \Auth::guard('admin')->user();
        if (!empty($id)) {
            $attachment = WechatAttachmentNews::uniacid()->find($id);
            if (empty($attachment)) {
                return ['status' => 0, 'message' => '素材不存在或已删除!', 'data' => []];
            }
        } else {
            $attachment = new WechatAttachmentNews();
            $attachment->createtime = time();
        }
        $attachment->uniacid = \YunShop::app()->uniacid;
        $attachment->acid = \YunShop::app()->uniacid;
        $attachment->type = WechatAttachmentNews::ATTACHMENT_TYPE_NEWS;
        $attachment->media_id = $media_id;
        $attachment->model = $model;
        $attachment->uid = $user->uid;
        $validate = $attachment->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
        }
        if ($attachment->save()) {
            return ['status' => 1, 'message' => '保存本地成功!', 'data' => $attachment];
        } else {
            return ['status' => 0, 'message' => '保存本地失败!', 'data' => []];
        }
    }

    /*
     * $wechatNewsList 从微信获取的数据，如果只是保存本地，则是页面填写的数据
     * $has_many_wechat_news 页面填写的数据
     */
    public function saveNews($attachmentId,$wechatNewsList,$has_many_wechat_news)
    {
        if (empty($attachmentId) || empty($wechatNewsList)) {
            return ['status' => 0, 'message' => '缺少素材ID或图文数据!', 'data' => []];
        }
        foreach ($has_many_wechat_news as $key => $news) {
            if (!empty($news['id'])) {
                $wechatNews = WechatNews::uniacid()->find($news['id']);
            } else {
                $wechatNews = new WechatNews();
            }
            $wechatNews->fill($wechatNewsList[$key]);
            $wechatNews->content = $news['content'];// 内容在上传微信后，它会对图片路径进行处理，src会变成data-src,本地显示不了,所以要在这处理
            $wechatNews->uniacid = \YunShop::app()->uniacid;
            $wechatNews->show_cover_pic = intval($wechatNews->show_cover_pic);
            $wechatNews->need_open_comment = intval(0);
            $wechatNews->only_fans_can_comment = intval(0);
            $wechatNews->attach_id = $attachmentId;
            $validate = $wechatNews->validator();
            if ($validate->fails()) {
                return ['status' => 0, 'message' => $validate->messages(), 'data' => []];
            }
            if (!$wechatNews->save()) {
                return ['status' => 0, 'message' => '图文数据保存本地失败!', 'data' => []];
            }
        }
        return ['status' => 1, 'message' => '图文数据保存本地成功!', 'data' => []];
    }

    // 查询出图文信息并渲染页面
    public function edit()
    {
        $id = request('id');
        $result = WechatAttachmentNews::getNewsById($id);
        foreach ($result['data']->hasManyWechatNews as $news) {
            $news->content = str_replace('data-src','src',$news->content);
            if (strpos($news->thumb_url,'http') === 0) {
                $news->thumb_url = yzWebFullUrl('plugin.wechat.admin.material.controller.material.getWechatImageResource',['attachment'=>$news->thumb_url]);
            } else if(strpos($news->thumb_url,'image') === 0) {
                $news->thumb_url = yz_tomedia($news->thumb_url,true);
            }
        }
        return view('Yunshop\Wechat::admin.material.news',['data' => json_encode($result)]);
    }

    // 删除,先删除本地，再删除微信端
    public function delete()
    {
        $id = intval(request('id'));
        $attachment = WechatAttachmentNews::uniacid()->find($id);
        if (empty($attachment)) {
            return $this->errorJson('图文不存在或已删除!');
        }
        // 删除微信
        if ($attachment->media_id) {
            $wechatApp = new WechatApplication();
            $material = $wechatApp->material;
            try {
                $material->delete($attachment->media_id);
            } catch(\Exception $exception) {
                return $this->errorJson('微信端删除失败，错误代码:'.Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }
        }
        DB::beginTransaction();
        // 删除本地
        WechatNews::uniacid()->where('attach_id','=',$id)->delete();
        // 删除本地
        if ($attachment->delete()) {
            DB::commit();
            return $this->successJson('素材删除成功!');
        }
        DB::rollBack();
        return $this->errorJson('删除失败!');
    }

    public function fetch($url)
    {
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
        switch ($response->contentType) {
            case 'application/x-jpg':
            case 'image/jpeg':
                $ext = 'jpg';
                break;
            case 'image/png':
                $ext = 'png';
                break;
            case 'image/gif':
                $ext = 'gif';
                break;
            default:
                return $this->errorJson('提取资源失败, 资源文件类型错误.');
                break;
        }
        if (intval($response->headers['Content-Length']) > static::IMAGE_SIZE_LIMIT) {
            return $this->errorJson('上传的媒体文件过大(' . intval($response->headers['Content-Length']) . ' > ' . static::IMAGE_SIZE_LIMIT);
        }
        $coreAttach = $this->saveCoreAttachment($response->content,'',$ext,CoreAttach::IMAGE_TYPE,Helper::IMAGE_FOLDER_NAME);
        return $coreAttach->attachment;
    }

    // 通过material->get(mediaid)获取图片时会出现json转换失败的错误,所以需要重新写这个方法，参考同步微信图片
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
}
