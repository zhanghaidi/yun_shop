<?php

namespace Yunshop\Wechat\admin\material\controller;

use Yunshop\Wechat\admin\material\model\WechatAttachmentImage;
use Yunshop\Wechat\common\helper\Helper;
use app\platform\modules\application\models\CoreAttach;
use app\common\modules\wechat\WechatApplication;

class ImageController extends MaterialController
{
    // 上传图片最大字节
    const IMAGE_SIZE_LIMIT = 1048576;

    public function index()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $data = WechatAttachmentImage::getAttachmentImages($page)->toArray();
        foreach ($data['data'] as &$image) {
            if (strpos($image['attachment'],'http') === 0) {
                $image['attachment'] = yzWebFullUrl('plugin.wechat.admin.material.controller.material.getWechatImageResource',['attachment'=>$image['attachment']]);
            } else if(strpos($image['attachment'],'image') === 0) {
                $image['attachment'] = yz_tomedia($image['attachment'],true);
            }
        }
        return $this->successJson('success',$data);
    }

    public function getWechatImage()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $data = WechatAttachmentImage::getAttachmentWechatImages($page)->toArray();
        foreach ($data['data'] as &$image) {
            if (strpos($image['attachment'],'http') === 0) {
                $image['attachment'] = yzWebFullUrl('plugin.wechat.admin.material.controller.material.getWechatImageResource',['attachment'=>$image['attachment']]);
            } else if(strpos($image['attachment'],'image') === 0) {
                $image['attachment'] = yz_tomedia($image['attachment'],true);
            }
        }
        return $this->successJson('success',$data);
    }

    public function getWechatImageV2()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $data = WechatAttachmentImage::getAttachmentWechatImages($page)->toArray();
        foreach ($data['data'] as &$image) {
            //不知道为什么要加上域名前缀，加上前缀图文封面显示错误，所以重写了一个方法
//            if (strpos($image['attachment'],'http') === 0) {
//                $image['attachment'] = yzWebFullUrl('plugin.wechat.admin.material.controller.material.getWechatImageResource',['attachment'=>$image['attachment']]);
//            } else
             if(strpos($image['attachment'],'image') === 0) {
                $image['attachment'] = yz_tomedia($image['attachment'],true);
            }
        }
        return $this->successJson('success',$data);
    }

    public function getLocalImage()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $list = CoreAttach::uniacid()
            ->where('type','=',CoreAttach::IMAGE_TYPE)
            ->where('upload_type','=',CoreAttach::UPLOAD_LOCAL)
            ->orderBy('id','desc')
            ->paginate(WechatAttachmentImage::PAGE_SIZE,['*'],'page',$page)
            ->toArray();
        foreach ($list['data'] as &$image) {
            $image['attachment'] = yz_tomedia($image['attachment'],true);
        }
        return $this->successJson('success',$list);
    }

    public function add()
    {

    }
    public function edit()
    {

    }

    public function fetch()
    {
        $url = trim(request()->url);
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
        $result = $this->uploadWechat($coreAttach);
        if (!$result['status']) {
            return $this->errorJson('图片'.$coreAttach->attachment.'保存数据失败!'.$result['message']);
        }
        return $this->successJson('success');

        /*
        $url = trim(request()->url);
        $resp = ihttp_get($url);
        if (is_error($resp)) {
            return $this->errorJson('提取文件失败, 错误信息: ' . $resp['message']);
        }
        if (intval($resp['code']) != 200) {
            return $this->errorJson('提取文件失败: 未找到该资源文件.');
        }
        switch ($resp['headers']['Content-Type']) {
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
        if (intval($resp['headers']['Content-Length']) > static::IMAGE_SIZE_LIMIT) {
            return $this->errorJson('上传的媒体文件过大(' . intval($resp['headers']['Content-Length']) . ' > ' . static::IMAGE_SIZE_LIMIT);
        }

        $coreAttach = $this->saveCoreAttachment($resp['content'],'',$ext,CoreAttach::IMAGE_TYPE,Helper::IMAGE_FOLDER_NAME);
        $result = $this->uploadWechat($coreAttach);
        if (!$result['status']) {
            return $this->errorJson('图片'.$coreAttach->attachment.'保存数据失败!'.$result['message']);
        }
        return $this->successJson('success');
        */
    }

    // 服务器图片转换微信图片
    public function localToWechat()
    {
        $id = request()->id;
        if (empty($id)) {
            return $this->errorJson('id不能为空!');
        }
        $coreAttach = CoreAttach::uniacid()->where('upload_type', '=', CoreAttach::UPLOAD_LOCAL)->find($id);
        if(empty($coreAttach)) {
            return $this->errorJson('图片不存在或已删除!');
        }
        $result = $this->uploadWechat($coreAttach);
        if (!$result['status']) {
            return $this->errorJson('图片'.$coreAttach->attachment.'保存数据失败!'.$result['message']);
        }
        return $this->successJson('success',$result['data']->toArray());
    }

    public function upload(\Illuminate\Http\Request $request)
    {
        if (!$request->hasFile('file')) {
            return $this->errorJson('请选择文件!');
        }
        if (!($request->file('file')->isValid())){
            return $this->errorJson('上传出现错误!');
        }
        $ext = strtolower($request->file('file')->extension());
        $file = $_FILES['file'];
        $type = request('type');
        //$file = $_FILES['file'];
        if ($type == 'wechat') {
            $coreAttach = $this->uploadLocal($file,$ext);
            $result = $this->uploadWechat($coreAttach);
            if (!$result['status']) {
                return $this->errorJson('图片'.$coreAttach->attachment.'保存数据失败!'.$result['message']);
            }
            return $this->successJson('success');
        } elseif ($type == 'local') {
            $coreAttach = $this->uploadLocal($file,$ext);
            return $this->successJson('success',json_encode($coreAttach->toArray()));
        }
        return $this->errorJson('上传类型错误!');
    }
    // 上传微信，先存本地，再绝对路径传微信，得到url和media_id
    public function uploadWechat($coreAttach)
    {
        // 传输微信，得到media_id
        $wechatApp = new WechatApplication();
        $material = $wechatApp->material;
        try {
            $media = $material->uploadImage(Helper::getRootName().$coreAttach->attachment);// 绝对路径
        } catch(\Exception $exception) {
            return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        if (empty($media['media_id'])) {
            return $this->errorJson($coreAttach->attachment.'上传微信失败');
        }
        // 保存微信素材表
        $result =  WechatAttachmentImage::saveWechatAttachment($media,WechatAttachmentImage::ATTACHMENT_TYPE_IMAGE,$coreAttach);
        return $result;
    }

    public function uploadLocal($file,$ext)
    {
        $imageTypes = array('gif', 'jpg', 'jpeg', 'bmp', 'png');
        // 判断文件类型
        //$ext = Helper::getFileType($file['type']);
        if (!in_array($ext,$imageTypes)) {
            return $this->errorJson('请选择格式正确的图片，仅支持jpg,jpeg,bmp,png,gif等类型!');
        }
        $harm_type = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
        $ext2 = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext2,$harm_type)) {
            return $this->errorJson('不允许上传该文件类型!');
        }
        // 判断文件大小
        if ($file['size'] >= static::IMAGE_SIZE_LIMIT) {
            return $this->errorJson('图片大小不能大于1M');
        }
        if ($file['size'] <= 0) {
            return $this->errorJson('文件大小为0');
        }

        $coreAttach = $this->saveCoreAttachment($file['tmp_name'],$file['name'],$ext,CoreAttach::IMAGE_TYPE,Helper::IMAGE_FOLDER_NAME,true);

        return $coreAttach;
    }
}
