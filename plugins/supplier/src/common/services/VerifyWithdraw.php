<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 下午7:58
 */

namespace Yunshop\Supplier\common\services;

use Setting;
use Yunshop\Supplier\supplier\models\SupplierWithdraw;

class VerifyWithdraw
{
    /**
     * @name 验证当前提现是否满足提现设置的时间限制
     * @author yangyang
     * @return bool|false|string
     */
    public static function verifyWithdraw($supplier_id)
    {
        $set = Setting::get('plugin.supplier');
        $last_withdraw = SupplierWithdraw::getLastWithdraw($supplier_id);
        if (!$last_withdraw) {
            return false;
        }
        $last_withdraw_time = strtotime($last_withdraw->toArray()['created_at']);
        if (($last_withdraw_time + $set['limit_day'] * 86400) > time()) {
            $date = date('Y-m-d H:i:s', ($last_withdraw_time + $set['limit_day'] * 86400));
            $msg = $date . '可以提现,最后一次提现时间为['.$last_withdraw->toArray()['created_at'].'],提现限制为最后一次提现时间后的['.$set['limit_day'].']天';
            return $msg;
        }
        return false;
    }
}