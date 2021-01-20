<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/20
 * Time: 下午3:04
 */

namespace Yunshop\Micro\widgets;

use app\common\components\Widget;
use Setting;

class MicroWithdrawWidget extends Widget
{
    public function run()
    {
        $set = Setting::get('withdraw.micro', ['roll_out_limit' => '', 'poundage_rate' => '']);
        return view('Yunshop\Micro::backend.Withdraw.set', [
            'set' => $set,
        ])->render();
    }
}