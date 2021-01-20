<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/16
 * Time: 下午3:14
 */

namespace Yunshop\Micro\Listener;

use app\common\events\order\AfterOrderCreatedImmediatelyEvent;
use app\common\facades\Setting;
use app\common\models\Member;
use app\common\models\Order;
use app\Jobs\OrderBonusJob;
use Illuminate\Contracts\Events\Dispatcher;
use app\common\events\order\AfterOrderCreatedEvent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;
use Yunshop\Micro\common\models\GoodsMicro;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\models\MicroShopBonusLog;
use Yunshop\Micro\common\services\MessageService;
use Yunshop\Micro\common\services\MicroShop\MicroShopService;

use Yunshop\Micro\common\services\MicroShopGoods\MicroShopGoodsService;

class CreateBonusLogListener
{
    use DispatchesJobs;

    public $event;
    public $order;
    public $micro_shop_id;
    public $micro_shop;
    public $public_data;
    public $data;
    public $agent_data;
    public $set;
    public $goods_micro;
    public $bonus_type;
    public $is_bonus = true;
    public $num = 0;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(AfterOrderCreatedImmediatelyEvent::class, self::class . '@onCreate');
    }

    public function onCreate(AfterOrderCreatedImmediatelyEvent $event)
    {
        $this->set = Setting::get('plugin.micro');
        $shop_id = \YunShop::request()->shop_id;

        if (empty($shop_id)) {
            Log::debug('微店分红记录:店铺id为空');
            return;
        }
        // todo 如果基础设置没有开启微店分红，返回
        if ($this->set['is_open_bonus'] == 0) {
            Log::debug('微店分红记录:未开启');
            return;
        }
        $this->event = $event;
        $this->order = Order::find($event->getOrderModel()->id);
        $this->micro_shop = MicroShopService::verifyMicroShopByUrlId($shop_id);
        if (!$this->micro_shop) {
            Log::debug('微店分红记录:未找到店铺记录');

            return;
        }
        $this->micro_shop_id = $shop_id;
        $this->verifyGoodsIsMicroShop();

        // 订单插件分红记录
        $this->dispatch(new OrderBonusJob('yz_micro_shop_bonus_log', 'micro-shop', 'order_sn', 'order_sn', 'bonus_money', $this->order));
        Log::debug('微店分红记录:完成');

    }

    public function verifyGoodsIsMicroShop()
    {
        foreach ($this->order->hasManyOrderGoods as $order_goods) {
            $result = MicroShopGoodsService::verifyGoodsBelongToMicroShop($this->micro_shop_id, $order_goods->goods_id);
            if (!$result) {
                return;
            }
            $this->goods_micro = GoodsMicro::getGoodsMicro($order_goods->goods_id)->first();
            $this->verifyGoodsOpenBonus();
            if ($this->is_bonus) {
                $this->createBonusLog($order_goods);
            }
        }
    }

    public function verifyGoodsOpenBonus()
    {
        if ($this->goods_micro && $this->goods_micro['is_open_bonus'] == 0) {
            $this->is_bonus = false;
        }
        if (!$this->goods_micro) {
            $this->is_bonus = false;
        }
    }

    public function createBonusLog($order_goods)
    {
        $this->getData($order_goods);
        $result = MicroShopBonusLog::create($this->data);
        if ($result) {
            //todo 微店分红通知
            MessageService::bonusOrder($result);
        }
        $this->handleAgentBonus($this->micro_shop->member_id);
    }

    /**
     * @name 三级微店分红
     * @author
     * @param $member_id
     */
    public function handleAgentBonus($member_id)
    {
        if ($this->num == $this->set['agent_bonus_level']) {
            return;
        }
        $agent = MicroShopService::verifyAgentMicroShop($member_id);
        if (!$agent) {
            return;
        }
        $this->getAgentData($agent, $member_id);
        if ($this->agent_data['agent_bonus_ratio'] <= 0) {
            $this->num += 1;
            $this->handleAgentBonus($agent->member_id);
        }
        $agent_result = MicroShopBonusLog::create($this->agent_data);
        if ($agent_result) {
            MessageService::lowerBonusOrder($agent_result);
            $this->num += 1;
            $this->handleAgentBonus($agent->member_id);
        }
    }

    public function getData($order_goods)
    {
        $buyer = Member::getMemberById($this->order->uid);
        $this->public_data = [
            'uniacid'           => \YunShop::app()->uniacid,
            'shop_id'           => $this->micro_shop_id,
            'member_id'         => $this->micro_shop->member_id,
            'bonus_ratio'       => $this->micro_shop->hasOneMicroShopLevel->bonus_ratio,
            'level_id'          => $this->micro_shop->hasOneMicroShopLevel->id,
            'order_id'          => $this->order->id,
            'order_sn'          => $this->order->order_sn,
            'order_buyer'       => $buyer->nickname,
            'goods_id'          => $order_goods->goods_id,
            'goods_title'       => $order_goods->title,
            'goods_price'       => $order_goods->goods_price,
            'goods_cost_price'  => $order_goods->goods_cost_price,
            'goods_total'       => $order_goods->total,
            'goods_thumb'       => $order_goods->thumb,
            'order_status'      => $this->order->status,
            'apply_status'      => 0
        ];
        $this->data = $this->public_data;
        $this->calculationBonus($order_goods);
    }

    public function calculationBonus($order_goods)
    {
        \Log::info("--Yangyang3--");
        // todo 走微店等级分红比例
        $ratio = $this->micro_shop->hasOneMicroShopLevel->bonus_ratio;
        // todo 商品里面的微店分红比例
        if ($this->goods_micro && $this->goods_micro['is_open_bonus'] == 1) {
            // todo 如果启用独立分红比例，并且每个等级的比例都不为空
            if ($this->goods_micro['independent_bonus'] == 1) {
                $this->goods_micro['set'] = unserialize($this->goods_micro['set']);
                $ratio = $this->goods_micro['set'][$this->micro_shop->hasOneMicroShopLevel->id];
            }
        }
        if ($this->set['bonus_type'] == 0) {
            $this->data['bonus_money'] = $order_goods->goods_price * $ratio / 100;
        } else {
            $this->data['bonus_money'] = ($order_goods->goods_price - $order_goods->goods_cost_price) * $ratio / 100;
        }
        // todo 分红记录里面的分红比例是当前运算的比例
        $this->data['bonus_ratio'] = $ratio;
    }

    public function getAgentData($agent, $lower_uid = 0)
    {
        $this->agent_data = $this->public_data;
        $this->agent_data['shop_id'] = $agent->id;
        $this->agent_data['member_id'] = $agent->member_id;
        $this->agent_data['bonus_ratio'] = $agent->hasOneMicroShopLevel->bonus_ratio;
        $this->agent_data['level_id'] = $agent->hasOneMicroShopLevel->id;
        $this->agent_data['is_lower'] = MicroShopBonusLog::IS_LOWER;
        $this->agent_data['lower_level_shop_id'] = $this->micro_shop->id;
        $this->agent_data['lower_level_member_id'] = $this->micro_shop->member_id;
        $this->agent_data['lower_level_nickname'] = $this->micro_shop->hasOneMember->nickname;

        if ($lower_uid != 0) {
            $micro = MicroShop::getMicroShopByMemberId($lower_uid);
            $this->agent_data['lower_level_shop_id'] = $micro->id;
            $this->agent_data['lower_level_member_id'] = $micro->member_id;
            $this->agent_data['lower_level_nickname'] = $micro->hasOneMember->nickname;
        }

        $this->calculationAgentBonus();
    }

    public function calculationAgentBonus()
    {
        $ratio = $this->getAgentBonusRatio();
        $this->agent_data['lower_level_bonus_money'] = $this->data['bonus_money'] * $ratio / 100;
        $this->agent_data['agent_bonus_ratio'] = $ratio;
    }

    public function getAgentBonusRatio()
    {
        if (($this->num + 1) == 1) {
            return $this->set['bonus_first_level'];
        } else if (($this->num + 1) == 2) {
            return $this->set['bonus_second_level'];
        } else if (($this->num + 1) == 3) {
            return $this->set['bonus_third_level'];
        } else {
            return 0;
        }
    }
}