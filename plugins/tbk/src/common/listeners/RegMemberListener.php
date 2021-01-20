<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/27
 * Time: 下午5:13
 */

namespace Yunshop\Tbk\common\listeners;

use app\common\events\member\MemberRegisterSuccessEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Tbk\common\services\TaobaoMemberService;

class RegMemberListener
{


    public function subscribe(Dispatcher $events)
    {
        $events->listen(MemberRegisterSuccessEvent::class, self::class . '@handle');
    }

    public function handle(MemberRegisterSuccessEvent $event)
    {
        $memberId = $event->getMemberId();
        $tbkMember = new TaobaoMemberService();
        $tbkMember->regTaobaoMember($memberId);
    }
}