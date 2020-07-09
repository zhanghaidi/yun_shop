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

class CommissionManageWithdrawWidget extends Widget
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function run()
    {
        $set = Setting::get('withdraw.manage', ['roll_out_limit' => '', 'poundage_rate' => '']);
        return view('Yunshop\Commission::admin.withdraw-manage-set', [
            'set' => $set,
        ])->render();
    }
}