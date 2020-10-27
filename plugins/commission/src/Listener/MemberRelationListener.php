<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/7
 * Time: 下午1:50
 */

namespace Yunshop\Commission\Listener;

use app\common\events\member\MemberRelationEvent;
use app\common\facades\Setting;
use app\common\models\Member;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Yunshop\Commission\Jobs\UpgrateByRegisterJob;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\services\MessageService;
use Yunshop\Commission\services\UpgradeService;

class MemberRelationListener
{
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(\app\common\events\member\MemberRelationEvent::class, self::class . '@handle');
    }

    public function handle (MemberRelationEvent $event) {
        date_default_timezone_set("PRC");
        \Log::info('分销商-会员获得推广权限');

        if ($event->getOrderId()) {
            return;
        }
        $yzMemberModel = $event->getMemberModel()->yzMember;
        \Log::info('yzMemberId:' . $yzMemberModel->member_id);

        $memberFans = $event->getMemberModel()->hasOneFans;
        $agent = Agents::getAgentByMemberId($yzMemberModel->member_id)->first();
        if($agent){
            return;
        }

        $set = Setting::get('plugin.commission');
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
            if (Agents::create($agentData)) {
                event(new \app\common\events\plugin\CommissionEvent($agentData));
                MessageService::becomeAgent($memberFans);
                $this->upgrade($yzMemberModel, $set);
                \Log::info('添加分销商完成');
                return;
            }
            //Agents::insert($agentData);

        }
        \Log::info('未开启分销插件 或 已是分销商 或 分销商插入失败');

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
}