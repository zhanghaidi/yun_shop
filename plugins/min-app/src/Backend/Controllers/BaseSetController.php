<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019/4/3
 * Time: 2:18 PM
 */

namespace Yunshop\MinApp\Backend\Controllers;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\facades\Setting;
use app\common\helpers\Url;
use Illuminate\Support\Facades\Storage;

class BaseSetController extends BaseController
{
    public function index()
    {
        $requestData = \YunShop::request()->min;
        if ($requestData) {
            if (request()->file('apiclient_cert')) {
                $requestData['apiclient_cert'] = $this->uploadFile('apiclient_cert');
            }
            if (request()->file('apiclient_key')) {
                $requestData['apiclient_key'] = $this->uploadFile('apiclient_key');
            }
            if (Setting::set('plugin.min_app', $requestData)) {
                return $this->message('设置成功', Url::absoluteWeb('plugin.min-app.Backend.Controllers.base-set'));
            }
            return $this->error('设置失败');
        }

        return view('Yunshop\MinApp::baseSet', $this->resultData());
    }

    /**
     * @param string $fileKey
     *
     * @return string
     * @throws ShopException
     */
    private function uploadFile($fileKey)
    {
        $file = request()->file($fileKey);

        if ($file->isValid()) {
            //文件原名
            $originalName = $file->getClientOriginalName();
            //扩展名
            $fileExt = $file->getClientOriginalExtension();
            //临时文件的绝对路径
            $realPath = $file->getRealPath();
            //新文件名
            $fileName = $this->fileName($originalName);

            if (!in_array($fileExt, $this->validExt())) {
                throw new ShopException("{$originalName}文件格式错误");
            }
            $bool = Storage::disk('cert')->put($fileName, file_get_contents($realPath));
            if ($bool) {
                return storage_path("cert/{$fileName}");
            }
        }
        throw new ShopException("{$fileKey}.pem文件上传错误");
    }

    /**
     * @param string $originName
     *
     * @return string
     */
    private function fileName($originName)
    {
        $i = \YunShop::app()->uniacid;

        return "{$i}_weChatApplet_{$originName}";
    }

    /**
     * @return array
     */
    private function validExt()
    {
        return ['pem'];
    }

    /**
     * @return array
     */
    private function resultData()
    {
        return [
            'set' => $this->appletSet()
        ];
    }

    /**
     * @return array
     */
    private function appletSet()
    {
        return Setting::get('plugin.min_app');
    }

}
