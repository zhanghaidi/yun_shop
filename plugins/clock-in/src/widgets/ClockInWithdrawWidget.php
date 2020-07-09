<?php
namespace Yunshop\ClockIn\widgets;
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/10/12
 * Time:09:53
 */

use app\common\components\Widget;
use app\common\facades\Setting;

class ClockInWithdrawWidget extends Widget
{
    public function run()
    {
        $set = Setting::get('withdraw.clockIn', ['roll_out_limit' => '', 'poundage_rate' => '']);

        return view('Yunshop\ClockIn::admin.withdraw-set', [
            'set' => $set,
        ])->render();
    }
}