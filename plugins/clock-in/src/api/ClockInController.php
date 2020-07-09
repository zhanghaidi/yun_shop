<?php

namespace Yunshop\ClockIn\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use Yunshop\ClockIn\models\ClockContinuityModel;
use Yunshop\ClockIn\models\ClockPayLogModel;
use Yunshop\ClockIn\models\ClockRewardLogModel;
use Yunshop\ClockIn\services\ClockInService;
use Yunshop\ClockIn\models\ClockRuleModel;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/16
 * Time: 下午2:29
 */
class ClockInController extends ApiController
{
    public $_set;
    public $_clockInService;
    public $_pluginName;

    public function preAction()
    {
        parent::preAction();

        $this->_clockInService = new ClockInService();
        $this->_pluginName = $this->_clockInService->get('plugin_name');
        $this->_set = Setting::get('plugin.clock_in');

    }

    /**
     * 打卡规则获取
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    public function ruleContent()
    {
        $clockRule = new ClockRuleModel();

        if (!isset($this->_set['content'])) {
            $this->_set['content'] = $clockRule->getRule()->rule_content;
        }

        return $this->_set['content'];
    }

    public function getSet()
    {
        //plugin.clock-in.api.clock-in.get-set
//        dd($this->_set);
        if ($this->_set['is_clock_in']) {
            $share_title = $this->_set['share_title'] ?: \Setting::get('shop')['name'];
            $clock_time = [
                'starttime' => $this->_set['starttime'],
                'endtime' => $this->_set['endtime'],
            ];
            $share = [
                'share_title' => $share_title,
                'share_icon'  => yz_tomedia($this->_set['share_icon']),
                'share_desc'  => $this->_set['share_desc']
            ];
            return $this->successJson('启用', ['is_clock_in' => $this->_set['is_clock_in'], 'plugin_name' => $this->_pluginName, 'share' => $share, 'clock_time' => $clock_time]);
        }
        return $this->errorJson('禁用!', ['is_clock_in' => $this->_set['is_clock_in'], 'plugin_name' => $this->_pluginName]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 今日支付数据接口
     */
    public function getTodayPayData()
    {
        //plugin.clock-in.api.clock-in.get-today-pay-data
        $data = [
            'today_pay_amount' => $this->getTodayPayAmount(),
            'today_pay_num' => $this->getTodayPayNum(),
            'today_pay_member' => $this->getTodayPayMember(),
        ];

        if ($data) {
            return $this->successJson('成功', $data);
        }
        return $this->errorJson('未检测到数据!', $data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 打卡规则
     */
    public function getRule()
    {
        //plugin.clock-in.api.clock-in.get-rule
        if ($this->ruleContent()) {
            return $this->successJson('成功', ['rule' => html_entity_decode($this->_set['content'])]);
        }
        return $this->errorJson('禁用!', ['rule' => html_entity_decode($this->_set['content'])]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 今日打卡数据接口
     */
    public function getTodayClockData()
    {
        //plugin.clock-in.api.clock-in.get-today-clock-data
        $data = [
            'clock_in_num' => $this->getClockInNum(1),
            'not_clock_in_num' => $this->getClockInNum(0),
            'clock_firat_member' => $this->getClockFirstMember(),
            'reward_lucky_member' => $this->getRewardLuckyMember(),
            'clock_continuity_member' => $this->getClockContinuityMember(),
        ];

        if ($data) {
            return $this->successJson('成功', $data);
        }
        return $this->errorJson('未检测到数据!', $data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 当前支付打卡状态
     * return
     * -1 前一天未支付->支付; 显示 支付
     * 0 今日未支付->支付; 显示 支付
     * 1 前一天已支付->倒计时; 显示 倒计时
     * 2 前一天已支付->打卡进行中->等待打卡; 显示 打卡
     * 3 前一天已支付->打卡完成 --- 继续支付->今日已支付->倒计时; 显示 倒计时
     * 4 今日已支付->倒计时; 显示 倒计时
     */
    public function getMemberPayStatus()
    {
        //plugin.clock-in.api.clock-in.get-member-pay-status

        $data = [];
        switch ($this->getValidateTime()) {
            case 0:
                $data = $this->clockInBefore();
                break;
            case 1:
                $data = $this->clockInUnderway();
                break;
            case 2:
                $data = $this->clockInAfter();
                break;
        }
        if ($data) {
            $data['amount'] = $this->_set['amount'];
            return $this->successJson('成功', $data);
        }
        return $this->errorJson('未检测到数据!', $data);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 打卡
     */
    public function runClockIn()
    {
        //plugin.clock-in.api.clock-in.run-clock-in
        $data = [];

        $yesterday = strtotime(date("Y-m-d", strtotime("-1 day")));//昨天
        $today = strtotime(date("Y-m-d")); //今天
        $memberPayLog = $this->getPayStatus($yesterday, $today);

        if (!$memberPayLog || $memberPayLog->clock_in_status == 1) {
            return $this->errorJson('您还没有支付或已经打卡！', []);
        }

        $request = $this->updetadClockInStatus($memberPayLog);
        if ($request) {
            $clock_continuity_request = $this->updetadClockInContinuity($memberPayLog);
        }

        $data = [
            'clock_in_num' => $this->getClockInNum(1),
            'not_clock_in_num' => $this->getClockInNum(0),
            'clock_continuity_member' => $this->getClockContinuityMember(),
        ];

        if ($clock_continuity_request) {
            return $this->successJson('打卡成功', $data);
        }
        return $this->errorJson('打卡失败', $data);
    }


    /**
     * @return mixed
     * 今日支付金额
     */
    public function getTodayPayAmount()
    {
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        $payAmount = ClockPayLogModel::getStatistic($today, $current)->sum('amount');
        return $payAmount;
    }

    /**
     * @return mixed
     * 今日支付人数
     */
    public function getTodayPayNum()
    {
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        $payNum = ClockPayLogModel::getStatistic($today, $current)->count('id');
        return $payNum;
    }

    /**
     * @return mixed
     * 今日支付会员
     */
    public function getTodayPayMember()
    {
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        $payMember = ClockPayLogModel::getStatistic($today, $current)
            ->select('id', 'member_id')
            ->with(['hasOneMember' => function ($query) {
                return $query->select('uid', 'avatar');
            }])
            ->get();
        return $payMember;
    }

    /**
     * @param $status
     * @return mixed
     * 今日打卡数量
     */
    public function getClockInNum($status)
    {
        $yesterday = strtotime(date("Y-m-d", strtotime("-1 day")));//昨天
        $today = strtotime(date("Y-m-d")); //今天
        $clockNum = ClockPayLogModel::getStatistic($yesterday, $today)->where('clock_in_status', $status)->count('id');
        return $clockNum;
    }

    /**
     * @return mixed
     * 第一名打卡会员
     */
    public function getClockFirstMember()
    {
        $yesterday = strtotime(date("Y-m-d", strtotime("-1 day")));//昨天
        $today = strtotime(date("Y-m-d")); //今天
        $clockFrstMember = ClockPayLogModel::getStatistic($yesterday, $today)
            ->select('id', 'member_id', 'clock_in_at')
            ->with(['hasOneMember' => function ($query) {
                return $query->select('uid', 'nickname', 'avatar');
            }])
            ->where('clock_in_status', 1)
            ->orderBy('clock_in_at', 'asc')
            ->offset(0)
            ->limit(1)
            ->first();
        if ($clockFrstMember) {
            $clockFrstMember->clock_in_at = date('H:i:s', $clockFrstMember->clock_in_at);
        }
        return $clockFrstMember;
    }

    /**
     * @return mixed
     * 手气最佳会员
     */
    public function getRewardLuckyMember()
    {
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        $rewardLuckyMember = ClockRewardLogModel::getRewardByTime($today, $current)
            ->select('id', 'member_id', 'amount')
            ->with(['hasOneMember' => function ($query) {
                return $query->select('uid', 'nickname', 'avatar');
            }])
            ->orderBy('amount', 'desc')
            ->offset(0)
            ->limit(1)
            ->first();
        return $rewardLuckyMember;
    }

    /**
     * @return mixed
     * 连续打卡会员
     */
    public function getClockContinuityMember()
    {
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间

        $clockNum = ClockContinuityModel::getClockNum($today, $current)
            ->select('id', 'member_id', 'clock_num')
            ->with(['hasOneMember' => function ($query) {
                return $query->select('uid', 'nickname', 'avatar');
            }])
            ->orderBy('clock_num', 'desc')
            ->orderBy('updated_at', 'asc')
            ->first();
        return $clockNum;
    }

    /**
     * @return int
     * 验证时间
     */
    public function getValidateTime()
    {
        $times = time();
        $starttime = strtotime(date("Y-m-d " . $this->_set['starttime'] . ":00:00"));
        $endtime = strtotime(date("Y-m-d " . $this->_set['endtime'] . ":00:00"));
        if ($times < $starttime) {
            return 0;
        } elseif ($times >= $starttime && $times < $endtime) {
            return 1;
        } elseif ($times >= $endtime) {
            return 2;
        }
    }

    /**
     * @return array
     * 打卡前
     */
    public function clockInBefore()
    {
        $yesterday = strtotime(date("Y-m-d", strtotime("-1 day")));//昨天
        $today = strtotime(date("Y-m-d")); //今天
        $memberPayLog = $this->getPayStatus($yesterday, $today);
        if ($memberPayLog) {
            $data = [
                'status' => 1,
                'current_time' => time(),
                'start_time' => strtotime(date("Y-m-d " . $this->_set['starttime'] . ":00:00")),
                'message' => '打卡倒计时',
            ];
        } else {

            $data = $this->todayPayStatus();
        }
        return $data;

    }

    /**
     * @return array
     * 打卡中
     */
    public function clockInUnderway()
    {
        $yesterday = strtotime(date("Y-m-d", strtotime("-1 day")));//昨天
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间

        $memberPayLog = $this->getPayStatus($yesterday, $today);

        if ($memberPayLog && $memberPayLog['clock_in_status'] == 0) {
            $data = [
                'status' => 2,
                'message' => '可以打卡',
            ];
        } else {
            $data = $this->todayPayStatus();
            //if ($memberPayLog && $memberPayLog['clock_in_status'] == 1)
            // 已打卡 或昨天 未支付
//            $todayMemberPayLog = $this->getPayStatus($today, $current);
//            if ($todayMemberPayLog) {
//                $data = [
//                    'status' => 3,
//                    'current_time' => time(),
//                    'start_time' => strtotime(date("Y-m-d " . $this->_set['starttime'] . ":00:00", strtotime("+1 day"))),
//                    'message' => '今日已支付',
//                ];
//            } else {
//                $data = ['status' => 0, 'message' => '今日未支付 - 支付'];
//            }
//            $data = [
//                'status' => 3,
//                'message' => '已打卡',
//            ];
        }
//        else {
//
//            $data = ['status' => 0, 'message' => '前一天未支付 - 支付'];
//        }
        return $data;
    }

    /**
     * @return array
     * 打卡后
     */
    public function clockInAfter()
    {
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        $memberPayLog = $this->getPayStatus($today, $current);

        if ($memberPayLog) {
            $data = [
                'status' => 4,
                'current_time' => time(),
                'start_time' => strtotime(date("Y-m-d " . $this->_set['starttime'] . ":00:00", strtotime("+1 day"))),
                'message' => '今日已支付',
            ];
        } else {
            $data = ['status' => 0, 'message' => '今日未支付 - 支付'];
        }
        return $data;
    }


    public function todayPayStatus()
    {
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        $todayMemberPayLog = $this->getPayStatus($today, $current);
        if ($todayMemberPayLog) {
            $data = [
                'status' => 3,
                'current_time' => time(),
                'start_time' => strtotime(date("Y-m-d " . $this->_set['starttime'] . ":00:00", strtotime("+1 day"))),
                'message' => '今日已支付',
            ];
        } else {
            $data = ['status' => 0, 'message' => '今日未支付 - 支付'];
        }
        return $data;
    }

    /**
     * @param $start
     * @param $end
     * @return mixed
     * 支付记录
     */
    public function getPayStatus($start, $end)
    {
        $member_id = \YunShop::app()->getMemberId();

        $memberPayLog = ClockPayLogModel::getStatistic($start, $end)
            ->select('id', 'member_id', 'amount', 'pay_status', 'clock_in_status', 'clock_in_at')
            ->with(['hasOneMember' => function ($query) {
                return $query->select('uid', 'nickname', 'avatar');
            }])
            ->where('member_id', $member_id)
            ->first();

        return $memberPayLog;
    }

    /**
     * @param $memberPayLog
     * @return mixed
     * 修改打卡状态
     */
    public function updetadClockInStatus($memberPayLog)
    {
        return $request = ClockPayLogModel::where('id', $memberPayLog->id)
            ->update([
                'clock_in_status' => 1,
                'clock_in_at' => time()
            ]);
    }

    /**
     * @param $memberPayLog
     * @return mixed
     * 修改连续打卡状态
     */
    public function updetadClockInContinuity($memberPayLog)
    {
        $member_id = \YunShop::app()->getMemberId();
        $clock_continuity = ClockContinuityModel::getClockByMemberId($member_id)->first();
        if ($clock_continuity) {
            $request = $this->updetadClockContinuityStatus($clock_continuity);
        } else {
            $request = $this->addClockContinuityStatus($member_id);
        }
        return $request;
    }

    /**
     * @param $clock_continuity
     * @return mixed
     * 修改连续打卡状态数据
     */
    public function updetadClockContinuityStatus($clock_continuity)
    {
        $yesterday = strtotime(date("Y-m-d", strtotime("-1 day")));//昨天
        $today = strtotime(date("Y-m-d")); //今天
        if ($clock_continuity->last_clock_at >= $yesterday && $clock_continuity->last_clock_at <= $today) {
            $clockNum = $clock_continuity->clock_num + 1;
        } else {
            $clockNum = 1;
        }
        $data = [
            'clock_num' => $clockNum,
            'last_clock_at' => time(),
            'updated_at' => time(),
        ];
        return $request = ClockContinuityModel::where('id', $clock_continuity->id)
            ->update($data);
    }

    /**
     * @param $member_id
     * @return mixed
     * 增加连续打卡状态数据
     */
    public function addClockContinuityStatus($member_id)
    {
        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $member_id,
            'clock_num' => 1,
            'last_clock_at' => time(),
            'created_at' => time(),
            'updated_at' => time(),
        ];
        return $request = ClockContinuityModel::insert($data);
    }

}