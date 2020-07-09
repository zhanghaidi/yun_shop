<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/28 下午2:49
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Widgets\Income;


use app\common\components\Widget;
use app\common\facades\Setting;

class LoveWithdrawWidget extends Widget
{
    public function run()
    {
        $set = Setting::get('withdraw.loveWithdraw', ['roll_out_limit' => '', 'poundage_rate' => '']);

        return view('Yunshop\Love::Backend.Widgets.love_withdraw', [
            'set' => $set,
        ])->render();
    }

}
