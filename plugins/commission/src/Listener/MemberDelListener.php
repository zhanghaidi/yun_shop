<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/3
 * Time: 14:56
 */

namespace Yunshop\Commission\Listener;

use app\common\events\member\MemberDelEvent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Commission\models\Agents;

class MemberDelListener
{
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(MemberDelEvent::class, self::class . '@handle');
    }

    public function handle(MemberDelEvent $event)
    {

        $uid =  $event->getUid();
        $agentModel = Agents::where('member_id', $uid)->first();

        if ($agentModel) {
            $agentModel->delete();
        }

    }
}