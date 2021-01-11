<?php

namespace Yunshop\ActivityQrcode\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use Illuminate\Support\Facades\DB;
use Yunshop\ActivityQrcode\models\Activity;
use Yunshop\ActivityQrcode\models\Qrcode;
use Yunshop\ActivityQrcode\models\ActivityUser;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/16
 * Time: 下午2:29
 */
class QrcodeController extends ApiController
{
    protected $publicController = ['Qrcode'];
    protected $publicAction = ['index', 'scan'];
    protected $ignoreAction = ['index', 'scan'];


    //活码维码展示页面
    public function index()
    {
        $activityId =  intval(\YunShop::request()->id);
        if(!$activityId){
            return $this->errorJson('参数错误', [
                'status' => 0
            ]);
        }
        $activityModel = Activity::getActivity($activityId);
        if(!$activityModel){
            return $this->errorJson('活码不存在或已失效');
        }

        return $this->successJson('ok', $activityModel);

    }

   //扫码识别页面
    public function scan()
    {

        return $this->successJson('ok-scan');
    }

}