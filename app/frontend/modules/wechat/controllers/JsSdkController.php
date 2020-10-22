<?php
/**
 * Created by PhpStorm.
 * User: zlt
 * Date: 2020/10/22
 * Time: 10:00
 */

namespace app\frontend\modules\wechat\controllers;

use app\common\components\BaseController;
use app\common\models\AccountWechats;
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
        $res_mini = Setting::get('plugin.min_app');
        if(empty($res_mini)){
            $this->errorJson('获取小程序配置失败');
        }
        $res = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);
        $options = [
            'app_id' => $res['key'],
            'secret' => $res['secret'],
        ];
        if(empty($res)){
            $this->errorJson('获取公众号配置失败');
        }
        $app = new Application($options);
        $app = $app->js;
        $res = $app->signature(request()->url);
        if($res){
            $res['mini_origin_id'] = $res_mini['origin_id'] ?? 'gh_1d1360810f09';
            $res['mini_appid'] = $res_mini['key'];
            return $this->successJson('获取成功',$res);
        }else{
            $this->errorJson('获取失败');
        }
    }
}