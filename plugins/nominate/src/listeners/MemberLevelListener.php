<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/16
 * Time: 5:30 PM
 */

namespace Yunshop\Nominate\listeners;


use app\common\events\member\MemberLevelValidityEvent;
use app\common\models\member\MemberParent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Nominate\models\ShopMember;
use Yunshop\Nominate\models\ShopMemberLevel;
use Yunshop\Nominate\services\NominatePrize;
use Yunshop\Nominate\services\NominateTask;

class MemberLevelListener
{
    use DispatchesJobs;

    public $memberModel;
    public $number;
    public $levelId;
    public $levelWeight;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(MemberLevelValidityEvent::class, self::class . '@handle');
    }

    public function test($memberModel, $number, $levelId)
    {
        $this->memberModel = $memberModel;
        $set = \Setting::get('plugin.nominate');
        if (!$set['is_open']) {
            return;
        }

        $this->number = $number;
        $this->levelId = $levelId;
        // 任务
        (new NominateTask($this))->handle();


        $level = ShopMemberLevel::select(['id', 'level', 'level_name'])
            ->where('id', $this->levelId)
            ->first();

        $levelRet = $this->verifyLevelWeightIsMin($level);
        // 会员等级权重最小的等级是 vip,其余不做处理
        if ($levelRet) {
            return;
        }
        $this->levelWeight = $level->level;

        // 奖励
        (new NominatePrize($this))->handle();
    }

    public function handle(MemberLevelValidityEvent $event)
    {
        $this->memberModel = $event->getMemberModel();
        \Log::debug("uid:[".$this->memberModel->member_id."]推荐奖励入口");
        $set = \Setting::get('plugin.nominate');
        if (!$set['is_open']) {
            \Log::debug("uid:[".$this->memberModel->member_id."]没开启奖励");
            return;
        }

        $this->number = $event->getNumber();
        $this->levelId = $event->getLevelId();
        // 任务
        (new NominateTask($this))->handle();

        \Log::debug("uid:[".$this->memberModel->member_id."]次数:[".$this->number."]成为的等级:[".$this->levelId."]");

        $level = ShopMemberLevel::select(['id', 'level', 'level_name'])
            ->where('id', $this->levelId)
            ->first();

        $levelRet = $this->verifyLevelWeightIsMin($level);
        // 会员等级权重最小的等级是 vip,其余不做处理
        if ($levelRet) {
            \Log::debug("uid:[".$this->memberModel->member_id."升级的等级不是vip");
            return;
        }
        $this->levelWeight = $level->level;

        // 奖励
        (new NominatePrize($this))->handle();
    }

    private function verifyLevelWeightIsMin($level)
    {
        $ret = ShopMemberLevel::select(['id', 'level', 'level_name'])
            ->where('level', '<', $level->level)
            ->first();
        return $ret;
    }
}