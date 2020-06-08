<?php

namespace app\common\listeners;

use app\common\events\cart\AddCartEvent;
use app\common\exceptions\AppException;
use Illuminate\Contracts\Events\Dispatcher;

class MemberCartListener
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(AddCartEvent::class, self::class . '@handle');
    }

    public function handle(AddCartEvent $event)
    {
        $memberId = $event->getCartModel()['member_id'];

        $memberCarts = app('OrderManager')->make('MemberCart')->carts()->where('member_id', $memberId)
            ->pluginId()
            ->orderBy('created_at', 'desc')
            ->get();

        if ($memberCarts->count() < 2) {
            return;
        }
        $lastEnableDispatchTypeIds = null;
        foreach ($memberCarts as $memberCart) {
            $enableDispatchTypeIds = $memberCart->goods->hasOneGoodsDispatch ? $memberCart->goods->hasOneGoodsDispatch->enableDispatchTypeIds() : null;

            if (isset($lastEnableDispatchTypeIds)) {
                sort($enableDispatchTypeIds);
                sort($lastEnableDispatchTypeIds);

                if ($enableDispatchTypeIds != $lastEnableDispatchTypeIds) {
                    throw new AppException("购物车商品配送方式冲突,请分开结算");
                }
            };
            $lastEnableDispatchTypeIds = $enableDispatchTypeIds;

        }

    }

}