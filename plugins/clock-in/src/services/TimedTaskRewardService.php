<?php

namespace Yunshop\ClockIn\services;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/09
 * Time: 下午5:50
 */

use app\common\facades\Setting;
use app\common\models\Income;
use app\common\models\UniAccount;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;
use Yunshop\Article\models\Log;
use Yunshop\ClockIn\jobs\addClockInRewardJob;
use Yunshop\ClockIn\models\ClockPayLogModel;
use Yunshop\ClockIn\models\ClockQueueModel;
use Yunshop\ClockIn\models\ClockRewardLogModel;

class TimedTaskRewardService
{
    use DispatchesJobs;
    public $set;
    public $setLog;
    public $amount;
    public $topThreeMemberId;

    /**
     * 早起打卡奖励发放处理入口
     */
    public function handle()
    {
        set_time_limit(0);
        $uniAccount = UniAccount::getEnable();
        \Log::info('早起打卡奖励发放处理');
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;

            $clockInService = new ClockInService();

            $this->set = Setting::get('plugin.clock_in');
            $this->setLog = Setting::get('plugin.clock_in_log');
            $statisticsReward = $this->_runStatisticsReward();
            if ($statisticsReward) {
                $this->_grantReward($statisticsReward);
            }
        }
    }

    /**
     * 奖励统计入口
     */
    public function _runStatisticsReward()
    {
        \Log::info('奖励统计入口');
        if (!$this->set['is_clock_in']) {
            return false;
        }
        if (!$this->validateStatisticsTime()) {
            return false;
        }

        if (!$this->validateStatisticsLog()) {
            return false;
        }

        return $this->runStatisticsReward();
    }

    /**
     * @return bool
     */
    public function validateStatisticsTime()
    {
        // 打卡结束后 30分钟进行统计
        $target = strtotime(date('Y-m-d ' . $this->set['endtime'] . ':19:00'));

        if (time() < $target) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function validateStatisticsLog()
    {
        if ($this->setLog['statistics_current_d'] == date('d')) {
            return false;
        }

        //设置当前返现日期
        $this->setLog['statistics_current_d'] = date('d');
        Setting::set('plugin.clock_in_log', $this->setLog);

        return true;
    }

    /**
     * @return bool|void
     */
    public function runStatisticsReward()
    {
        $yesterday = strtotime(date("Y-m-d", strtotime("-1 day")));//昨天
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        if (!$this->validateStatisticsQueue($today, $current)) {
            return false; //今天已统计
        }

        $queueId = $this->addClockInQueueData(); // 添加统计队列 return queueu_id

        $this->updatedPayLog($yesterday, $today, $queueId); // 更新支付记录关联队列ID

        \Log::info('统计完成 时间：' . date("Y-m-d H:i:s"));

        return $queueId;
    }

    /**
     * @param $yesterday
     * @param $today
     * @param $queueId
     * @return mixed
     */
    public function updatedPayLog($yesterday, $today, $queueId)
    {
        return ClockPayLogModel::updatedPayLog($yesterday, $today, $queueId);
    }

    /**
     * @return mixed
     */
    public function addClockInQueueData()
    {
        $queueData = $this->getQueueData();

        return ClockQueueModel::insertGetId($queueData);
    }

    /**
     * @return array
     */
    public function getQueueData()
    {
        $yesterday = strtotime(date("Y-m-d", strtotime("-1 day")));//昨天
        $today = strtotime(date("Y-m-d")); //今天

        return $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'day_before_amount' => ClockPayLogModel::getStatistic($yesterday, $today)->sum('amount'), // 前一天奖金池总额
            'rate' => $this->set['rate'], // 奖金发放比例
            'amount' => sprintf("%.2f", ClockPayLogModel::getStatistic($yesterday, $today)->sum('amount') / 100 * $this->set['rate']), // 总发放金额
            'pay_num' => ClockPayLogModel::getStatistic($yesterday, $today)->count('id'), // 前一天支付人数
            'clock_in_num' => ClockPayLogModel::getStatistic($yesterday, $today)->where('clock_in_status', 1)->count('id'), // 打卡人数
            'not_clock_in_num' => ClockPayLogModel::getStatistic($yesterday, $today)->where('clock_in_status', 0)->count('id'), // 未打卡人数
            'created_at' => time(),
        ];
    }

    /**
     * @param $today
     * @param $current
     * @return bool
     */
    public function validateStatisticsQueue($today, $current)
    {
        $queue = ClockQueueModel::getStatistic($today, $current)->first();
        if ($queue) {
            return false;
        }
        return true;
    }

    /**
     * @param $queueId
     */
    public function _grantReward($queueId)
    {
        \Log::info('发放奖励入口');
        $queue = ClockQueueModel::find($queueId)->toArray();

        $this->runTopThreeReward($queue); // 前三名处理

        if ($this->amount <= 0) {
            return;
        }
        $this->runPartitionReward($this->amount); // 瓜分处理
    }

    /**
     * @param $queue
     * 前三名奖励处理
     */
    public function runTopThreeReward($queue)
    {
        $this->amount = $queue['amount'];
        $this->runFirstReward();
        $this->runSecondReward();
        $this->runThirdReward();
    }

    /**
     *
     */
    public function runFirstReward()
    {
        if ($this->amount <= 0) {
            return;
        }
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        $clockLog = ClockPayLogModel::getClockInLog($today, $current)->where('clock_in_status', 1)->orderBy('clock_in_at', 'asc')->offset(0)->limit(1)->first(); //
        if (!$clockLog) {
            return;
        }
        $amount = $this->set['first'] > $this->amount ? $this->amount : $this->set['first'];
        if ($amount <= 0) {
            return;
        }
        $this->amount -= $amount;
        $this->topThreeMemberId['0'] = $clockLog->hasOneMember->uid; // 第一名打卡会员ID

        $rewardData = $this->getRewardData($clockLog->hasOneMember->uid, $amount, $clockLog->id);

        $this->addClockRewardLog($rewardData);
    }

    /**
     *
     */
    public function runSecondReward()
    {
        if ($this->amount <= 0) {
            return;
        }
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        $clockLog = ClockPayLogModel::getClockInLog($today, $current)->where('clock_in_status', 1)->orderBy('clock_in_at', 'asc')->offset(1)->limit(1)->first(); //
        if (!$clockLog) {
            return;
        }
        $amount = $this->set['second'] > $this->amount ? $this->amount : $this->set['second'];
        if ($amount <= 0) {
            return;
        }
        $this->amount -= $amount;
        $this->topThreeMemberId['1'] = $clockLog->hasOneMember->uid;

        $rewardData = $this->getRewardData($clockLog->hasOneMember->uid, $amount, $clockLog->id);

        $this->addClockRewardLog($rewardData);
    }

    /**
     *
     */
    public function runThirdReward()
    {
        if ($this->amount <= 0) {
            return;
        }
        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        $clockLog = ClockPayLogModel::getClockInLog($today, $current)->where('clock_in_status', 1)->orderBy('clock_in_at', 'asc')->offset(2)->limit(1)->first(); //
        if (!$clockLog) {
            return;
        }
        $amount = $this->set['third'] > $this->amount ? $this->amount : $this->set['third'];
        if ($amount <= 0) {
            return;
        }
        $this->amount -= $amount;
        $this->topThreeMemberId['2'] = $clockLog->hasOneMember->uid;

        $rewardData = $this->getRewardData($clockLog->hasOneMember->uid, $amount, $clockLog->id);

        $this->addClockRewardLog($rewardData);
    }

    /**
     * @param $amount
     */
    public function runPartitionReward($amount)
    {

        $today = strtotime(date("Y-m-d")); //今天
        $current = strtotime(date("Y-m-d H:i:s"));//当前时间
        $clockLog = ClockPayLogModel::getClockInLog($today, $current)->where('clock_in_status', 1)->whereNotIn('member_id', $this->topThreeMemberId)->orderBy('clock_in_at', 'asc')->get(); //
        if (!$clockLog) {
            return;
        }

        $clockInService = new ClockInService();
        $rewardPartitionData = $clockInService->randAmount($amount, count($clockLog));
        if (!$rewardPartitionData['status']) {
            return;
        }
        $beans = $rewardPartitionData['beans']; // 随机瓜分结果

        foreach ($clockLog as $key => $item) {

            $rewardAmount = $beans[$key];
            if ($rewardAmount <= 0) {
                continue;
            }
            $rewardData = $this->getRewardData($item->hasOneMember->uid, $rewardAmount, $item->id);
            $this->dispatch((new addClockInRewardJob($rewardData)));
//            $this->addClockRewardLog($rewardData); // todo 队列执行
        }
    }

    /**
     * @param $member_id
     * @param $rewardAmount
     * @param $payId
     * @return array
     */
    public function getRewardData($member_id, $rewardAmount, $payId)
    {
        return $rewardData = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $member_id,
            'amount' => $rewardAmount,
            'status' => 1,
            'pay_id' => $payId,
            'created_at' => time(),
            'updated_at' => time(),
        ];
    }

    /**
     * @param $rewardData
     */
    public function addClockRewardLog($rewardData)
    {
        $rewardId = ClockRewardLogModel::insertGetId($rewardData);
        \Log::info('rewardData:',$rewardData);
        $this->addIncome($rewardData, $rewardId);
    }

    /**
     * @param $rewardData
     * @param $rewardId
     */
    public function addIncome($rewardData, $rewardId)
    {
        $config = \app\backend\modules\income\Income::current()->getItem('clockIn');

        //收入数据
        $incomeData = [
            'uniacid' => $rewardData['uniacid'],
            'member_id' => $rewardData['member_id'],
            'incometable_type' => $config['class'],
            'incometable_id' => $rewardId,
            'type_name' => $config['title'],
            'amount' => $rewardData['amount'],
            'status' => '0',
            'detail' => '',
            'create_month' => date("Y-m"),
            'created_at' => time(),
            'updated_at' => time()
        ];
        \Log::info('$incomeData',$incomeData);

        //插入收入
        Income::insert($incomeData);
//        $incomeModel = new Income();
//        $incomeModel->setRawAttributes($incomeData);
//        $requestIncome = $incomeModel->save();
    }

}