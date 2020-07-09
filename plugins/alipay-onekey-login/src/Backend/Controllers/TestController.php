<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/28 上午10:33
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Controllers;

use app\common\models\UniAccount;
use app\common\components\BaseController;
use Yunshop\Love\Backend\Modules\Love\Models\LoveRecords;
use Yunshop\Love\Common\Models\MemberLove;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Common\Services\LoveActivationService;
use Yunshop\Love\Common\Services\OfflineService;
use Yunshop\Love\Common\Services\SetService;

class TestController extends BaseController
{

    public function offline()
    {
        $member_id = 5;

        $offlineService = new OfflineService();

        //dd($offlineService);

        //dd($offlineService->getFirstOffline($member_id));

        //dd($offlineService->getSecondOffline($member_id));

        //dd($offlineService->getThirdOffline($member_id));

        //dd($offlineService->getMemberLevelOffline($member_id, 4));

        dd($offlineService->getTeamOffline($member_id));
    }
    public function withdrawRollback()
    {

        return null;
            dump('++++++++++ 提现奖励修改开始 +++++++++++');
            $list = LoveRecords::select('id', 'member_id', 'change_value')->where('value_type', 2)->where('source', 12)->get();

            foreach ($list as $key => $item) {

                LoveRecords::where('id', $item->id)->update(['value_type' => 1]);

                $memberLove = MemberLove::where('member_id', $item->member_id)->first();

                if ($memberLove && $memberLove->froze >= $item->change_value) {
                    $memberLove->update(['froze' => $memberLove->froze - $item->change_value, 'usable' => $memberLove->usable + $item->change_value]);

                    dump('会员ID：【' . $memberLove->member_id . '】,提现奖励修改成功(正常)，修改值：' . $item->change_value);
                } elseif ($memberLove && $memberLove->froze < $item->change_value) {

                    $change_value = $memberLove->froze;
                    $memberLove->update(['froze' => 0, 'usable' => $memberLove->usable + $memberLove->froze]);
                    dump('会员ID：【' . $memberLove->member_id . '】,提现奖励修改成功(冻结不足)，修改值：' . $change_value);
                } else {
                    dump('！！！错误！！！会员ID：【' . $memberLove->member_id . '】,提现奖励修改失败，应修改值：' . $item->change_value);

                }
            }

            dd('++++++++++ 提现奖励修改完成 +++++++++++');
    }





    public function index()
    {
        if(\YunShop::app()->isfounder === true) {
            set_time_limit(0);
            $uniAccount = UniAccount::getEnable() ?: [];
            foreach ($uniAccount as $u) {

                \YunShop::app()->uniacid = $u->uniacid;
                \Setting::$uniqueAccountId = $u->uniacid;

                $isActivation = $this->isActivation();
                if ($isActivation) {
                    (new LoveActivationService())->handleActivation($u->uniacid);
                    //\Log::info('========爱心值激活UNIACID:'.$u->uniacid.'激活成功========');
                } else {
                    //\Log::info('========爱心值激活UNIACID:'.$u->uniacid.'未满足激活条件========');
                    continue;
                }
            }
        }
    }

    private function isActivation()
    {
        $activation_time = SetService::getActivationTime();
        //dd($activation_time);
        switch ($activation_time) {
            case 1:
                return $this->everyDay();
                break;
            case 2:
                return $this->weekly();
                break;
            case 3:
                return $this->monthly();
                break;
            default:
                echo '<br> ========爱心值激活UNIACID:'.\YunShop::app()->uniacid.'未开启激活========';
                return false;

        }
    }


    private function everyDay()
    {
        //return 'day';
        return $this->activationHour();
    }

    private function weekly()
    {
        //return 'week';
        $activation_time_week = SetService::getActivationWeek();
        if ($activation_time_week == date('w')) {
            return $this->activationHour();
        }
        echo '<br> ========爱心值激活UNIACID:'.\YunShop::app()->uniacid.',本周'.date('w').'已经激活========';
        return false;

    }

    private function monthly()
    {
        //return 'month';
        $last_month_activation = \Setting::get('love.last_month_activation');
        if ($last_month_activation && $last_month_activation == date('m')) {
            echo '<br> ========爱心值激活UNIACID:'.\YunShop::app()->uniacid.',本月'.date('m').'已经激活========';
            return false;
        }
        return $this->activationHour();
    }

    private function activationHour()
    {
        $activation_time_hour = SetService::getActivationHour();
        $activation_time_hour = $activation_time_hour - 1;
        if ($activation_time_hour == date('H')) {
            return $this->isActivated();
        }
        echo '<br> ========爱心值激活UNIACID:'.\YunShop::app()->uniacid.',激活时间'.$activation_time_hour.'========';
        return false;
    }

    private function isActivated()
    {
        $last_time_activation = \Setting::get('love.last_time_activation');
        if ($last_time_activation && $last_time_activation == date('d')) {
            echo '<br> ========爱心值激活UNIACID:'.\YunShop::app()->uniacid.','.date('d').'日已经激活========';
            return false;
        }
        return true;
    }



    public function test()
    {
        if(\YunShop::app()->isfounder === true) {
            //爱心值设置---》抵扣开关，
            SetService::getDeductionStatus();
            //爱心值设置---》最高抵扣比例
            SetService::getDeductionProportion();
            //爱心值设置---》最低抵扣比例
            SetService::getDeductionProportionLow();

            //爱心值商品数据表   ims_yz_love_goods

            //商品设置---》抵扣开关字段：deduction  最高抵扣比例：deduction_proportion

            $memberId = 1;
            //获取爱心值会员
            CommonService::getLoveMemberModelById($memberId);

            //验证商城是否存在此会员
            CommonService::getMemberModel($memberId);

            //会员当前可用爱心值
            CommonService::getMemberUsableLove($memberId);

            //会员当前冻结爱心值
            CommonService::getMemberFrozeLove($memberId);

        }


    }




}