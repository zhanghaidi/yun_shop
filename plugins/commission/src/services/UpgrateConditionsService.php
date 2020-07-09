<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/7/23
 * Time: 下午2:31
 */

namespace Yunshop\Commission\services;


use app\common\models\Order;
use Yunshop\Commission\models\Agents;
use Yunshop\Commission\models\CommissionOrder;
use Yunshop\Commission\models\Log;
use Yunshop\Commission\models\YzMember;
use Yunshop\Love\Common\Models\LoveOrderGoods;

class UpgrateConditionsService
{
    // 一级客户消费满x元 人数达到x个
    public static function buyAndSum($uid, $level_model, $order_model, $self_order_after)
    {
        $agentPraentData = Agents::getLowerData($uid, '1')->get();
        if (!$agentPraentData->isEmpty() && $level_model['upgraded']['buy_and_sum']['buy'] && $level_model['upgraded']['buy_and_sum']['sum']) {
            $sum = 0;
            foreach ($agentPraentData->toArray() as $agent) {
                $buy_price_total = Order::select('id', 'uid', 'price')
                    ->whereStatus(3)
                    ->whereUid($agent['member_id'])
                    ->sum('price');
                if ($buy_price_total >= $level_model['upgraded']['buy_and_sum']['buy']) {
                    $sum += 1;
                }
                if ($sum >= $level_model['upgraded']['buy_and_sum']['sum']) {
                    return true;
                }
            }
            if ($sum < $level_model['upgraded']['buy_and_sum']['sum']) {
                return false;
            }
        }
    }

    // 分销订单金额满x元
    public static function orderMoney($uid, $level_model, $order_model, $self_order_after)
    {
        $order = self::orderCommon($uid);
        // 比较
        if ($order->sum("order.price") < $level_model['upgraded']['order_money']) {
            return false;
        }
        return true;
    }

    // 分销订单数量满X个
    public static function orderCount($uid, $level_model, $order_model, $self_order_after)
    {
        $order = self::orderCommon($uid);
        // 比较
        if ($order->count("id") < $level_model['upgraded']['order_count']) {
            return false;
        }
        return true;
    }

    // 一级分销订单金额满X元
    public static function firstOrderMoney($uid, $level_model, $order_model, $self_order_after)
    {
        $order = self::orderCommon($uid);
        // 一级层级分销订单
        $order = $order->where('hierarchy', '1');
        // 比较
        if ($order->sum("order.price") < $level_model['upgraded']['first_order_money']) {
            return false;
        }
        return true;
    }

    // 一级分销订单数量满X个
    public static function firstOrderCount($uid, $level_model, $order_model, $self_order_after)
    {
        $order = self::orderCommon($uid);
        // 一级层级分销订单
        $order = $order->where('hierarchy', '1');
        // 比较
        if ($order->count("id") < $level_model['upgraded']['first_order_count']) {
            return false;
        }
        return true;
    }

    // 购买指定商品
    public static function goods($uid, $level_model, $order_model, $self_order_after)
    {
        // 如果订单是已付款  并且  等级设置不是 付款后 返回
        /*if ($order_model->status == 1 && $self_order_after != 1) {
            return false;
        }*/
        // 如果订单是已完成  并且  等级设置是 付款后 返回
        /*if ($order_model->status == 3 && $self_order_after == 1) {
            return false;
        }*/
        \YunShop::app()->uniacid = $order_model->uniacid;
        \Setting::$uniqueAccountId = $order_model->uniacid;
        $set = \Setting::get('plugin.commission');
        if (!$set['is_with']) {
            if (!$order_model) {
                return false;
            }
            // 订单商品
            foreach ($order_model->hasManyOrderGoods as $order_goods) {
                // 等级是否设置购买指定商品升级选项
                if (isset($level_model['upgraded']['goods'])) {
                    // 比较
                    if ($order_goods->goods_id == $level_model['upgraded']['goods']) {
                        return true;
                        break;
                    }
                }
            }
            return false;
        }
    }

    // 购买指定商品之一
//    public static function manyGood($uid, $level_model, $order_model, $self_order_after)
//    {
//        \YunShop::app()->uniacid = $order_model->uniacid;
//        \Setting::$uniqueAccountId = $order_model->uniacid;
//            if (!$order_model) {
//                return false;
//            }
//            // 订单商品
//            foreach ($order_model->hasManyOrderGoods as $order_goods) {
//                // 等级是否设置购买指定商品之一升级选项
//                if (isset($level_model['upgraded']['many_good'])) {
//                    // 比较
//                    if (in_array($order_goods->goods_id, $level_model['upgraded']['many_good'])) {
//                        return true;
//                        break;
//                    }
//                }
//            }
//            return false;
//    }

    // 自购订单金额满X元
    public static function selfBuyMoney($uid, $level_model, $order_model, $self_order_after)
    {
        $status = 3;
        if ($self_order_after == 1) {
            $status = 1;
        }
        $price_total = Order::select()->where('status', '>=', $status)->where('uid',$uid)->sum("price");
        if ($price_total < $level_model['upgraded']['self_buy_money']) {
            return false;
        }
        return true;
    }

    // 自购订单数量满X个
    public static function selfBuyCount($uid, $level_model, $order_model, $self_order_after)
    {
        $status = 3;
        if ($self_order_after == 1) {
            $status = 1;
        }
        $order_count = Order::select()->where('status', '>=', $status)->where('uid',$uid)->count();
        if ($order_count < $level_model['upgraded']['self_buy_count']) {
            return false;
        }
        return true;
    }

    // 结算佣金满X元
    public static function settleMoney($uid, $level_model, $order_model, $self_order_after)
    {
        $agentModel = Agents::getAgentByMemberId($uid)->first();
        if ($agentModel->commission_total < $level_model['upgraded']['settle_money']) {
            return false;
        }
        return true;
    }

    // 粉丝数量满X人
    public static function lowerCount($uid, $level_model, $order_model, $self_order_after)
    {
        $memberPraentData = YzMember::getLowerData($uid)->get();
        if ($memberPraentData->count('member_id') < $level_model['upgraded']['lower_count']) {
            return false;
        }
        return true;
    }

    // 一级粉丝数量满X人
    public static function firstLowerCount($uid, $level_model, $order_model, $self_order_after)
    {
        $memberPraentData = YzMember::getLowerData($uid, '1')->get();
        if ($memberPraentData->count('member_id') < $level_model['upgraded']['lower_count']) {
            return false;
        }
        return true;
    }

    // 粉丝分销商满X人
    public static function lowerAgentCount($uid, $level_model, $order_model, $self_order_after)
    {
        $agentPraentData = Agents::getLowerData($uid)->get();
        if ($agentPraentData->count('id') < $level_model['upgraded']['lower_agent_count']) {
            return false;
        }
        return true;
    }

    // 一级粉丝分销商满X人
    public static function firstLowerAgentCount($uid, $level_model, $order_model, $self_order_after)
    {
        $agentPraentData = Agents::getLowerData($uid, '1')->get();
        if ($agentPraentData->count('id') < $level_model['upgraded']['first_lower_agent_count']) {
            return false;
        }
        return true;
    }

    private static function orderCommon($uid)
    {
        // 分销订单build
        $orderModel = CommissionOrder::getCommissionOrderByMemberId($uid);
        $order = $orderModel->get();
        // 没有分销订单
        if ($order->isEmpty()) {
            return collect([]);
        }
        // 返回分销订单集合
        return $order;
    }
}