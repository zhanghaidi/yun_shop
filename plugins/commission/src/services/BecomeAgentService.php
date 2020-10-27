<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/7/18
 * Time: 10:45
 */

namespace Yunshop\Commission\services;

use app\common\models\MemberRelation;
use app\common\models\Order;
use app\common\models\MemberShopInfo;
use Yunshop\Commission\models\OrderGoods;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\Commission\Jobs\UpgrateByRegisterJob;
use Yunshop\Commission\models\Agents;
use app\common\models\Member;

class BecomeAgentService
{
    use DispatchesJobs;
    /**
     * 成为分销商
     * @author
     * @param $uid
     * @param $order_status
     */
    public function verification($uid, $order_status)
    {
        $agent = Agents::getAgentByMemberId($uid)->first();
        if($agent){
            return;
        }

        $set = MemberRelation::getSetInfo()->first();
        if (empty($set)) {
            return;
        }

        //付款后 完成后
        if ($set->become_order != $order_status) {
            return;
        }

        $become_term = unserialize($set->become_term);
        if (empty($become_term)) {
            return;
        }


        //只支持 或和与
        if ($set->become != 2 && $set->become != 3) {
            return;
        }

        //如果销售佣金插件未开启则不走销售佣金
        if (!app('plugins')->isEnabled('sale-commission')) {
            unset($become_term[5]);
        }

        $result = false;
        foreach ($become_term as $item) {
            $result = $this->conditionalVerification($item, $set, $uid);
            //或
            if ($set->become == 2) {
                // 假 继续循环
                if ($result) {
                    break;
                }
                // 真 跳出循环
                continue;
            }
            //与
            if ($set->become == 3) {
                if (!$result) {
                    break;
                }
            }
        }
        if ($result) {
            $this->become($uid);
        }

    }

    public function conditionalVerification($item, $set, $uid)
    {
        switch ($item)
        {
            //消费次数
            case 2: return $this->checkConsumeTotal($set, $uid);
                break;
            //消费总额
            case 3: return $this->checkConsumePrice($set, $uid);
                break;
            //指定商品
            case 4 : return $this->checkOrderGoods($set, $uid);
                break;
            //销售佣金
            case 5 : return $this->checkSalesCommission($set, $uid);
                break;
            default : return false;
        }
    }

    public function become($uid)
    {

        \Log::info('分销商-会员成为分销商');
        $yzMemberModel = MemberShopInfo::uniacid()->where('member_id',$uid)->with('hasOneMember')->first();

        \Log::info('yzMemberId:' . $yzMemberModel->member_id);
        $memberFans = $yzMemberModel->hasOneMember;

        $set = \Setting::get('plugin.commission');
        if ($set['is_commission']) {
            $agentData = [
                'uniacid' => \YunShop::app()->uniacid,
                'member_id' => $yzMemberModel->member_id,
                'parent_id' => $yzMemberModel->parent_id,
                'parent' => $yzMemberModel->relation,
                'created_at' => time(),
                'updated_at' => time(),
            ];
            \Log::info('分销商数据：', $agentData);
            Agents::create($agentData);
            //fixby-zhd-分销关系绑定报错不存在，暂时注释 20201027
            //event(new \app\common\events\plugin\CommissionEvent($agentData));
            MessageService::becomeAgent($memberFans);
            $this->upgrade($yzMemberModel, $set);
        } else {
            \Log::info('未开启分销插件 或 已是分销商');
        }
        \Log::info('添加分销商完成');


    }

    public function upgrade($yzMemberModel, $set)
    {
        $agent = Agents::getAgentByMemberId($yzMemberModel['member_id'])->first();
        $levels = UpgradeService::getLevelUpgraded();
        if (!$levels) {
            return;
        }
        \Log::debug("监听is_with",$set);
        if ($set['is_with']) {
            $this->dispatch(new UpgrateByRegisterJob($yzMemberModel['member_id'], $levels));
        } else {
            //分销商会员下线升级
            UpgradeService::member($yzMemberModel['member_id']);

            //分销商下线升级
            UpgradeService::agent($agent);
        }
    }

    public function checkOrderGoods($set, $uid)
    {
        //判断商品
        $goods_id = explode(',',$set->become_goods_id);
        $list = OrderGoods::uniacid()
            ->where('uid',$uid)
            ->whereIn('goods_id', $goods_id)
            ->whereHas('hasOneOrder', function ($query) {
                $query->where('status', '>=', 1);
            })
            ->get();

        if ($list->isEmpty()) {
            return false;
        }
        return true;
    }

    public function checkConsumeTotal($set, $uid)
    {
        //消费达多少次
        $ordercount = Order::getCostTotalNum($uid);
        \Log::debug('用户：'. $ordercount);
        \Log::debug('系统：'. intval($set->become_ordercount));
        $can = $ordercount >= intval($set->become_ordercount);

        if ($can) {
            return true;
        }
        return false;
    }

    public function checkConsumePrice($set, $uid)
    {
        //消费达多少钱
        $money_count = Order::getCostTotalPrice($uid);
        if ($money_count >= floatval($set->become_moneycount)) {
            return true;
        }
        return false;
    }

    public function checkSalesCommission($set, $uid)
    {
        //销售佣金
        $sales_money = \Yunshop\SalesCommission\models\SalesCommission::sumDividendAmountByUid($uid);
        if ($sales_money >= $set->become_selfmoney) {
            return true;
        }
        return false;
    }

}