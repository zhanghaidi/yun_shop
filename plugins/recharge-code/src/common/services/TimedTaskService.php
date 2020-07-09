<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/2
 * Time: 下午1:05
 */

namespace Yunshop\RechargeCode\common\services;

use app\common\models\UniAccount;
use Yunshop\RechargeCode\common\models\RechargeCode;


class TimedTaskService
{
    public function handle()
    {
        $uniAccount = UniAccount::get();
        $this->everyoneUniacid($uniAccount);
    }

    public function everyoneUniacid($uniAccount)
    {
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;
            $this->expired();
        }
    }

    public function expired()
    {
        $recharge_codes = RechargeCode::fetchCodesByEndTimeAndStatus(time(), 0)->get();
        if ($recharge_codes->isEmpty()) {
            return;
        }
        $code_ids = $recharge_codes->pluck('id');
        RechargeCode::updateStatusByIds($code_ids);
    }
}