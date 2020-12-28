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


class ClockExtController extends ApiController
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

    private function getClockNoteModel()
    {
        return XiaoeClockNote::select('id', 'user_id', 'clock_id', 'clock_task_id', 'type', 'text_desc', 'image_desc', 'audio_desc', 'video_desc', 'sort', 'created_at')->where('clock_id', $this->clock_id);
    }

    private function getPostDate()
    {
        return \YunShop::request()->date ?: date('Y-m-d');
    }

    //获取日历打卡，一周打卡详情
    public function getCalendarWeekClock()
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
        return $this->successJson('获取成功', $data);
    }

    //获取用户打卡状态
    private function getClockStatus($startTime, $endTime)
    {
        $todayStart = $startTime;
        $todayEnd = $endTime;

        $todayNoteLog = XiaoeClockNote::where(['clock_id'=>$this->clock_id,'user_id'=> $this->member_id])->whereBetween('created_at', [$todayStart, $todayEnd])->first();

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
