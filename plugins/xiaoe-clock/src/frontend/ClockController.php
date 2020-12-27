<?php


namespace Yunshop\XiaoeClock\frontend;

use app\backend\modules\tracking\models\DiagnosticServiceUser;
use app\common\components\ApiController;
use Yunshop\XiaoeClock\models\XiaoeClock;
use Yunshop\XiaoeClock\models\XiaoeClockTopic;
use Yunshop\XiaoeClock\models\XiaoeClockNote;
use Yunshop\XiaoeClock\models\XiaoeClockNoteLike;
use Yunshop\XiaoeClock\models\XiaoeClockNoteComment;
use Yunshop\XiaoeClock\models\XiaoeClockNoteCommentLike;
use Yunshop\XiaoeClock\models\XiaoeClockUser;
use Illuminate\Support\Facades\DB;
use Yunshop\Appletslive\common\services\BaseService;
use Carbon\Carbon;


class ClockController extends ApiController
{
    private $clockNoteModel;

    private $page;

    private $year;

    private $month;

    public function __construct()
    {
        $member_id = \YunShop::app()->getMemberId();
        if(!$member_id){
            response()->json([
                'result' => 41009,
                'msg' => '请登录',
                'data' => '',
            ], 200, ['charset' => 'utf-8'])->send();
            exit;
        }


        $this->clockNoteModel = $this->getClockNoteModel($member_id);

        $this->page = $this->getPostPage();
        $this->year = $this->getPostYear();
        $this->month = $this->getPostMonth();
    }

    //日历打卡活动详情 ims_yz_xiaoe_clock 默认显示一周的日历 用户一周打卡情况 当天是否打卡 当天的打卡主题
    public function getCalendarClock()
    {
        $id = intval(request()->get('id'));
        if (!$id) {
            return $this->errorJson('打卡id不能为空');
        }
        $member_id = \YunShop::app()->getMemberId();
        //搜集新学员
        $this->userJoin($id, $member_id);


        $clock = XiaoeClock::where('id', $id)->withCount(['hasManyNote', 'hasManyUser'])->with(['hasManyNote', 'hasManyTopic'])->with(['myNote' => function ($my_note) use ($member_id) {
            return $my_note->where('user_id', $member_id);
        }])->first();

        $data = [
            'clock'     => $clock,
            'clock_status'   => $this->getClockStatus($clock->id,$member_id),
            'clock_total'    => $this->clockNoteModel->count() . "天",
            'my_clock_log'      => $this->getCalendarData()
        ];
        return $this->successJson('ok', $data);

        if (!$clock) {
            return $this->errorJson('不存在数据');
        }

        return $this->successJson('success', $clock);
    }


