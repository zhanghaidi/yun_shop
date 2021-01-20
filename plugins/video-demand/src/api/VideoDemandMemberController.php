<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/20
 * Time: 上午11:42
 */

namespace Yunshop\VideoDemand\api;


use app\common\components\ApiController;
use app\common\facades\Setting;
use Yunshop\VideoDemand\models\MemberModel;
use Yunshop\VideoDemand\models\MemberCourseModel;
use Yunshop\VideoDemand\models\RewardLogModel;
use Yunshop\VideoDemand\models\HistoricalModel;

class VideoDemandMemberController extends ApiController
{
    public $set;
    public $uid;

    protected $pageSize = 15;

    public function __construct()
    {
        parent::__construct();

        $this->set = Setting::get('plugin.video_demand');
        $this->uid = \YunShop::app()->getMemberId();
    }



    public function getVideoDemand()
    {
        // plugin.video-demand.api.video-demand-member.get-video-demand
        $data['is_video_demand'] = $this->set['is_video_demand'];
        if ($data) {
            return $this->successJson('成功', $data);
        }
        return $this->errorJson('未检测到数据!', $data);
    }

    /**
     * 会员个人信息
     * @return json
     */
    public function getMemberData()
    {
        $Member = new MemberModel();
        $manyArr = $Member->getData($this->uid);

        $data['nickname']  = $manyArr['nickname'];
        $data['avatar']    = replace_yunshop(tomedia($manyArr['avatar']));
        $data['level_name']= $manyArr['yz_member']['level']['level_name'] === null ? '普通会员' : $manyArr['yz_member']['level']['level_name'];

        $this->successJson('ok', $data);  
    }

    /**
     * 会员购买的课程
     * @return json 
     */
    public function getMeCourse()
    {
        $MemberCourse = new MemberCourseModel();
        $member_course = $MemberCourse->memberCourse($this->uid);

        foreach ($member_course as &$value) {
            $value['goods_title'] = $value['course_goods']['title'];
            $value['goods_thumb'] = replace_yunshop(tomedia($value['course_goods']['thumb']));
            unset($value['course_goods']);
        }

           //dd($member_course);

        $this->successJson('ok', $member_course);
    }

    /**
     * 会员的打赏
     * @return json 
     */
    public function getMeReward()
    {
        $RewardLog = new RewardLogModel;
        $member_reward = $RewardLog->meReward($this->uid);

        foreach ($member_reward as &$value) {
            $value['goods_title'] = $value['reward_goods']['title'];
            $value['goods_thumb'] = replace_yunshop(tomedia($value['reward_goods']['thumb']));
            $value['lecturer_name'] = $value['reward_lecturer']['real_name'];

            unset($value['reward_lecturer'], $value['reward_goods']);
        }
         // dd($member_reward);

        $this->successJson('ok', $member_reward);

    }

    /**
     * 添加会员的观看历史
     */
    public function setWatchHistory()
    {
        $where['uniacid'] = \YunShop::app()->uniacid;
        $where['member_id'] = $this->uid;
        $where['course_id'] = \YunShop::request()->get('course_id');
        $where['course_chapter_id'] = \YunShop::request()->get('chapter_id');
            
        $history = new HistoricalModel();

        $model = $history->inquireNotes($where);

        if ($model) {
            //有记录，更新时间
            $model->setRawAttributes($where);
            $model->created_at = time();
            $model->save();

        } else {
            //没记录，插入数据
            $history->setRawAttributes($where);
            $history->save();
        }
    }


    /**
     * 浏览历史
     * @return json
     */
    public function getScanHistory()
    {
        $Historical = new HistoricalModel();
        $member_history = $Historical->memberScan($this->uid, $this->pageSize);

        $data = [];
        foreach ($member_history['data'] as $key=>$value) {
            $data[$key]['course_goods_id'] = $value['history_course_goods']['goods_id'];
            $data[$key]['course_title']    = $value['history_course_goods']['has_one_goods']['title'];
            $data[$key]['course_thumb']    = replace_yunshop(tomedia($value['history_course_goods']['has_one_goods']['thumb']));
            $data[$key]['course_chapter_id'] = $value['history_course_chapter']['id'];
            $data[$key]['chapter_name']      = $value['history_course_chapter']['chapter_name'];
        }
        //没有历史记录
        if (empty($data)) {
            $this->errorJson('无观看记录');
        }

        $member_history['data'] = $data;

        $this->successJson('ok', $member_history);
    }

    //清除历史
    public function historicalPurge()
    {
        $bool = HistoricalModel::where('member_id', $this->uid)->delete();

        if (!$bool) {
            $this->errorJson('清空失败');
        }
            $this->successJson('已清空');
    }

}