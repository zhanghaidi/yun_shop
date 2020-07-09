<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/27
 * Time: 下午5:13
 */

namespace Yunshop\MinApp\Common\Listeners;

use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\MinApp\Common\Services\DelCollectionHwqService;
use app\common\events\cart\DeleteCartEvent;

class DeleteCartListener
{
    public $event;
    public $order;


    /**
     * 购物车删除事件
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
            if(\Setting::get('plugin.min_app.hwq') && \YunShop::request()->type == 2){
                $events->listen(DeleteCartEvent::class, function($event) {
                    $cartsId = $event->getCartsId();
                    new DelCollectionHwqService($cartsId);
                });
            }
    }
}