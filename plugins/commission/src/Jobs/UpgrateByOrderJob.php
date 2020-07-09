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
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\Log;
use Yunshop\Commission\services\AgentService;
use Yunshop\Commission\services\UpgradeService;
use Yunshop\Commission\services\UpgrateConditionsService;
use Yunshop\Merchant\common\services\CenterUpgradeService;

class UpgrateByOrderJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $uid;
    protected $self_order_after;
    protected $order;
    protected $levels;
    protected $set;

    public function __construct($uid, $self_order_after, $order, $levels, $set)
    {
        $this->uid = $uid;
        $this->order = $order;
        $this->levels = $levels;
        // 0 是完成  1 是付款
        $this->self_order_after = $self_order_after;
        $this->set = $set;
        \YunShop::app()->uniacid = $order->uniacid;
    }

    public function handle()
    {
        \Log::info('分销商升级队列');
        // 自己不是分销商 找 上级分销商
        $one_agent = AgentService::getFirstAgentByUid($this->uid);
        if (!$one_agent) {
            return;
        }
        \Log::debug('上级分销商');
        $this->agents($one_agent->member_id);
    }

    private function agents($uid, $num = 1)
    {
        if ($uid > 0) {
            // 跳出递归
            if ($num > 3) {
                return;
            }
            $num += 1;
            // 分销商
            $agent = Agents::getAgentByMemberId($uid)->first();
            // 没有分销商数据 或 不允许升级
            if (!$agent || $agent->agent_not_upgrade) {
                file_put_contents(storage_path('logs/Y0914.txt'), print_r('UID['.$uid.']没有分销商数据 或 不允许升级, '.PHP_EOL,1), FILE_APPEND);
            } else {
                // 分销商的等级权重
                $agent_level_weight = isset($agent->agentLevel->level) ? $agent->agentLevel->level : 0;
                // uid 重新赋值
                $this->uid = $agent->member_id;
                file_put_contents(storage_path('logs/Y0914.txt'),  print_r('time:'.date('Y-m-d H:i:s').',UID['.$this->uid.']要进行升级'.PHP_EOL,1), FILE_APPEND);
                // 下一步
                $this->fecLevels($agent_level_weight);
            }

            // 递归当前
            $this->agents($agent->parent_id, $num);
        }
    }

    private function fecLevels($level_weight)
    {
        \Log::debug('$level_weight',$level_weight);
        $is_upgrate = true;
        foreach ($this->levels as $level) {
            // 当前等级的等级权重 小于等于 分销商等级的等级权重
            if ($level['level'] <= $level_weight) {
                continue;
            }
            // 判断条件
            $condition = $this->getCondition($level);

            if ($condition) {
                continue;
            }
            // 触发升级条件判断
            $is_upgrate = $this->upgrateVerdict($level, $is_upgrate);
            if ($is_upgrate) {
                // 升级
                $this->upgrate($level);
                // 预防重复升级
                break;
            }
        }
    }

    private function upgrate($level)
    {
        // 分销商数据
        $agentModel = Agents::getAgentByMemberId($this->uid)->first();
        // 升级
        UpgradeService::upgrade($level, $this->uid, $agentModel);
        if (app('plugins')->isEnabled('merchant')) {
            //升级完判断招商中心升级
            CenterUpgradeService::handle($this->uid);
        }

        // log
        Log::addLog($agentModel['agent_level_id'], $level['id'], $agentModel, '[与]升级(ORDERID:'.$this->order->id.')');
    }

    private function upgrateVerdict($level, $is_upgrate){
        foreach ($level['upgraded'] as $upgrateType => $value) {
            // 转换字符串格式 [ps:order_count => orderCount]
            $function_name = Str::camel($upgrateType);
            // 验证方法是否存在 并 执行
            if(method_exists(new UpgrateConditionsService(), $function_name)) {
                $is_upgrate = UpgrateConditionsService::$function_name($this->uid, $level, $this->order, $level['upgraded']['self_order_after']);
                // 返回false 跳出循环
                if (!$is_upgrate) {
                    break;
                }
            }
        }
        return $is_upgrate;
    }

    private function getCondition($level)
    {
        $count = count($level['upgraded']);

        $upgraded = $count == 1 ? $level['upgraded']['self_order_after'] :0;
        // 没有升级条件 or 升级条件为:购买指定商品 or 订单状态
        return (!$level['upgraded'] || $upgraded) || $level['upgraded']['goods'];
//        return !$level['upgraded'] || $level['upgraded']['goods'];
        // todo 我咋觉得没啥用呢
        // 没有升级条件 or ((升级条件为:购买指定商品 or 自购订单数 or 自购金额) and 订单状态)
        //return !$level['upgraded'] || (($level['upgraded']['goods'] || $level['upgraded']['self_buy_money'] || $level['upgraded']['self_buy_count']) && $level['upgraded']['self_order_after'] != $this->self_order_after);
    }
}