<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/17 上午9:35
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Services;


use app\backend\modules\finance\models\Income;
use app\common\facades\Setting;
use app\common\models\UniAccount;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\Love\Common\Jobs\addLoveReturnLogJob;
use Yunshop\Love\Common\Jobs\LoveBalanceJob;
use Yunshop\Love\Common\Jobs\LoveRecyclJob;
use Yunshop\Love\Common\Jobs\TimingRechargeJob;
use Yunshop\Love\Common\Models\LoveReturnLogModel;
use Yunshop\Love\Common\Models\LoveTimingQueueModel;
use Yunshop\Love\Common\Models\LoveTradingModel;
use Yunshop\Love\Common\Models\MemberLove;

class TimedTaskRechargeService
{
    use DispatchesJobs;
    protected $setLog;
    protected $loveName;

    public function __construct()
    {
        $this->loveName = \Yunshop\Love\Common\Services\SetService::getLoveName();
    }


    public function handle()
    {
        set_time_limit(0);
        $uniAccount = UniAccount::getEnable();
        \Log::info($this->loveName . '定时充值');
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;

            $this->setLog = Setting::get('plugin.timing_recharge_log');
            $this->setRecharge();

        }
    }

    /**
     *  定时充
     */
    public function setRecharge()
    {
        $queues = LoveTimingQueueModel::getRechargeQueue()->get();

        foreach ($queues as $item) {
            $changeValue = sprintf('%.2f', $item->timing_rate * ($item->change_value / 100));

            $rechargeData = [
                'id' => $item->id,
                'uniacid' => $item->uniacid,
                'member_id' => $item->member_id,
                'change_value' => $changeValue,
                'recharge_sn' => $item->recharge_sn
            ];

            $this->dispatch((new TimingRechargeJob($rechargeData)));

        }

    }

    /**
     * @param $rechargeData
     * 更新队列
     */
    public function updateRecharge($rechargeData)
    {
        LoveTimingQueueModel::where('id', $rechargeData['id'])->update(['status' => '1']);
    }

    /**
     * @param $rechargeData
     * 充值
     */
    public function addRecharge($rechargeData)
    {
        \YunShop::app()->uniacid = $rechargeData['uniacid'];
        Setting::$uniqueAccountId = $rechargeData['uniacid'];

        $loveData = [
            'member_id' => $rechargeData['member_id'],
            'change_value' => $rechargeData['change_value'],
            'operator' => ConstService::OPERATOR_SHOP,
            'operator_id' => '0',
            'remark' => $this->loveName . '定时充值' . $rechargeData['change_value'] . '定时充编号：' . $rechargeData['recharge_sn'],
            'relation' => ''
        ];

        (new LoveChangeService('usable'))->recharge($loveData);

    }


}
