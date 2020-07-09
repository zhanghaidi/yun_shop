<?php


namespace Yunshop\Commission\Listener;

use app\common\facades\Setting;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Commission\Jobs\UpgrateByRegisterJob;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\Log;
use Yunshop\Commission\models\YzMember;
use Yunshop\Commission\services\UpgradeService;
use Yunshop\Commission\services\UpgrateConditionsService;
use Illuminate\Foundation\Bus\DispatchesJobs;

class RegisterByAgentListener
{
    use DispatchesJobs;

    /**
     *
     * @param  Dispatcher $events
     * @return mixed
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(\app\common\events\member\RegisterByAgent::class, function ($event) {
            date_default_timezone_set("PRC");
            //订单model
            $model = $event->getData();
            $agent = Agents::getAgentByMemberId($model['member_id'])->first();

            $set = \Setting::get('plugin.commission');
            $levels = UpgradeService::getLevelUpgraded();
            if (!$levels) {
                return;
            }
            \Log::debug("监听is_with",$set);
            if ($set['is_with']) {
                $this->dispatch(new UpgrateByRegisterJob($model['member_id'], $levels));
            } else {
                /**
                 * 分销商会员下线升级
                 */
                UpgradeService::member($model['member_id']);
                /**
                 * 分销商下线升级
                 */
                UpgradeService::agent($agent);

                return;
            }
        });
    }
}
