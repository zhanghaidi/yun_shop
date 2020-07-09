<?php

namespace Yunshop\Wechat\admin\material\controller;

use Yunshop\Wechat\admin\material\model\WechatAttachmentVoice;
use app\common\modules\wechat\WechatApplication;
use Yunshop\Wechat\common\helper\Helper;
use app\platform\modules\application\models\CoreAttach;

class VoiceController extends MaterialController
{
    // 语音大小限制，2M
    const VOICE_SIZE_LIMIT = 2097152;
    // 语音时长限制，60s
    const VOICE_LENGTH_LIMIT = 60;

    public function index()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $data = WechatAttachmentVoice::getAttachmentVoices($page)->toArray();
        foreach ($data['data'] as &$voice) {
            $voice['attachment'] = yz_tomedia($voice['attachment'],true);
        }
        return $this->successJson('success',$data);
    }
    public function getWechatVoice()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $data = WechatAttachmentVoice::getAttachmentWechatVoices($page)->toArray();
        foreach ($data['data'] as &$voice) {
            $voice['attachment'] = yz_tomedia($voice['attachment'],true);
        }
        return $this->successJson('success',$data);
    }

    public function getLocalVoice()
    {
        $page = (int)request()->page ? (int)request()->page : 1;
        $data = CoreAttach::uniacid()
            ->where('type','=',CoreAttach::VOICE_TYPE)
            ->where('upload_type','=',CoreAttach::UPLOAD_LOCAL)
            ->orderBy('id','desc')
            ->paginate(WechatAttachmentVoice::PAGE_SIZE,['*'],'page',$page)
            ->toArray();
        foreach ($data['data'] as &$voice) {
            $voice['attachment'] = yz_tomedia($voice['attachment'],true);
        }
        return $this->successJson('success',$data);
    }


    public function add()
    {

    }
    public function edit()
    {

    }

    // 服务器转换微信
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
        $type = request('type');
        if (!$request->hasFile('file')) {
            return $this->errorJson('请选择文件!');
        }
        if (!($request->file('file')->isValid())){
            return $this->errorJson('上传出现错误!');
        }
        // 获取文件后缀名，这里的音频格式不好获取真实的文件格式，所以直接使用文件后缀名
        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        //$ext = strtolower($request->file('file')->extension());
        $file = $_FILES['file'];
        if ($type == 'wechat') {
            $coreAttach = $this->uploadLocal($file,$ext);
            $result = $this->uploadWechat($coreAttach);
            if (!$result['status']) {
                return $this->errorJson('音频'.$coreAttach->attachment.'保存数据失败!'.$result['message']);
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
            $resultWechat = $material->uploadVoice(Helper::getRootName().$coreAttach->attachment);// 绝对路径
        } catch(\Exception $exception) {
            return $this->errorJson(Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
        }
        if (empty($resultWechat['media_id'])) {
            return $this->errorJson($coreAttach->attachment.'上传微信失败');
        }
        // 保存微信素材表
        $result =  WechatAttachmentVoice::saveWechatAttachment($resultWechat,WechatAttachmentVoice::ATTACHMENT_TYPE_VOICE,$coreAttach);
        return $result;
    }

    public function uploadLocal($file,$ext)
    {
        // 判断文件类型
        $Types = array('mp3', 'wma', 'wav', 'amr');
        if (!in_array($ext,$Types)) {
            return $this->errorJson('请选择格式正确的语音，仅支持mp3/wma/wav/amr等类型!');
        }
        $harm_type = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
        $ext2 = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext2,$harm_type)) {
            return $this->errorJson('不允许上传该文件类型!');
        }
        // 判断文件大小
        if ($file['size'] >= static::VOICE_SIZE_LIMIT) {
            return $this->errorJson('文件大小不能大于2M');
        }
        if ($file['size'] <= 0) {
            return $this->errorJson('文件大小为0');
        }

        $coreAttach = $this->saveCoreAttachment($file['tmp_name'],$file['name'],$ext,CoreAttach::VOICE_TYPE,Helper::VOICE_FOLDER_NAME,true);

        return $coreAttach;
    }
}
