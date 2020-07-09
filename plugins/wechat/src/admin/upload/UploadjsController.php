<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/5/9
 * Time: 18:53
 */
namespace Yunshop\Wechat\admin\upload;

class UploadjsController extends \app\common\components\BaseController
{
    public function index()
    {
        $file = request()->file('file');
    	
    	if ($file) {

      		$checkFile = $this->checkFile();
    	
	    	if ($checkFile != 1) {
	    		
	    		return $this->errorJson($checkFile);
	    	}

            $filename = $file->getClientOriginalName();
			//执行上传
	        $ext = $file->getClientOriginalExtension(); //文件扩展名
	        
	        $realPath = $file->getRealPath();   //临时文件的绝对路径

            if (file_exists(base_path($filename))) {
                $reso = fopen(base_path($filename), 'w+');
                fwrite($reso, file_get_contents($realPath));
                fclose($reso);
            
            } else {

                $res = file_put_contents(base_path($filename), file_get_contents($realPath));

                if (!$res) {
                    return $this->errorJson('上传失败');                
                }   
            }
	        $pro = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://';

	        if (config('app.framework') == 'platform') {
	        	
	        	$url = $pro.$_SERVER['HTTP_HOST'].'/'.$filename;
	        
	        } else {
	        	$url = $pro.$_SERVER['HTTP_HOST'].'/addons/yun_shop/'.$filename;
	        }
        	return $this->successJson('上传成功', $url);
        }
        
        return view('Yunshop\Wechat::admin.upload.upload', [])->render();
    }

    private function checkFile()
    {
        $file = request()->file('file');

    	if (!$file) {
            return $this->errorJson('请选择文件');
        }

        if (!$file->isValid()) {
        	return $this->errorJson('上传失败请重试');
        }
    	//获取文件类型
    	if ($file->getClientOriginalExtension() != 'txt') {
    		return '格式不符合';
    	}
    	
    	if ($file->getClientSize() > 10 * 1024 * 1024) {
    		return '文件内容大于10M';
    	}

    	if (!preg_match("/^[A-Za-z0-9]+$/", $file)) {
    		// return '文件不合法, 请重新选择';
    	}

    	return 1;
    }

}