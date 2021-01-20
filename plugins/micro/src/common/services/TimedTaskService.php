<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/19
 * Time: 下午4:21
 */

namespace Yunshop\Micro\common\services;
use app\common\models\Income;
use app\common\models\UniAccount;
use Setting;
use Yunshop\Micro\common\models\MicroShopBonusLog;

class TimedTaskService
{
    public function handle()
    {
        \Log::info("--微店定时任务入口--");
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $this->updateMicroBonus();
        }
    }

    public function updateMicroBonus()
    {
        \Log::info("--微店定时任务执行开始--");
        $set = Setting::get('plugin.micro');
        $refund_days = Setting::get('shop.trade')['refund_days'];
        $time = time() - ($set['cycle'] + $refund_days) * 60 * 60 * 24;
        $logs = MicroShopBonusLog::uniacid()->select()->byOrderStatus(3)->applyStatus(0)->where('complete_time', '<', $time)->get();
        if (!$logs->isEmpty()) {
            \Log::info("--微店定时任务开始--");
            $logs->each(function($log){
                $income_data = [
                    'uniacid'           => \YunShop::app()->uniacid,
                    'member_id'         => $log->member_id,
                    'incometable_type'  => MicroShopBonusLog::class,
                    'incometable_id'    => $log->id,
                    'type_name'         => $log->mode_type,
                    'amount'            => $log->is_lower == 1 ? $log->lower_level_bonus_money : $log->bonus_money,
                    'status'            => 0,
                    'pay_status'        => 0,
                    'detail'            => '',
                    'create_month'      => date('Y-m', time())
                ];
                $incomeModel = new Income();
                $incomeModel->separate = [
                    'mark' => 'micro',
                    'order_sn' => $log['order_sn']
                ];
                $incomeModel->fill($income_data);
                $requestIncome = $incomeModel->save();
//                $result = Income::create($income_data);
                if ($requestIncome) {
                    $log_data = [
                        'apply_status'  => MicroShopBonusLog::APPLY_STATUS_TRUE,
                        'apply_time'    => time()
                    ];
                    MicroShopBonusLog::uniacid()->where('id',$log->id)->update($log_data);
                }
                $bonus_total = $log->is_lower == 1 ? $log->lower_level_bonus_money : $log->bonus_money;
                // todo 分红结算通知
                if ($log->is_lower == 1) {
                    // todo 上级微店分红结算通知
                    MessageService::agentMicroBonusApply($log->member_id, $bonus_total, $log->uniacid);
                } else {
                    // todo 微店分红结算通知
                    MessageService::microBonusApply($log->member_id, $bonus_total, $log->uniacid);
                }
                \Log::info("--微店定时任务结束--");
            });


            /*$log_ids = $logs->pluck('id');
            $log_data = [
                'apply_status'  => MicroShopBonusLog::APPLY_STATUS_TRUE,
                'apply_time'    => time()
            ];
            $result = MicroShopBonusLog::uniacid()->whereIn('id',$log_ids)->update($log_data);
            if ($result) {
                // todo 可以直接使用$logs  不用再次进行查询
                $handle_logs = MicroShopBonusLog::uniacid()->whereIn('id',$log_ids)->get();
                $handle_logs->each(function($log){
                    $income_data = [
                        'uniacid'           => \YunShop::app()->uniacid,
                        'member_id'         => $log->member_id,
                        'incometable_type'  => 'Yunshop\Micro\models\MicroShopBonusLog',
                        'incometable_id'    => $log->id,
                        'type_name'         => $log->mode_type,
                        'amount'            => $log->is_lower == 1 ? $log->lower_level_bonus_money : $log->bonus_money,
                        'status'            => 0,
                        'pay_status'        => 0,
                        'detail'            => '',
                        'create_month'      => date('Y-m', time())
                    ];
                    Income::create($income_data);
                    $bonus_total = $log->is_lower == 1 ? $log->lower_level_bonus_money : $log->bonus_money;
                    // todo 分红结算通知
                    MessageService::bonusApply($log->member_id, $bonus_total);
                });
                \Log::info("--微店定时任务结束--");
            }*/
        }
    }
}