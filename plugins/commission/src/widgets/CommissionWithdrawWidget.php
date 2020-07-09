<?php
namespace Yunshop\Commission\widgets;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/18
 * Time: 下午5:36
 */

use app\common\components\Widget;
use app\common\facades\Setting;
use Yunshop\Commission\models\Commission;

class CommissionWithdrawWidget extends Widget
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function run()
    {
        $set = Setting::get('withdraw.commission', ['roll_out_limit' => '', 'poundage_rate' => '', 'max_roll_out_limit' => '', 'max_time_out_limit' => '']);
        return view('Yunshop\Commission::admin.withdraw-set', [
            'set' => $set,
        ])->render();
    }
}