    //作业打卡活动详情
    public function getHomeWorkClock()
    {

        $id = intval(request()->get('id'));
        if (!$id) {
            return $this->errorJson('打卡id不能为空');
        }
        $member_id = \YunShop::app()->getMemberId();
        $this->userJoin($id, $member_id);

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

    //打卡记录评论列表 ims_yz_xiaoe_users_clock_comment
    /*public function clockNoteCommentList()
    {



    }*/

    //创建打卡日记:简单校验了开始结束时间和文本内容校验
    public function clockNoteCreate()
    {
        $clock_id = intval(request()->get('id'));
        $topic_id = intval(request()->get('clock_task_id'));
        if(!$clock_id){
            return $this->errorJson('打卡id不能为空');
        }

        $clockInfo = XiaoeClock::find($clock_id);
        if(empty($clockInfo)){
            return $this->errorJson('打卡不存在或已被删除');
        }


        $time = time();

        if($clockInfo->start_time > $time){
            return $this->errorJson('打卡暂未开始');
        }
        if($clockInfo->end_time < $time){
            return $this->errorJson('打卡已结束');
        }


        $member_id = \YunShop::app()->getMemberId();
        $content = trim(request()->get('content'));
        if(!$content){
            return $this->errorJson('评论内容不能为空');
        }

        //用户今日打卡状态
        $status = $this->getClockStatus($clock_id, $member_id);
        if($status == 1){
            return $this->errorJson('今日已打卡');
        }
        $image = trim(request()->get('image'));
        $video = trim(request()->get('video'));
        $image_size = trim(request()->get('image_size'));
        $video_size = '';



        $this->checkBlack($member_id);

        $wxapp_base_service = new BaseService();
        $sensitive_check = $wxapp_base_service->msgSecCheck($content);
        if (!is_bool($sensitive_check) || $sensitive_check === false) {
            return $this->errorJson('评论内容包含敏感词', $sensitive_check);
        }



        // 组装插入数据
        $content = $wxapp_base_service->textCheck($content, false);
        $params = array(
            'uniacid' => \YunShop::app()->uniacid,
            'user_id' => $member_id,
            'clock_id' => $clock_id,
            'type' => $clockInfo->type,
            'clock_task_id' => $topic_id,
            'text_desc' => $content,
            'image_desc' => $image,
            'video_desc' => $video,
            'image_info' => $image_size,
        );

        XiaoeClockNote::create($params);
        XiaoeClockUser::where(['clock_id' => $clock_id, 'user_id' => $member_id])->increment('clock_num');


        return $this->successJson('打卡成功', $params);

    }

    //打卡日记详情 ims_yz_xiaoe_users_clock
    public function clockNoteInfo()
    {
        $note_id = intval(request()->get('id'));
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
        $note_id = intval(request()->get('id'));
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
        $note_id = intval(request()->get('id'));
        if(!$note_id){
            return $this->errorJson('日记id不能为空');
        }
        $member_id = \YunShop::app()->getMemberId();
        $content = trim(request()->get('content'));
        if(!$content){
            return $this->errorJson('评论内容不能为空');
        }

        $this->checkBlack($member_id);

        $wxapp_base_service = new BaseService();
        $sensitive_check = $wxapp_base_service->msgSecCheck($content);
        if (!is_bool($sensitive_check) || $sensitive_check === false) {
            return $this->errorJson('评论内容包含敏感词', $sensitive_check);
        }

        // 组装插入数据
        $content = $wxapp_base_service->textCheck($content, false);
        $params = array(
            'uniacid' => \YunShop::app()->uniacid,
            'user_id' => $member_id,
            'clock_users_id' => $note_id,
            'content' => $content
        );

        XiaoeClockNoteComment::create($params);

        return $this->successJson('评论成功', $params);

    }

    protected function checkBlack($user_id){
        //用户禁言
        $user = DiagnosticServiceUser::where('ajy_uid', $user_id)->first();
        if ($user->is_black == 1) {
            if ($user->black_end_time > time()) {
                response()->json([
                    'result' => 301,
                    'msg' => '您已被系统禁言！截止时间至：' . date('Y-m-d H:i:s', $user->black_end_time) . '申诉请联系管理员',
                    'data' => false,
                ], 200, ['charset' => 'utf-8'])->send();
                exit;
            } else {
                $user->is_black = 0;
                $user->black_content = '时间到期,自然解禁';
                $user->save();
            }
        }
    }


    //打卡记录评论点赞 ims_yz_xiaoe_clock_users_comment_like
    public function clockNoteCommentLike()
    {
        $comment_id = intval(request()->get('comment_id'));
        $member_id = \YunShop::app()->getMemberId();

        if(!$comment_id){
            return $this->errorJson('评论id不能为空');
        }
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

    /**
     * 日历打卡首页 日历数据重构
     *
     * @return array
     */
    private function getCalendarData()
    {
        $note_log = $this->getClockNoteData();
        !$note_log && $note_log == [];

        $result = [];
        foreach ($note_log as $key => $item) {
            $result[] = (int)date('d', $item->created_at->timestamp) - 1;
        }

        return $result;
    }

    private function getClockNoteModel($user_id)
    {
        return XiaoeClockNote::select('id', 'user_id','clock_id', 'clock_task_id', 'text_desc', 'created_at')->where(['user_id' => $user_id]);
    }

    /**
     * @return mixed
     */
    private function getClockNoteData()
    {
        list($startTime, $endTime) = $this->searchTime();

        return $this->clockNoteModel->whereBetween('created_at', [$startTime, $endTime])
            ->orderBy('created_at', 'desc')
            ->paginate(32, '', '', $this->getPostPage());
    }

    //创建前端传递过来的月份
    private function searchTime()
    {
        $startTime = Carbon::create($this->year, $this->month)->startOfMonth()->timestamp;
        $endTime = Carbon::create($this->year, $this->month)->endOfMonth()->timestamp;

        return [$startTime, $endTime];
    }


    /**
     * 前段提交分页值
     *
     * @return int
     */
    private function getPostPage()
    {
        return \YunShop::request()->page ?: 1;
    }

    /**
     * 前段提交月份值
     *
     * @return int
     */
    private function getPostMonth()
    {
        return (int)\YunShop::request()->month ?: (int)date("m");
    }

    /**
     * 前段提交年份值
     *
     * @return int
     */
    private function getPostYear()
    {
        return (int)\YunShop::request()->year ?: (int)date("Y");
    }

    //打卡记录参与用户ims_yz_xiaoe_clock_users
    protected function userJoin($clock_id, $user_id)
    {
        $params = array(
            'uniacid' => \YunShop::app()->uniacid,
            'clock_id' => $clock_id,
            'user_id' => $user_id
        );

        XiaoeClockUser::firstOrCreate($params);
    }

    //用户今日打卡状态
    private function getClockStatus($clock_id, $user_id)
    {
        $todayStart = Carbon::now()->startOfDay()->timestamp;
        $todayEnd = Carbon::now()->endOfDay()->timestamp;
        $todayNoteLog = XiaoeClockNote::select('id', 'user_id','clock_id', 'clock_task_id', 'text_desc', 'created_at')->where(['clock_id' => $clock_id, 'user_id' => $user_id])->whereBetween('created_at', [$todayStart, $todayEnd])->first();
        if(!empty($todayNoteLog)){
            $status = 1;
        }else{
            $status = 0;
        }
        return $status;
    }



}
