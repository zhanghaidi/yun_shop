<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/10
 * Time: 12:08
 */

namespace Yunshop\Tbk\common\jobs;

use app\common\models\Member;
use app\common\models\PayType;
use app\common\modules\orderGoods\OrderGoodsCollection;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Yunshop\Tbk\common\models\TbkGoods;
use Yunshop\Tbk\common\modules\order\models\PreOrder;
use Yunshop\Tbk\common\modules\orderGoods\models\PreOrderGoods;
use Yunshop\Tbk\common\services\TaobaoMemberService;

class OrderSynJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tbkOrders;

    public function __construct($tbkOrders)
    {
        $this->tbkOrders = $tbkOrders;
    }

    public function handle()
    {
        // todo $order 判断状态
        $order = $this->tbkOrders[0];

        if ($order->yz_order_sn && $order->tk_status == '订单付款') {
            //无需处理，状态重复导入
            return;
        }

        if (!$order->yz_order_sn && $order->tk_status == '订单付款') {
            $this->createOrder();
            return;
        }

        // 状态改变，更新商城订单状态
        if ($order->yz_order_sn && $order->tk_status == '订单结算') {
            $this->complete($order->yz_order_sn);
            return;
        }

        // 直接入库结算订单，商城订单流程需要全部跑一遍
        if (!$order->yz_order_sn && $order->tk_status == '订单结算') {
            $preOrder = $this->createOrder();
            $this->complete($preOrder->id);
            return;
        }
        //$this->compelete();

    }

    private function getMember($pid)
    {
        $tbkPidModel = new TaobaoMemberService();
        $tbkMember = $tbkPidModel->getMemberIdByPid($pid);

        return Member::where('uid', $tbkMember->member_id)->first();
    }

    private function getGoods($num_iid)
    {
        $goods = TbkGoods::select("goods_id")->where("num_iid", $num_iid)->first();
        return $goods->goods_id;
    }

    /**
     * @throws \app\common\exceptions\ShopException
     */
    private function createOrder()
    {
        // todo, 会员通过推广位，找到商城会员
        $member = $this->getMember($this->tbkOrders[0]->adzone_id);
        \app\frontend\models\Member::$current = $member;

        $orderGoodsCollection = [];
        foreach ($this->tbkOrders as $tbkOrder) {
            $orderGoods = new PreOrderGoods([
                'total' => $tbkOrder->item_num,
                'goods_id' => $this->getGoods($tbkOrder->num_iid),       // todo, 从tbkOrder获取商城商品ID
            ]);
            $orderGoods->tbkOrder = $tbkOrder;
            $orderGoodsCollection[] = $orderGoods;
        }

        $orderGoodsCollection = new OrderGoodsCollection($orderGoodsCollection);
        $preOrder = new PreOrder();
        $preOrder->tbkOrder = $this->tbkOrder;
        $preOrder->init($member, $orderGoodsCollection);
        $preOrder->generate();
        $this->pay($preOrder->id);

        // todo, 更新tbkOrders里yz_order_sn
        foreach ($this->tbkOrders as $tbkOrder) {
            $tbkOrder->update("yz_order_sn", $preOrder->id);
        }

        \app\frontend\models\Member::$current = null;

        return $preOrder;
    }

    public function pay($orderId)
    {
        OrderService::orderPay(['order_id' => $orderId,'pay_type_id'=>PayType::BACKEND]);
    }

    private function send($orderId)
    {
        OrderService::orderSend(['order_id' => $orderId]);
    }

    /**
     * @throws \app\common\exceptions\AppException
     */
    private function complete($orderId)
    {
        OrderService::orderReceive(['order_id' => $orderId]);

        // todo, 更新tbkOrders里yz_order_status=1
        foreach ($this->tbkOrders as $tbkOrder) {
            $tbkOrder->update("yz_order_status", 1);
        }
    }
}