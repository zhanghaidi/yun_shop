<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/7/25
 * Time: 下午5:11
 */

namespace Yunshop\Commission\Jobs;


use EasyWeChat\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\Log;
use Yunshop\Commission\models\YzMember;
use Yunshop\Commission\services\UpgradeService;
use Yunshop\Commission\services\UpgrateConditionsService;

class UpgrateByRegisterJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $uid;
    protected $levels;
    protected $uniacid;

    public function __construct($uid, $levels)
    {
        $this->uid = $uid;
        $this->levels = $levels;
        $this->uniacid = \YunShop::app()->uniacid;
    }

    public function handle()
    {
        \YunShop::app()->uniacid = $this->uniacid;
        $agentModel = Agents::getAgentByMemberId($this->uid)->first();
        if (!$agentModel || $agentModel->agent_not_upgrade) {
            return;
        }
        $agent_level_weight = isset($agentModel->agentLevel->level) ? $agentModel->agentLevel->level : 0;
        $member = YzMember::getMemberByMemberId($this->uid)->first();
        $memberParent = YzMember::getPraents($member->relation)->get();
        foreach ($this->levels as $level) {

            if ($level['level'] <= $agent_level_weight) {
                continue;
            }

            $upgraded = $this->getCondition($level);
            if ($upgraded) {
                continue;
            }
//            if (!$level['upgraded']) {
//                continue;
//            }
            foreach ($memberParent as $member) {


                $agent = Agents::getAgentByMemberId($member->member_id)->first();
                $level_weight = isset($agent->agentLevel->level) ? $agent->agentLevel->level : 0;

                if ($agentModel->agent_not_upgrade) {
                    continue;
                }

                if ($level_weight >= $level['level']) {
                    continue;
                }

                $is_upgrate = true;
                foreach ($level['upgraded'] as $upgrateType => $value) {
                    $function_name = Str::camel($upgrateType);
                    if(method_exists(new UpgrateConditionsService(), $function_name)) {
                        $is_upgrate = UpgrateConditionsService::$function_name($member['member_id'], $level, false, $level['upgraded']['self_order_after']);
                        if (!$is_upgrate) {
                            break;
                        }
                    }
                }
                if ($is_upgrate) {
                    // 升级
                    $agentModel = Agents::getAgentByMemberId($member['member_id'])->first();
                    UpgradeService::upgrade($level, $member['member_id'], $agentModel);
                }
            }
        }
    }

    private function getCondition($level)
    {
        $count = count($level['upgraded']);

        $upgraded = $count == 1 ? $level['upgraded']['self_order_after'] :0;
        // 没有升级条件 or 升级条件为:购买指定商品 or 订单状态
        return (!$level['upgraded'] || $upgraded) || $level['upgraded']['goods'];

    }
}