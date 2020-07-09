<?php

namespace Yunshop\MinApp\Common\Listeners;
use app\common\events\cart\AddCartEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\MinApp\Common\Services\CollectionHwqService;

class AddCartListener
{

    /**
     * 购物车添加完成事件
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(AddCartEvent::class, function($event) {
                if(\Setting::get('plugin.min_app.hwq') && \Yunshop::request()->type == 2){
                    $cartModel = $event->getCartModel();
                    new CollectionHwqService($cartModel);
                }
        });
    }
}