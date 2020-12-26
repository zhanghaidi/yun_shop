<?php


namespace Yunshop\XiaoeClock\frontend;

use app\common\components\ApiController;
use Yunshop\XiaoeClock\models\XiaoeClock;
use Yunshop\XiaoeClock\models\XiaoeClockTopic;
use Yunshop\XiaoeClock\models\XiaoeClockNote;
use Yunshop\XiaoeClock\models\XiaoeClockNoteLike;
use Yunshop\XiaoeClock\models\XiaoeClockNoteComment;
use Yunshop\XiaoeClock\models\XiaoeClockNoteCommentLike;
use Yunshop\XiaoeClock\models\XiaoeClockUser;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ClockController extends ApiController
{

    //日历打卡活动详情 ims_yz_xiaoe_clock
    public function getCalendarClock()
    {
        $id = request()->get('id');
        if (!$id) {
            return $this->errorJson('打卡id不能为空');
        }
        $member_id = \YunShop::app()->getMemberId();

        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();

        $clock = XiaoeClock::where('id', $id)->withCount(['hasManyNote', 'hasManyUser'])->with(['hasManyNote', 'hasManyTopic'])->with(['myNote' => function ($my_note) use ($member_id) {
            return $my_note->where('user_id', $member_id);
        }])->first();


        if (!$clock) {
            return $this->errorJson('不存在数据');
        }

        return $this->successJson('success', $clock);
    }


    //作业打卡活动详情
    public function getHomeWorkClock()
    {

        $id = request()->get('id');
        if (!$id) {
            return $this->errorJson('打卡id不能为空');
        }
        $member_id = \YunShop::app()->getMemberId();

        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();

        $clock = XiaoeClock::where('id', $id)->withCount(['hasManyNote', 'hasManyUser'])->with(['hasManyNote', 'hasManyTopic'])->with(['myNote' => function ($my_note) use ($member_id) {
            return $my_note->where('user_id', $member_id);
        }])->first();


        return $this->successJson('success', $clock);
    }

    //打卡活动主题 ims_yz_xiaoe_clock_task
    /*public function getClockTopic()
    {
        $id = request()->get('id');
        if(!$id){
            return $this->errorJson('主题id不能为空');
        }

        $topic = XiaoeClockTopic::where(['id'=> $id])->with(['hasManyUser'])->get();

        if(!$topic){
            return $this->errorJson('不存在数据');
        }

        return $this->successJson('success', $topic);

    }*/

    //打卡日记列表 ims_yz_xiaoe_users_clock
    /*public function clockNoteList()
    {


    }*/


    //打卡日记详情 ims_yz_xiaoe_users_clock
    public function clockNoteInfo()
    {
        $note_id = request()->get('id');
        $member_id = \YunShop::app()->getMemberId();
        if (!$note_id) {
            return $this->errorJson('日记id不能为空');
        }

        //关联用户打卡天数、关联主题主题参与人数和打卡数 关联评论
        $note = XiaoeClockNote::where('id', $note_id)
            ->select('id','user_id','clock_id','clock_task_id','type','text_desc','image_desc','audio_desc','video_desc','created_at')
            ->withCount(['hasManyLike','hasManyComment'])
            ->with([
                'user' => function($user) {
                    return $user->select('ajy_uid','nickname','avatarurl');
                },
                'topic'=>function($topic){
                    return $topic->select('id','clock_id','type','name');
                },
                'clock' => function($clock){
                    return $clock->select('id','name','cover_img')->withCount(['hasManyUser','hasManyNote']);
                },
                'hasManyLike' => function($like){
                    return $like->select('clock_users_id','user_id','created_at')->with([
                        'user' => function($user){
                            return $user->select('ajy_uid','nickname','avatarurl');
                        }
                    ]);
                },
                'hasManyComment' => function ($comment){
                    return $comment->select('id','clock_users_id','user_id','content','parent_id','is_reply','created_at')->with(['user' => function($user){
                        $user->select('ajy_uid','nickname','avatarurl');
                    }])->orderBy('id','desc');
                }
            ])->first();

        if (empty($note)) {
            return $this->errorJson('日记数据不存在',['status' =>1]);
        }

        //当前用户是否点赞
        $note->is_like = 0;
        $userLike = XiaoeClockNoteLike::where(['clock_users_id' => $note_id,'user_id' => $member_id])->first();
        if(!empty($userLike)){
            $note->is_like = 1;
        }
        //处理图片
        $note->image_desc = json_decode($note->image_desc,true);

        return $this->successJson('打卡日记详情', $note);

    }

    //打卡日记点赞 ims_yz_xiaoe_users_clock_like
    public function clockNoteLike()
    {
        $note_id = request()->get('id');
        $member_id = \YunShop::app()->getMemberId();

        if(!$note_id){
            return $this->errorJson('日记id不能为空');
        }
        
        $params = array(
            'uniacid' => \YunShop::app()->uniacid,
            'user_id' => $member_id,
            'clock_users_id' => $note_id,
        );
        $is_like = XiaoeClockNoteLike::where($params)->first();

        if (!empty($is_like)) {
            XiaoeClockNoteLike::where($params)->delete();
            return $this->successJson('取消点赞成功', ['is_like' => 0]);
        } else {
            XiaoeClockNoteLike::firstOrCreate($params);
            return $this->successJson('点赞成功', ['is_like' => 1]);
        }

    }

    //打卡记录评论/回复 ims_yz_xiaoe_users_clock_comment
    public function clockNoteComment()
    {
        $note_id = request()->get('id');
        $member_id = \YunShop::app()->getMemberId();
        $content = request()->get('content');

        $content = (new Yunshop\Appletslive\common\services\BaseService())->textCheck($content, false);
        $params = array(
            'uniacid' => \YunShop::app()->uniacid,
            'user_id' => $member_id,
            'clock_user_id' => $note_id,
            'content' => $content
        );

        XiaoeClockNoteComment::create($params);

        return $this->successJson('评论成功', $params);

    }

    //打卡记录评论列表 ims_yz_xiaoe_users_clock_comment
    public function clockNoteCommentList()
    {



    }

    //打卡记录评论点赞 ims_yz_xiaoe_clock_users_comment_like
    public function clockNoteCommentLike()
    {
        $comment_id = request()->get('comment_id');
        $member_id = \YunShop::app()->getMemberId();

        $params = array(
            'uniacid' => \YunShop::app()->uniacid,
            'user_id' => $member_id,
            'comment_id' => $comment_id,
        );
        $is_like = XiaoeClockNoteCommentLike::where($params)->first();

        if (!empty($is_like)) {
            XiaoeClockNoteCommentLike::where($params)->delete();
            return $this->successJson('取消点赞', ['is_like' => 0]);
        } else {
            XiaoeClockNoteCommentLike::firstOrCreate($params);
            return $this->successJson('点赞成功', ['is_like' => 1]);
        }
    }

    //打卡记录参与用户ims_yz_xiaoe_clock_users
    public function clockUserList()
    {
        $clock_id = request()->get('clock_id');
        if(!$clock_id){
            return $this->errorJson('打卡id不能为空');
        }
        $user_list = XiaoeClockUser::where('clock_id', $clock_id)->with(['user' => function($user){
            return $user->select('nickname','avatarurl');
        }])->get();

        return $this->successJson('点赞成功', $user_list);
    }



}
