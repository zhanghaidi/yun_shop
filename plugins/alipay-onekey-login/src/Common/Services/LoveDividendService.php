<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-05-08
 * Time: 15:18
 */

namespace Yunshop\Love\Common\Services;


use app\common\facades\Setting;
use app\common\models\Order;
use app\common\models\UniAccount;
use Carbon\Carbon;
use Yunshop\Love\Common\Models\LoveDividendLog;
use Yunshop\Love\Common\Models\MemberLove;

class LoveDividendService
{
    public function handle()
    {
        set_time_limit(0);
        $uniAccount = UniAccount::getEnable();

        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;

            $this->handleLog();
        }
    }

    public function handleLog()
    {
        $love_dividend_set = Setting::get('plugin.love_dividend');
        $time = $love_dividend_set['dividend_day'];

        if ($love_dividend_set['is_dividend'] != 1) {
            return;
        }

        $old_time = Setting::get('plugin.love_dividend_time');
        $is_execute = $this->isPeriod($old_time, $time);
        if ($is_execute == false) {
            return;
        }

        $cycle_time = Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->subDay($time+1)->endOfDay()->timestamp;
        $current_time = Carbon::now()->startOfDay()->timestamp;
        $range = [$cycle_time, $current_time];

        foreach ($this->getMemberByLove() as $member) {
            $logModel = new LoveDividendLog();

            $data = $this->getData($love_dividend_set, $range, $member);
            if ($data == false) {
                continue;
            }
            $logModel->fill($data);
            $logModel->save();
        }

        $current_time = date('Y-m-d');
        Setting::set('plugin.love_dividend_time', $current_time);
    }

    public function isPeriod($old_time, $time)
    {
        if ($old_time == null) {
            return true;
        }

        if ($old_time == date('Y-m-d')) {
            return false;
        }

        if (date('Y-m-d') == Carbon::createFromFormat('Y-m-d', $old_time)->addDay($time)->toDateString()) {
            return true;
        }

        return false;
    }

    public function getData($set, $range, $member_id)
    {
        $dividend = round($this->getShopPercentage($set)/100*$this->getOrderAll($range)*$this->getLovePercentage($member_id), 2);

        if ($dividend < 1) {
            return false;
        }

        $data = [
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => $member_id,
            'shop_amount'   => $this->getOrderAll($range),
            'love'          => MemberLove::uniacid()->where('member_id', $member_id)->sum('usable'),
            'love_all'      => MemberLove::uniacid()->sum('usable'),
            'dividend_rate' => $this->getShopPercentage($set),
            'dividend'      => $dividend,
        ];

        return $data;
    }

    public function getMemberByLove()
    {
        return MemberLove::uniacid()->whereHas('Member')->pluck('member_id');
    }

    public function getShopPercentage($set)
    {
        $shop_percentage_set = $set['dividend_rate'];

        return $shop_percentage_set;
    }

    public function getOrderAll($range)
    {
        $price = Order::uniacid()->where('status', 3)->whereBetween('finish_time', $range)->sum('price');
        $dispatch_price = Order::uniacid()->where('status', 3)->whereBetween('finish_time', $range)->sum('dispatch_price');
        $amount = $price - $dispatch_price;
        $amount = $amount > 0 ? $amount : 0;
        return $amount;
    }

    public function getLovePercentage($member_id)
    {
        $love_all = MemberLove::uniacid()->sum('usable');
        $love_member = MemberLove::uniacid()->where('member_id', $member_id)->sum('usable');

        return bcdiv($love_member, $love_all, 10);
    }
}