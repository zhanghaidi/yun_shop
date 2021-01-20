<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/16
 * Time: 上午11:38
 */

namespace Yunshop\Micro\Listener;

use app\backend\modules\member\models\Member;
use app\common\events\order\AfterOrderPaidEvent;
use app\common\models\Order;
use Illuminate\Contracts\Events\Dispatcher;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\services\MessageService;
use Yunshop\Micro\common\services\MicroShopLevel\LevelService;
use app\common\events\member\MemberGroupEvent;

class ChangeMicroListener
{
    public $event;
    public $order;
    public $level = false;
    public $micro_shop;

    const MICRO_SHOP_NAME = '的微店';
    const MICRO_SHOP_SIGNATURE = '微店铺';

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderPaidEvent::class, self::class . '@onCreate');
    }

    public function onCreate(AfterOrderPaidEvent $event)
    {
        $this->event = $event;
        $this->order = Order::find($event->getOrderModel()->id);
        $this->verify();
        //todo 只要购买的是微店等级的商品就成为微店
    }

    public function verify()
    {
        foreach ($this->order->hasManyOrderGoods as $order_goods) {
            $this->level = LevelService::verifyGoodsBelongLevel($order_goods->goods_id);
            $this->vertfyMemberIsMicroShop();
        }
    }

    public function vertfyMemberIsMicroShop()
    {
        $this->micro_shop = MicroShop::getMicroShopByMemberId($this->order->uid);
        if (!$this->micro_shop) {
            $this->changeFirst();
        } else {
            $this->changeAgain();
        }
    }

    public function changeFirst()
    {
        if ($this->level) {
            $member = Member::getMemberInfoById($this->order->uid);
            $micro_data = [
                'uniacid'       => \YunShop::app()->uniacid,
                'member_id'     => $this->order->uid,
                'level_id'      => $this->level->id,
                'shop_name'     => $member->nickname . self::MICRO_SHOP_NAME,//todo 默认
                'shop_avatar'   => $member->avatar,//todo 默认
                'signature'     => $member->nickname . self::MICRO_SHOP_SIGNATURE //todo 默认
            ];
            $micro_shop_model = new MicroShop();
            $micro_shop_model->fill($micro_data);
            $result = $micro_shop_model->save();
            if ($result) {
                event(new MemberGroupEvent($this->order->uid));
                //todo 发送成为微店通知，并纳入金币设置的会员分组
                MessageService::becomeMicro($this->order->uid);
            }
        }
    }

    public function changeAgain()
    {
        if ($this->level) {
            $result = $this->verifyLevel();
            if ($result) {
                $this->micro_shop->level_id = $this->level->id;
                $this->micro_shop->save();
                MessageService::upgradeMicro($this->micro_shop);
                // todo 微店等级升级通知
            }
        }
    }

    public function verifyLevel()
    {
        if ($this->micro_shop->hasOneMicroShopLevel->level_weight < $this->level->level_weight) {
            return true;
        } else {
            return false;
        }
    }
}