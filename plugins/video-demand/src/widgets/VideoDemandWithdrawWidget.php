<?php
namespace Yunshop\VideoDemand\widgets;


use app\common\components\Widget;
use app\common\facades\Setting;

class VideoDemandWithdrawWidget extends Widget
{
    public function run()
    {
        $set = Setting::get('withdraw.videoDemand', ['roll_out_limit' => '', 'poundage_rate' => '']);
        return view('Yunshop\VideoDemand::admin.withdraw-set', [
            'set' => $set,
        ])->render();
    }
}