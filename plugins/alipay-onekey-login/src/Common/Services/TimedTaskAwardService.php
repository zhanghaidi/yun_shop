<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/17 上午9:35
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;


use app\common\facades\Setting;
use app\common\models\UniAccount;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\Love\Common\Jobs\addCommissionAwardJob;
use Yunshop\Love\Common\Models\CommissionAgent;

class TimedTaskAwardService
{
    use DispatchesJobs;
    protected $set;
    protected $setLog;


    public function handle()
    {
        set_time_limit(0);
        $uniAccount = UniAccount::getEnable();
        \Log::info('分销下线奖励');
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;

            $this->set = SetService::getLoveSet();
            $this->setLog = Setting::get('plugin.commission_award_log');

            $this->handleCommissionAward();
        }
    }


    public function handleCommissionAward()
    {

        if (!\YunShop::plugin()->get('commission')) {
            return;
        }
        if (!$this->set || !$this->set['commission_award']) {
            return;
        }
        if ($this->set['commission_every_day'] != date('H')) {
            return;
        }
        \Log::info('奖励时间:' . $this->set['commission_every_day'] . '点');

        if ($this->setLog['commission_current_d'] == date('d')) {
            \Log::info('UNIACID:' . \YunShop::app()->uniacid . ' - ' . date('d') . '号已奖励,当前不可奖励');
            return;
        }
        //设置当前返现日期
        $this->setLog['commission_current_d'] = date('d');
        Setting::set('plugin.commission_award_log', $this->setLog);
        //统计分销下线奖励
        $this->setStatisticsAward();
    }

    public function setStatisticsAward()
    {
        $award = $this->set['commission_award_proportion'];
        if(!$award){
            return;
        }
        $commissionAgents = CommissionAgent::getAgents()->get();
        foreach ($commissionAgents as $item) {
            $count = CommissionAgent::getAgentCount($item->member_id);
            if ($count > 0) {
                $loveGive = $award * $count;
                //设置奖励
                $this->dispatch(new addCommissionAwardJob($item->member_id, $loveGive, \YunShop::app()->uniacid));
            }
        }

    }

    public function setCommissionAward($memberId, $loveGive, $uniacId)
    {
        \YunShop::app()->uniacid = $uniacId;
        //分销下线奖励爱心值
        $result = (new LoveChangeService(SetService::getAwardType()))->commissionAward($this->getCommissionRecordData($loveGive, $memberId));

    }

    private function getCommissionRecordData($changeValue, $memberId = null)
    {

        return [
            'member_id' => $memberId,
            'change_value' => $changeValue,
            'operator' => ConstService::OPERATOR_SHOP,
            'operator_id' => 0,
            'remark' => '分销下线奖励' . $changeValue,
            'relation' => ''
        ];
    }


}
