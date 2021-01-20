<?php
/**
 * Create date: 2017/12/20 10:20
 */

namespace Yunshop\VideoDemand\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use Yunshop\VideoDemand\models\LecturerModel;
use Yunshop\VideoDemand\models\MemberModel;
use Yunshop\VideoDemand\models\LecturerRewardLogModel;

class VideoDemandLecturerController extends ApiController
{
    public $set;
    public $uid;
    protected $lecturer_id;

    public function __construct()
    {
        parent::__construct();

        $this->set = Setting::get('plugin.video_demand');
        $this->uid = \Yunshop::app()->getMemberId();
    }

    /**
     * 讲师信息
     * @return json    
    */
    public function getLecturerInfo()
    {
        $agent = LecturerModel::getLecturerByMemberId($this->uid)->first();

        if (!$agent) {
            $this->errorJson('抱歉，您不是讲师');
        }

        $this->lecturer_id = $agent->id;

        $data['lecturer_name'] = $agent->real_name;
        $avatar = MemberModel::lecturerInfo($this->uid);
        $data['avatar'] = replace_yunshop(tomedia($avatar->avatar));

        //讲师总分红
        $data['lecturer_bonus'] = LecturerRewardLogModel::getRewardLogByLecturerId($agent->id)->sum('amount');
        //订单分红
        $data['order_bonus'] = LecturerRewardLogModel::getRewardLogByLecturerId($agent->id)->where('reward_type', 0)->sum('amount');
        //打赏分红
        $data['reward_bonus'] = LecturerRewardLogModel::getRewardLogByLecturerId($agent->id)->where('reward_type', 1)->sum('amount');

        // dd($data);
        $this->successJson('ok',$data);
    }

    /**
     * 讲师的收入记录
     * @return [type] [description]
     */
    public function lecturerRewardInfo()    
    {
       $id = (empty($this->lecturer_id)) ? LecturerModel::getLecturerByMemberId($this->uid)->first()->id : $this->lecturer_id;
       $account_id = \YunShop::request()->account_status;

        //$account_id 等于 0 为未结算, 1为已结算, 为空两个都显示
        $data = LecturerRewardLogModel::getRewardLogByLecturerId($id)->accountinfo($account_id)->get()->toArray();

        if ($data) {
            $this->successJson('ok', $data);
        } else {
            $this->errorJson('无记录');
        }

    }

}