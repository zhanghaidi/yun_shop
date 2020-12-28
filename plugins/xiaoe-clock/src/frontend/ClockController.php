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
    private $clock_id;

    private $member_id;

    private $clockNoteModel;

    private $page;

    private $year;

    private $month;

    private $date; // 2020-12-27


    public function __construct()
    {
        if (!\YunShop::app()->getMemberId()) {
            response()->json([
                'result' => 41009,
                'msg' => '请登录',
                'data' => '',
            ], 200, ['charset' => 'utf-8'])->send();
            exit;
        }
        $this->member_id = \YunShop::app()->getMemberId();
        $this->clock_id = intval(request()->get('id'));
        if (!$this->clock_id) {
            return $this->errorJson('打卡id不能为空');
        }

        $this->clockNoteModel = $this->getClockNoteModel();

        $this->date = $this->getPostDate();
    }

    //日历打卡活动详情 ims_yz_xiaoe_clock 默认显示一周的日历 用户一周打卡情况 当天是否打卡 当天的打卡主题
    public function getCalendarClock()
    {

        //搜集新加入此打卡的学员
        $this->userJoin($this->clock_id, $this->member_id);

        $clock = XiaoeClock::where('id', $this->clock_id)
            ->select('id','type','name','cover_img','text_desc','audio_desc','video_desc','join_type','course_id','price','start_time','end_time','valid_time_start',
                'valid_time_end','text_length','image_length','video_length','is_cheat_mode','is_resubmit','created_at')
            ->withCount(['hasManyNote', 'hasManyUser'])
            ->with([
                'hasManyUser' => function($joinUser){
                    return $joinUser->select('clock_id','clock_task_id','type','user_id','created_at')
                        ->with(['user'=>function($user){
                            return $user->select('ajy_uid', 'nickname', 'avatarurl');
                        }]);
                },

                'hasManyNote' => function($note){
                    return $note->select('id', 'user_id', 'clock_id', 'clock_task_id', 'type', 'text_desc', 'image_desc', 'audio_desc', 'video_desc', 'sort','created_at')
                        ->withCount(['hasManyLike'])
                        ->with([
                            'user' => function ($user) {
                                return $user->select('ajy_uid', 'nickname', 'avatarurl');
                            },
                            'joinUser' => function($joinUser) {
                                return $joinUser->select('clock_id','clock_num','user_id')
                                    ->where('clock_id', $this->clock_id);
                            },

                            'topic' => function ($topic) {
                                return $topic->select('id', 'clock_id', 'type', 'name');
                            },
                            'hasManyLike' => function ($like) {
                                return $like->select('clock_users_id', 'user_id', 'created_at')->with([
                                    'user' => function ($user) {
                                        return $user->select('ajy_uid', 'nickname', 'avatarurl');
                                    }
                                ]);
                            },
                            'hasManyComment' => function ($comment) {
                                return $comment->select('id', 'clock_users_id', 'user_id', 'content', 'parent_id', 'is_reply', 'created_at')->with(['user' => function ($user) {
                                    $user->select('ajy_uid', 'nickname', 'avatarurl');
                                }])->orderBy('id', 'desc');
                            },
                        ]);
                },

            ])
            ->first();

        //显示本周本用户打卡状态

        $data = [
            'clock' => $clock,
            'week_calendar' => $this->getCalendarWeekData()
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

        $this->userJoin($this->clock_id, $this->member_id);

        $clock = XiaoeClock::where('id', $id)->withCount(['hasManyNote', 'hasManyUser'])->with(['hasManyNote', 'hasManyTopic'])->with(['myNote' => function ($my_note) {
            return $my_note->where('user_id', $this->member_id);
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
        $topic_id = intval(request()->get('clock_task_id'));

        $clockInfo = XiaoeClock::find($this->clock_id);
        if (empty($clockInfo)) {
            return $this->errorJson('打卡不存在或已被删除');
        }

        $time = time();

        if ($clockInfo->start_time > $time) {
            return $this->errorJson('打卡暂未开始');
        }
        if ($clockInfo->end_time < $time) {
            return $this->errorJson('打卡已结束');
        }


        $member_id = $this->member_id;
        $content = trim(request()->get('content'));
        if (!$content) {
            return $this->errorJson('评论内容不能为空');
        }

        //用户今日打卡状态
        $status = $this->getClockStatus();
        if ($status == 1) {
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
            'clock_id' => $this->clock_id,
            'type' => $clockInfo->type,
            'clock_task_id' => $topic_id,
            'text_desc' => $content,
            'image_desc' => $image,
            'video_desc' => $video,
            'image_info' => $image_size,
        );

        XiaoeClockNote::create($params);
        XiaoeClockUser::where(['clock_id' => $this->clock_id, 'user_id' => $member_id])->increment('clock_num');


        return $this->successJson('打卡成功', $params);

    }

    //打卡日记详情 ims_yz_xiaoe_users_clock
    public function clockNoteInfo()
    {
        $note_id = intval(request()->get('note_id'));
        $member_id = $this->member_id;
        if (!$note_id) {
            return $this->errorJson('日记id不能为空');
        }

        //关联用户打卡天数、关联主题主题参与人数和打卡数 关联评论
        $note = $this->clockNoteModel->where('id', $note_id)
            ->withCount(['hasManyLike', 'hasManyComment'])
            ->with([
                'user' => function ($user) {
                    return $user->select('ajy_uid', 'nickname', 'avatarurl');
                },
                'topic' => function ($topic) {
                    return $topic->select('id', 'clock_id', 'type', 'name');
                },
                'clock' => function ($clock) {
                    return $clock->select('id', 'name', 'cover_img')->withCount(['hasManyUser', 'hasManyNote']);
                },
                'hasManyLike' => function ($like) {
                    return $like->select('clock_users_id', 'user_id', 'created_at')->with([
                        'user' => function ($user) {
                            return $user->select('ajy_uid', 'nickname', 'avatarurl');
                        }
                    ]);
                },
                'hasManyComment' => function ($comment) {
                    return $comment->select('id', 'clock_users_id', 'user_id', 'content', 'parent_id', 'is_reply', 'created_at')->with(['user' => function ($user) {
                        $user->select('ajy_uid', 'nickname', 'avatarurl');
                    }])->orderBy('id', 'desc');
                }
            ])->first();

        if (empty($note)) {
            return $this->errorJson('日记数据不存在', ['status' => 1]);
        }

        //当前用户是否点赞
        $note->is_like = 0;
        $userLike = XiaoeClockNoteLike::where(['clock_users_id' => $note_id, 'user_id' => $member_id])->first();
        if (!empty($userLike)) {
            $note->is_like = 1;
        }

        $note->user->clock_num = XiaoeClockUser::where(['clock_id' => $note->clock_id, 'user_id' => $note->user_id])->value('clock_num'); //用户签到数量

        //$note->image_desc = json_decode($note->image_desc, true); //处理图片

        return $this->successJson('打卡日记详情', $note);

    }

    //打卡日记点赞 ims_yz_xiaoe_users_clock_like
    public function clockNoteLike()
    {
        $note_id = intval(request()->get('note_id'));

        if (!$note_id) {
            return $this->errorJson('日记id不能为空');
        }

        $params = array(
            'uniacid' => \YunShop::app()->uniacid,
            'user_id' => $this->member_id,
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
        $note_id = intval(request()->get('note_id'));
        if (!$note_id) {
            return $this->errorJson('日记id不能为空');
        }
        $member_id = $this->member_id;
        $content = trim(request()->get('content'));
        if (!$content) {
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

    protected function checkBlack($user_id)
    {
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
    /*public function clockNoteCommentLike()
    {
        $comment_id = intval(request()->get('comment_id'));
        $member_id = \YunShop::app()->getMemberId();

        if (!$comment_id) {
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
    }*/

    /**
     * 日历打卡首页 日历数据重构
     *
     * @return array
     */
    private function getCalendarWeekData()
    {
        $week = date('w', time());//周日是零
        $weekname = array('日', '一', '二', '三', '四', '五', '六');
        $data = [];
        for ($i = 0; $i <= 6; $i++) {
            $data[$i]['date'] = date('Y-m-d', strtotime('+' . $i - $week . ' days', time()));
            $data[$i]['week'] = $weekname[$i];
            $startTime = strtotime($data[$i]['date'] . '00:00:00');
            $endTime = strtotime($data[$i]['date'] . '23:59:59');
            if ($startTime <= time()) {
                //用户当天是否打卡
                $data[$i]['status'] = $this->getClockStatus($startTime, $endTime);
                //是否有主题 获取用户主题
                $data[$i]['theme'] = $this->getCalendarClockTheme($startTime);
            } else {
                $data[$i]['status'] = 0;
                $data[$i]['theme'] = null;
            }
        }

        return $data;
    }

    private function getClockNoteModel()
    {
        return XiaoeClockNote::select('id', 'user_id', 'clock_id', 'clock_task_id', 'type', 'text_desc', 'image_desc', 'audio_desc', 'video_desc', 'sort','created_at')->where('clock_id', $this->clock_id);

    }

    /**
     * @return mixed
     */
    private function getClockNoteData()
    {
        list($startTime, $endTime) = $this->searchTime();

        return $this->clockNoteModel->where('user_id', $this->member_id)->whereBetween('created_at', [$startTime, $endTime])
            ->orderBy('created_at', 'desc')
            ->paginate(32, '', '', $this->getPostPage());
    }

    //按月创建
    private function searchTimeByMonth()
    {
        $startTime = Carbon::create($this->date)->startOfMonth()->timestamp;
        $endTime = Carbon::create($this->date)->endOfMonth()->timestamp;

        return [$startTime, $endTime];
    }


    private function getPostDate()
    {
        return \YunShop::request()->date ?: date('Y-m-d');
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

    //用户今天打卡状态
    private function getClockStatus()
    {
        $todayStart = Carbon::now()->startOfDay()->timestamp;
        $todayEnd = Carbon::now()->endOfDay()->timestamp;
        $todayNoteLog = $this->clockNoteModel->where('user_id', $this->member_id)->whereBetween('created_at', [$todayStart, $todayEnd])->first();

        if (!empty($todayNoteLog)) {
            $status = 1;
        } else {
            $status = 0;
        }
        return $status;
    }

    //    获取用户主题
    private function getCalendarClockTheme($toDayTime)
    {
        $topic = XiaoeClockTopic::where(['start_time' => $toDayTime, 'clock_id' => $this->clock_id])
            ->select('name', 'cover_img', 'text_desc', 'video_desc', 'join_num', 'comment_num')
            ->first();
        return $topic;
    }

}
