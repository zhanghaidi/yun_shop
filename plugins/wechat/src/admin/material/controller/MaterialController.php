<?php

namespace Yunshop\Wechat\admin\material\controller;

use app\common\components\BaseController;
use app\platform\modules\application\models\CoreAttach;
use Yunshop\Wechat\common\helper\Helper;
use Yunshop\Wechat\common\model\WechatAttachment;
use app\common\modules\wechat\WechatApplication;

class MaterialController extends BaseController
{
    public function index()
    {
        return view('Yunshop\Wechat::admin.material.material');
    }

    // 传入微信图片链接，在该方法中使用微信图片链接获取图片资源，然后返回该资源
    public function getWechatImageResource()
    {
        $attachment = request()->attachment;
        $content = ihttp_request($attachment, '', array('CURLOPT_REFERER' => 'http://www.qq.com'));
        header('Content-Type:image/jpg');
        echo $content['content'];
        exit();
    }

    public function delete()
    {
        $id = intval(request('id'));
        $attachment = WechatAttachment::uniacid()->find($id);
        if (empty($attachment)) {
            return $this->errorJson('素材不存在或已删除!');
        }
        // 微信端删除
        if ($attachment->media_id) {
            $wechatApp = new WechatApplication();
            $material = $wechatApp->material;
            try {
                $material->delete($attachment->media_id);
            } catch(\Exception $exception) {
                return $this->errorJson('微信端删除失败，错误代码:'.Helper::getErrorMessage($exception->getCode(),$exception->getMessage()));
            }
        }
        // 删除本地
        if ($attachment->delete()) {
            return $this->successJson('素材删除成功!');
        }
        return $this->errorJson('删除失败!');
    }

    /**
     * 保存文件至服务器
     * @param $resource resource 文件资源
     * @param $originalName string 原文件名
     * @param $ext string 文件扩展名
     * @param $type int 文件类型，存储在CoreAttach中
     * @param $attachmentFolderName string 生成的文件夹名
     * @param $isUpload bool 是否是表单的文件上传
     * @param $upload_type int 存储方式，远程或本地
     * @return CoreAttach|\Illuminate\Http\JsonResponse
     */
    public function saveCoreAttachment($resource, $originalName, $ext, $type, $attachmentFolderName, $isUpload = false, $upload_type = CoreAttach::UPLOAD_LOCAL)
    {
        $user = \Auth::guard('admin')->user();
        $rootName = Helper::getRootName();
        $dirName = Helper::getUploadDirName($attachmentFolderName);
        $ext = strtolower($ext);
        do {
            $filename = Helper::getAttachmentFileName().'.'.$ext;
        } while (file_exists($rootName.$dirName.$filename));
        if (!file_exists($rootName.$dirName) && !mkdir(rtrim($rootName.$dirName,DIRECTORY_SEPARATOR),0777,true)) {
            return $this->errorJson($rootName.$dirName.':'.'文件夹创建失败');
        }
        if ($isUpload) {
            if (!move_uploaded_file($resource,$rootName.$dirName.$filename)) {
                return $this->errorJson($rootName.$dirName.$filename.'保存失败!');
            }
        } else {
            if (!file_put_contents($rootName.$dirName.$filename, $resource)) {
                return $this->errorJson($rootName.$dirName.$filename.'保存失败');
            }
        }
        // 保存数据库
        $coreAttach = new CoreAttach();
        $coreAttach->fill([
            'uniacid' => \YunShop::app()->uniacid,
            'uid' => $user->uid,
            'filename' => !empty($originalName) ? $originalName : $filename,
            'attachment' => $dirName.$filename,
            'type' => $type,
            'module_upload_dir' => '',
            'group_id' => 0,
            'upload_type' => $upload_type,
        ]);
        $validate = $coreAttach->validator();
        if ($validate->fails()) {
            return $this->errorJson($validate->messages()->first());
        }
        if (!$coreAttach->save()) {
            return $this->errorJson($originalName.'保存失败');
        }
        return $coreAttach;
    }
}

/*
switch ($resp['headers']['Content-Type']) {
		case 'application/x-jpg':
		case 'image/jpg':
		case 'image/jpeg':
			$ext = 'jpg';
			$type = 'images';
			break;
		case 'image/png':
			$ext = 'png';
			$type = 'images';
			break;
		case 'image/gif':
			$ext = 'gif';
			$type = 'images';
			break;
		case 'video/mp4':
		case 'video/mpeg4':
			$ext = 'mp4';
			$type = 'videos';
			break;
		case 'video/x-ms-wmv':
			$ext = 'wmv';
			$type = 'videos';
			break;
		case 'audio/mpeg':
			$ext = 'mp3';
			$type = 'audios';
			break;
		case 'audio/mp4':
			$ext = 'mp4';
			$type = 'audios';
			break;
		case 'audio/x-ms-wma':
			$ext = 'wma';
			$type = 'audios';
			break;
		default:
			return error(-1, '提取资源失败, 资源文件类型错误.');
			break;
	}

 */