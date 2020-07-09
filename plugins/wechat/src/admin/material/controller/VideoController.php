<?php

namespace Yunshop\Wechat\admin\material\controller;

use Yunshop\Wechat\admin\material\model\WechatAttachmentVideo;
use Yunshop\Wechat\common\helper\Helper;
use app\common\modules\wechat\WechatApplication;
use app\platform\modules\application\models\CoreAttach;

class VideoController extends MaterialController
{
    // 上传视频最大字节
    const VIDEO_SIZE_LIMIT = 10485760;

    public function index()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $data = WechatAttachmentVideo::getAttachmentVideos($page)->toArray();
        foreach ($data['data'] as &$video) {
            $video['attachment'] = yz_tomedia($video['attachment'],true);
        }
        return $this->successJson('success',$data);
    }

    public function getWechatVideo()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $data = WechatAttachmentVideo::getAttachmentWechatVideos($page)->toArray();
        foreach ($data['data'] as &$video) {
            $video['attachment'] = yz_tomedia($video['attachment'],true);
        }
        /*
        foreach ($data['data'] as &$video) {
            $video['tag'] = unserialize($video['tag']);
        }
        */
        return $this->successJson('success',$data);
    }

    public function getLocalVideo()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $data = CoreAttach::uniacid()
            ->where('type','=',CoreAttach::VIDEO_TYPE)
            ->where('upload_type','=',CoreAttach::UPLOAD_LOCAL)
            ->orderBy('id','desc')
            ->paginate(WechatAttachmentVideo::PAGE_SIZE,['*'],'page',$page)
            ->toArray();
        foreach ($data['data'] as &$video) {
            $video['attachment'] = yz_tomedia($video['attachment'],true);
        }
        return $this->successJson('success',$data);
    }
    public function add()
    {

    }
    public function edit()
    {

    }

    // 服务器视频转换微信视频
    public function localToWechat()
    {
        $id = request()->id;
        if (empty($id)) {
            return $this->errorJson('id不能为空!');
        }
        $coreAttach = CoreAttach::uniacid()->where('upload_type', '=', CoreAttach::UPLOAD_LOCAL)->find($id);
        if(empty($coreAttach)) {
            return $this->errorJson('视频不存在或已删除!');
        }
        $result = $this->uploadWechat($coreAttach);
        if (!$result['status']) {
            return $this->errorJson('视频'.$coreAttach->attachment.'保存数据失败!'.$result['message']);
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
        $type = request('type');
        $file = $_FILES['file'];
        if ($type == 'wechat') {
            $coreAttach = $this->uploadLocal($file,$ext);
            $result = $this->uploadWechat($coreAttach);
            if (!$result['status']) {
                return $this->errorJson('视频'.$coreAttach->attachment.'保存数据失败!'.$result['message']);
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
            $resultWechat = $material->uploadVideo(Helper::getRootName().$coreAttach->attachment,$coreAttach->filename,$coreAttach->attachment);// 绝对路径
        } catch(\Exception $exception) {
            return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        if (empty($resultWechat['media_id'])) {
            return $this->errorJson($coreAttach->attachment.'上传微信失败');
        }
        // 保存微信素材表
        $result =  WechatAttachmentVideo::saveWechatAttachment($resultWechat,WechatAttachmentVideo::ATTACHMENT_TYPE_VIDEO,$coreAttach);
        return $result;
    }

    public function uploadLocal($file,$ext)
    {
        $imageTypes = array('mp4');
        // 判断文件类型
        if (!in_array($ext,$imageTypes)) {
            return $this->errorJson('请选择格式正确的视频，仅支持mp4格式!');
        }
        $harm_type = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
        $ext2 = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext2,$harm_type)) {
            return $this->errorJson('不允许上传该文件类型!');
        }
        // 判断文件大小
        if ($file['size'] >= static::VIDEO_SIZE_LIMIT) {
            return $this->errorJson('视频文件大小不能大于10M');
        }
        if ($file['size'] <= 0) {
            return $this->errorJson('文件大小为0');
        }
        $coreAttach = $this->saveCoreAttachment($file['tmp_name'],$file['name'],$ext,CoreAttach::VIDEO_TYPE,Helper::VIDEO_FOLDER_NAME,true);
        return $coreAttach;
    }

}
