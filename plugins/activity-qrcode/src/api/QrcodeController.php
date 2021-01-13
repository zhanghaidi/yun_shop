<?php

namespace Yunshop\ActivityQrcode\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
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
    protected $publicAction = ['index', 'scan', 'helper'];
    protected $ignoreAction = ['index', 'scan', 'helper'];

    protected $activityId;

    protected $ip;

    public function __construct()
    {
        global $_W;

        $activityId =  intval(\YunShop::request()->id);
        if(!$activityId){
            return $this->errorJson('参数错误', [
                'status' => 0
            ]);
        }
        $this->activityId = $activityId;

        //搜集新加入此页面的用户
        $this->userJoin($activityId, $_W);
    }

    //活码维码展示页面
    public function index()
    {

        $activityModel = Activity::getActivity($this->activityId);
        if(!$activityModel){
            return $this->errorJson('活码不存在或已失效');
        }

        return $this->successJson('ok', $activityModel);

    }

    //助手接口
    public function helper(){
        $activitySetting = Setting::get('plugin.activity-qrcode');
        return $this->successJson('ok', $activitySetting);
    }

   //扫码识别页面
    public function scan()
    {
        global $_W;
        $qrcode_id = intval(\YunShop::request()->qrcode_id);
        if(!$qrcode_id){
            return $this->errorJson('缺少参数');
        }
        $params = array(
            'uniacid' => \YunShop::app()->uniacid,
            'code_id' => $this->activityId,
            'ip' => $_W['clientip'],
        );

        ActivityUser::where($params)->update(['qrcode_id' => $qrcode_id]);

        $qrcodeModel = Qrcode::getInfo($qrcode_id);
        if($qrcodeModel->hasManyUserCount >= $qrcodeModel->switch_limit){
            $qrcodeModel->is_full = 1;
            $qrcodeModel->save();
        }

        return $this->successJson('ok');

    }


    //参与扫码记录
    protected function userJoin($activity_id, $_W)
    {
        $params = array(
            'uniacid' => \YunShop::app()->uniacid,
            'code_id' => $activity_id,
            'ip' => $_W['clientip'],
            //'container' => $_W['container'],
            //'os' => $_W['os'],
            //'openid' => $_W['openid']
        );

        $lockCacheKey = 'userJoin' . \YunShop::app()->uniacid . $activity_id . $_W['clientip'] . date('Y-m-d H:i:s');

        $lockCacheRs = Redis::setnx($lockCacheKey, 1);
        if ($lockCacheRs != 1) {
            return false;
        }
        Redis::expire($lockCacheKey, 5);

        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'code_id' => $activity_id,
            'ip' => $_W['clientip'],
            'container' => $_W['container'],
            'os' => $_W['os'],
            'openid' => $_W['openid'] ? $_W['openid'] : ''
        ];
        ActivityUser::firstOrCreate($params, $data);

    }

}