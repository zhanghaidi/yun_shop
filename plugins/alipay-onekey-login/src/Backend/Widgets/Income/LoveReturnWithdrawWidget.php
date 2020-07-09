<?php
namespace Yunshop\Love\Backend\Widgets\Income;


/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/1
 * Time: 下午3:32
 */
use app\common\components\Widget;
use app\common\facades\Setting;

class LoveReturnWithdrawWidget extends Widget
{
    public function run()
    {
        $set = Setting::get('withdraw.loveReturn', ['roll_out_limit' => '', 'poundage_rate' => '']);
        return view('Yunshop\Love::Backend.Widgets.love-return', [
            'set' => $set,
        ])->render();
    }
}