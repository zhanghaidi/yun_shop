<?php
/**
 * Created by PhpStorm.
 * User: zlt
 * Date: 2020/10/22
 * Time: 10:00
 */

namespace app\frontend\modules\wechat\controllers;

use app\common\components\BaseController;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Log;
use app\common\facades\Setting;

class JsSdkController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function signature()
    {
        $this->validate(
            [
                'url' => 'required'
            ]
        );
        $res = Setting::get('plugin.min_app');
        if(empty($res)){
            $this->errorJson('获取小程序配置失败');
        }
        $options = [
            'app_id' => $res['key'],
            'secret' => $res['secret'],
        ];
        $app = new Application($options);
        $app = $app->js;
        $res = $app->signature(request()->url);
        if($res){
            return $this->successJson('获取成功',$res);
        }else{
            $this->errorJson('获取失败');
        }
    }
}