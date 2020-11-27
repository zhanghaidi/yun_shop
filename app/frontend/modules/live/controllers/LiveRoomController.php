<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/26 下午1:44
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\live\controllers;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\frontend\models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use app\common\models\live\CloudLiveRoom;
use app\common\services\tencentlive\LiveService;
use app\common\services\tencentlive\IMService;

class LiveRoomController extends ApiController
{

    protected $ignoreAction = ['index','detail'];

    public function index()
    {
//        $_model = new LiveService();
//        return $this->successJson('调试接口',$_model->getDescribeLiveStreamState(3));
        $im_service = new IMService();
//        return $this->successJson('调试接口',$im_service->getGroupList());
//        return $this->successJson('调试接口',$im_service->getGroupInfo('ajygroup-3'));
        return $this->successJson('调试接口',$im_service->getGroupMsg('ajygroup-3'));
    }

    public function getSign(){
        $member_id = \YunShop::app()->getMemberId();
        if(!empty($member_id)){
            return $this->successJson('获取签名成功',['usersig'=>IMService::getSign($member_id),'member_id'=>$member_id]);
        }else{
            return $this->errorJson('请登录');
        }

    }

    public function detail()
    {
        $id = intval(request()->id);

        if (!$id) {
            return  $this->errorJson('直播间ID为空');
        }

        $_model = CloudLiveRoom::where('id',$id)->first();
        if (!$_model) {
            return $this->errorJson('未获取到直播间');
        }

        $im_service = new IMService();
        $_model->online_num = $im_service->getOnlineMemberNum($_model->group_id);

        return $this->successJson('获取直播间信息成功！',$_model->toArray());
    }


}
