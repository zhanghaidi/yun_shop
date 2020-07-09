<?php

namespace Yunshop\Commission\services;

use app\common\models\Goods;
use app\common\models\order\OrderDeduction;
use Illuminate\Support\Facades\Log;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\Commission;
use Yunshop\Commission\models\Operation;
use Yunshop\Hotel\common\models\Hotel;
use Yunshop\Hotel\common\models\HotelOrder;
use Yunshop\Hotel\common\models\HotelSetting;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\StoreCashier\common\models\StoreSetting;


/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/15
 * Time: 下午3:05
 */
class CommissionOrderService
{
    /**
     * @param $search
     * @return array
     */
    public static function getSearch($search)
    {
//        echo '<pre>'; print_r($search); exit;
        $search = [
            'member' => $search['member'] ? $search['member'] : '',
            'order' => $search['order'] ? $search['order'] : '',
            'status' => isset($search['status']) ? $search['status'] : '',
            'hierarchy' => $search['hierarchy'] ? $search['hierarchy'] : '',
            'level' => $search['level'] ? $search['level'] : '',
            'member_id' => $search['member_id'] ? $search['member_id'] : '',
            'statistics' => $search['statistics'] ? $search['statistics'] : '',
            'is_time' => $search['is_time'] ? $search['is_time'] : '',
            'time' => $search['time'] ? $search['time'] : [],
            'is_pay' => $search['is_pay']
        ];
        if ($search['status'] == '3') {
            $search['status'] = '2';
            $search['withdraw'] = '0';
        }
        if ($search['status'] == '4') {
            $search['status'] = '2';
            $search['withdraw'] = '1';
        }
        if($search['is_time']){
            $search['time'];
        }
        return $search;
    }

    /**
     * @param $level
     * @return string
     * 分销商层级转换
     */
    public static function getHierarchy($level)
    {
        switch ($level) {
            case 'first_level':
                $hierarchy = '1';//分销层级
                break;
            case 'second_level':
                $hierarchy = '2';//分销层级
                break;
            default:
                $hierarchy = '3';//分销层级
        }
        return $hierarchy;
    }

    /**
     * @param $orderModel
     * @param $orderGoods
     * @param $agent
     * @param $set
     * @return array
     * 佣金计算规则 计算佣金 计算方式
     */
    public static function getCountAmount($orderModel, $orderGoods, $agent, $set)
    {
        $amount = 0;
        $method = "";
        if (isset($set['culate_method_plus'])) {
            foreach ($set['culate_method_plus'] as $key => $plus) {
                $methods = $key . 'Plus';
                $amount += static::$methods($orderGoods, $orderModel);
                $method .= "+" . static::getMethodName($key);
            }
        }
        if (isset($set['culate_method_minus'])) {
            foreach ($set['culate_method_minus'] as $key => $minus) {
                $methods = $key . 'Minus';
                $amount -= static::$methods($orderGoods, $orderModel);
                $method .= "-" . static::getMethodName($key);
            }
        }
        //获取对应层级比例
        $rate = static::getRate($agent, $set, $orderModel);
        //结算金额乘以比例
        $commission = $amount / 100 * $rate;
        //如果为负数则为0
        $commission = $commission > 0 ? $commission : 0;
        return [
            'amount' => $amount,
            'method' => $method,
            'rate' => $rate,
            'commission' => $commission
        ];
    }

    /**
     * @param $agent
     * @param $set
     * @return mixed
     * 获取佣金比例
     * 权重: 分销商等级比例->默认比例
     */
    public static function getRate($agent, $set, $orderModel)
    {
        if ($orderModel->plugin_id == 48 && app('plugins')->isEnabled('hotel-supply')) {
            \Setting::$uniqueAccountId = $orderModel->uniacid;
            $setting = \Setting::get('plugin.hotel_supply');
            if (empty($agent['agent_level'])) {
                return $setting['commission']['rule']['level_0'][$agent['hierarchy'] . '_rate'];
            } else {
                return $setting['commission']['rule']['level_' . $agent['agent_level']['id']][$agent['hierarchy'] . '_rate'];
            }
        }

        if ($orderModel->plugin_id == 32 && app('plugins')->isEnabled('store-cashier')) {
            $store_order = StoreOrder::getOrderByOrderId($orderModel->id)->first();
            $storeSetting = StoreSetting::getStoreSettingByStoreId($store_order->store_id)->where('key', 'commission')->first();
            if ($storeSetting) {
                $set = $storeSetting->value;
                if (empty($agent['agent_level'])) {

                    /*Operation::create([
                        'uniacid' => $orderModel->uniacid,
                        'order_id' => $orderModel->id,
                        'uid' => $agent['member_id'],
                        'buy_uid' => $orderModel->uid,
                        'level_id' => $agent['agent_level_id'],
                        'ratio' => $set[$agent['hierarchy']],
                        'content' => "没有分销等级,返回门店默认比例[{$set[$agent['hierarchy']]}]"
                    ]);*/

                    return $set[$agent['hierarchy']];
                } else {

                    /*Operation::create([
                        'uniacid' => $orderModel->uniacid,
                        'order_id' => $orderModel->id,
                        'uid' => $agent['member_id'],
                        'buy_uid' => $orderModel->uid,
                        'level_id' => $agent['agent_level_id'],
                        'ratio' => $set['rule']['level_' . $agent['agent_level']['id']][$agent['hierarchy'] . '_rate'],
                        'content' => "返回门店默认等级比例[{$set['rule']['level_' . $agent['agent_level']['id']][$agent['hierarchy'] . '_rate']}]"
                    ]);*/

                    return $set['rule']['level_' . $agent['agent_level']['id']][$agent['hierarchy'] . '_rate'];
                }
            }
        }
        if ($orderModel->plugin_id == 33 && app('plugins')->isEnabled('hotel')) {
            $hotel_order = HotelOrder::getOrderByOrderId($orderModel->id)->first();
            $hotelSetting = HotelSetting::getHotelSettingByHotelId($hotel_order->hotel_id)->where('key', 'commission')->first();
            if ($hotelSetting) {
                $set = $hotelSetting->value;
                if (empty($agent['agent_level'])) {

                    /*Operation::create([
                        'uniacid' => $orderModel->uniacid,
                        'order_id' => $orderModel->id,
                        'uid' => $agent['member_id'],
                        'buy_uid' => $orderModel->uid,
                        'level_id' => $agent['agent_level_id'],
                        'ratio' => $set[$agent['hierarchy']],
                        'content' => "没有分销等级,返回门店默认比例[{$set[$agent['hierarchy']]}]"
                    ]);*/

                    return $set[$agent['hierarchy']];
                } else {

                    /*Operation::create([
                        'uniacid' => $orderModel->uniacid,
                        'order_id' => $orderModel->id,
                        'uid' => $agent['member_id'],
                        'buy_uid' => $orderModel->uid,
                        'level_id' => $agent['agent_level_id'],
                        'ratio' => $set['rule']['level_' . $agent['agent_level']['id']][$agent['hierarchy'] . '_rate'],
                        'content' => "返回门店默认等级比例[{$set['rule']['level_' . $agent['agent_level']['id']][$agent['hierarchy'] . '_rate']}]"
                    ]);*/

                    return $set['rule']['level_' . $agent['agent_level']['id']][$agent['hierarchy'] . '_rate'];
                }
            }
        }

        if (empty($agent['agent_level'])) {
            /*Operation::create([
                'uniacid' => $orderModel->uniacid,
                'order_id' => $orderModel->id,
                'uid' => $agent['member_id'],
                'buy_uid' => $orderModel->uid,
                'level_id' => $agent['agent_level_id'],
                'ratio' => $set[$agent['hierarchy']],
                'content' => "没有分销等级,返回分销默认比例[{$set[$agent['hierarchy']]}]"
            ]);*/
            return $set[$agent['hierarchy']];
        } else {
            /*Operation::create([
                'uniacid' => $orderModel->uniacid,
                'order_id' => $orderModel->id,
                'uid' => $agent['member_id'],
                'buy_uid' => $orderModel->uid,
                'level_id' => $agent['agent_level_id'],
                'ratio' => $agent['agent_level'][$agent['hierarchy']],
                'content' => "返回分销等级比例[{$agent['agent_level'][$agent['hierarchy']]}]"
            ]);*/
            return $agent['agent_level'][$agent['hierarchy']];
        }
    }

    /**
     * @param $orderGoods
     * @param $orderModel
     * @return mixed
     * 实际支付金额
     * （商品最终价格 - 总优惠/商品最终价格总和 * 商品最终价格 ）* 分销比例
     */
    public static function actualPlus($orderGoods, $orderModel)
    {
        return $orderGoods->payment_amount;
    }

    /**
     * @param $orderGoods
     * @param $orderModel
     * @return mixed
     * 加运费
     */
    public static function freightPlus($orderGoods, $orderModel)
    {
        return $orderModel->dispatch_price;
    }

    /**
     * @param $orderGoods
     * @param $orderModel
     * @return mixed
     * 加商品原价
     */
    public static function pricePlus($orderGoods, $orderModel)
    {
//        $goods = Goods::getGoodsById($orderGoods->goods_id);
        return $orderGoods->goods_market_price;
    }

    /**
     * @param $orderGoods
     * @param $orderModel
     * @return mixed
     * 加商品现价
     */
    public static function market_pricePlus($orderGoods, $orderModel)
    {
//        $goods = Goods::getGoodsById($orderGoods->goods_id);
        return $orderGoods->goods_price;
    }

    /**
     * @param $orderGoods
     * @param $orderModel
     * @return mixed
     * 加商品成本价
     */
    public static function cost_pricePlus($orderGoods, $orderModel)
    {
//        $goods = Goods::getGoodsById($orderGoods->goods_id);
        return $orderGoods->goods_cost_price;
    }

    /**
     * 华侨币插件抵扣部分参与分销
     * @param $orderGoods
     * @param $orderModel
     * @return string
     */
    public static function coin_deductionPlus($orderGoods, $orderModel)
    {
        if (\YunShop::plugin()->get('coin')) {
            $orderDeductionModel = OrderDeduction::select('amount')->where('order_id', $orderModel->id)->where('code', 'coin')->first();
            if ($orderDeductionModel) {
                return $orderDeductionModel->amount;
            }
        }
        return '0';
    }

    /**
     * @param $orderGoods
     * @param $orderModel
     * @return mixed
     * 减运费
     */
    public static function freightMinus($orderGoods, $orderModel)
    {
        return $orderModel->dispatch_price;
    }

    /**
     * @param $orderGoods
     * @param $orderModel
     * @return mixed
     * 减成本
     */
    public static function costMinus($orderGoods, $orderModel)
    {
//        $goods = Goods::getGoodsById($orderGoods->goods_id);
        return $orderGoods->goods_cost_price;
    }

    /**
     * @param $culateMethod
     * @return string
     * 佣金计算方试名称转换
     */
    public static function getMethodName($culateMethod)
    {
        switch ($culateMethod) {
            case 'actual':
                $name = "商品实际支付金额";
                break;
            case 'freight':
                $name = "运费";
                break;
            case 'point':
                $name = "积分抵扣";
                break;
            case 'balance':
                $name = "余额抵扣";
                break;
            case 'price':
                $name = "商品原价";
                break;
            case 'market_price':
                $name = "商品现价";
                break;
            case 'cost_price':
                $name = "商品成本价";
                break;
            case 'point':
                $name = "奖励积分";
                break;
            case 'cost':
                $name = "成本";
                break;

            default:
                $name = "";
        }
        return $name;
    }

    public static function getAdditionalCountAmount($orderModel, $orderGoods, $agent, $set)
    {
        $amount = 0;
        $method = "";
        if (isset($set['culate_method_plus'])) {
            foreach ($set['culate_method_plus'] as $key => $plus) {
                $methods = $key . 'Plus';
                $amount += static::$methods($orderGoods, $orderModel);
                $method .= "+" . static::getMethodName($key);
            }
        }
        if (isset($set['culate_method_minus'])) {
            foreach ($set['culate_method_minus'] as $key => $minus) {
                $methods = $key . 'Minus';
                $amount -= static::$methods($orderGoods, $orderModel);
                $method .= "-" . static::getMethodName($key);
            }
        }
        //获取对应层级比例
        $rate = $agent['agent_level']['additional_ratio'];
        //结算金额乘以比例
        $commission = $amount / 100 * $rate;
        return [
            'amount' => $amount,
            'method' => $method,
            'rate' => $rate,
            'commission' => $commission
        ];
    }

    public static function getAdditionalCommission($orderModel, $agent, $set)
    {
        $orderGoods = $orderModel->hasManyOrderGoods;
        $commissionAmount = 0;
        $formula = '';
        $commissionRate = 0;
        $commission = 0;


        //临时解决分销等级删除后，分销订单不能使用默认等级计算问题
        if (!$agent['agent_level_id'] || !$agent['agent_level']['additional_ratio']) {
            return [
                'commission_amount' => $commissionAmount,
                'formula' => $formula,
                'commission_rate' => $commissionRate,
                'commission' => $commission,
                'orderGoods' => $orderGoods
            ];
        }

        foreach ($orderGoods as $key => $og) {

            //获取商品分销设置信息
            $commissionGoods = Commission::getGoodsById($og->goods_id)->first();
            if (!$commissionGoods->is_commission) {
                continue;
            }
            //分销订单商品 商品分销设置信息默认值
            $orderGoods[$key]['commissionGoods'] = [
                'has_commission' => '0',
                'commission_rate' => $agent['agent_level']['additional_ratio'],
                'commission_pay' => 0,
            ];
            //分销订单商品 商品信息
            $orderGoods[$key]['goods'] = [
                'name' => $og->title,
                'thumb' => $og->thumb,
            ];
            if ($commissionGoods) {
                $countAmount = static::getAdditionalCountAmount($orderModel, $og, $agent, $set);
                $commissionAmount += $countAmount['amount'];//分佣计算金额
                $formula = $countAmount['method'];//分佣计算方式
                $commissionRate = $countAmount['rate'];//分佣比例
                $commission += $countAmount['commission'];//佣金
            }
        }
        return [
            'commission_amount' => $commissionAmount,
            'formula' => $formula,
            'commission_rate' => $commissionRate,
            'commission' => $commission,
            'orderGoods' => $orderGoods
        ];
    }

    /**
     * @param $orderModel
     * @param $agent
     * @param $set
     * @return array
     * 获取佣金 计算金额 计算公式 佣金比例 分销订单商品等数据
     */
    public static function getCommission($orderModel, $agent, $set)
    {
        $orderGoods = $orderModel->hasManyOrderGoods;
        $commissionAmount = 0;
        $formula = '';
        $commissionRate = 0;
        $commissionPay = 0;
        $commission = 0;

        //运费需要最后计算订单的总运费而不是每个商品计算一次 （以下全部用于加减运费）
        $is_general = false;//是否走统一
        $is_commission = false;//判断订单的全部商品是否有开启分红
        $plus_freight = false;//是否加运费
        $minus_freight = false;//是否减运费
        if (isset($set['culate_method_plus']['freight'])) {
            $plus_freight = true;
            unset($set['culate_method_plus']['freight']);
        }
        if (isset($set['culate_method_minus']['freight'])) {
            $minus_freight = true;
            unset($set['culate_method_minus']['freight']);
        }

        //临时解决分销等级删除后，分销订单不能使用默认等级计算问题
        if ($agent['agent_level_id']) {
            $agentLevelModel = AgentLevel::find($agent['agent_level_id']);
            if (!$agentLevelModel) {
                $agent['agent_level_id'] = 0;
            }
        }

        foreach ($orderGoods as $key => $og) {

            //获取商品分销设置信息
            $commissionGoods = Commission::getGoodsById($og->goods_id)->first();
            //分销订单商品 商品分销设置信息默认值
            $orderGoods[$key]['commissionGoods'] = [
                'has_commission' => '0',
                'commission_rate' => $agent['agent_level'][$agent['hierarchy']],
                'commission_pay' => 0,
            ];
            //分销订单商品 商品信息
            $orderGoods[$key]['goods'] = [
                'name' => $og->title,
                'thumb' => $og->thumb,
            ];
            if ($commissionGoods && $commissionGoods->is_commission) {
                $is_commission = true;//用于加减运费
                if ($commissionGoods['has_commission'] == '1') {
                    //商品独立佣金
                    $commissionAmount += $og['payment_amount']; //分佣计算金额
                    $formula .= "+商品独立佣金";//分佣计算方式
                    $rule = unserialize($commissionGoods['rule']);
                    $agentRule = $rule['level_' . $agent['agent_level_id']];
                    if ($agentRule[$agent['hierarchy'] . '_rate'] > 0) {
                        $commissionRate = $agentRule[$agent['hierarchy'] . '_rate'];
                        $commission += ($og['payment_amount']) / 100 * $commissionRate;
                    } elseif ($agentRule[$agent['hierarchy'] . '_pay'] > 0) {

                        $commissionPay = $agentRule[$agent['hierarchy'] . '_pay'];
                        $commission += $agentRule[$agent['hierarchy'] . '_pay'] * $og['total'];
                    }
                    $orderGoods[$key]['commissionGoods'] = [
                        'has_commission' => '1',
                        'commission_rate' => $commissionRate,
                        'commission_pay' => $commissionPay,
                    ];

                } else {
                    $is_general = true;
                    $countAmount = static::getCountAmount($orderModel, $og, $agent, $set);
                    $commissionAmount += $countAmount['amount'];//分佣计算金额
                    $formula .= $countAmount['method'];//分佣计算方式
                    $commissionRate = $countAmount['rate'];//分佣比例
                    $commission += $countAmount['commission'];//佣金
                }
            } else {
                // 酒店供应链订单
                if ($orderModel->plugin_id == 48) {
                    $countAmount = static::getCountAmount($orderModel, $og, $agent, $set);
                    $commissionAmount += $countAmount['amount'];//分佣计算金额
                    $formula .= $countAmount['method'];//分佣计算方式
                    $commissionRate = $countAmount['rate'];//分佣比例
                    $commission += $countAmount['commission'];//佣金
                }
            }
//            if (!$commissionGoods->is_commission) {
//                continue;
//            }
//            if ($commissionGoods) {
//                $is_commission = true;//用于加减运费
//                if ($commissionGoods['has_commission'] == '1') {
//                    //商品独立佣金
//                    $commissionAmount += $og['payment_amount']; //分佣计算金额
//                    $formula .= "+商品独立佣金";//分佣计算方式
//                    $rule = unserialize($commissionGoods['rule']);
//                    $agentRule = $rule['level_' . $agent['agent_level_id']];
//                    if ($agentRule[$agent['hierarchy'] . '_rate'] > 0) {
//                        $commissionRate = $agentRule[$agent['hierarchy'] . '_rate'];
//                        $commission += ($og['payment_amount']) / 100 * $commissionRate;
//                        /*Operation::create([
//                            'uniacid' => $orderModel->uniacid,
//                            'order_id' => $orderModel->id,
//                            'uid' => $agent['member_id'],
//                            'buy_uid' => $orderModel->uid,
//                            'level_id' => $agent['agent_level_id'],
//                            'ratio' => $commissionRate,
//                            'content' => "goods_id[{$og->goods_id}]商品独立设置-比例,佣金[{$commission}]"
//                        ]);*/
//                    } elseif ($agentRule[$agent['hierarchy'] . '_pay'] > 0) {
//
//                        $commissionPay = $agentRule[$agent['hierarchy'] . '_pay'];
//                        $commission += $agentRule[$agent['hierarchy'] . '_pay'] * $og['total'];
//
//                        /*Operation::create([
//                            'uniacid' => $orderModel->uniacid,
//                            'order_id' => $orderModel->id,
//                            'uid' => $agent['member_id'],
//                            'buy_uid' => $orderModel->uid,
//                            'level_id' => $agent['agent_level_id'],
//                            'ratio' => 0,
//                            'content' => "goods_id[{$og->goods_id}]商品独立设置-固定金额[{$commission}]"
//                        ]);*/
//                    }
//
//                    $orderGoods[$key]['commissionGoods'] = [
//                        'has_commission' => '1',
//                        'commission_rate' => $commissionRate,
//                        'commission_pay' => $commissionPay,
//                    ];
//
//                } else {
//                    $is_general = true;
//                    $countAmount = static::getCountAmount($orderModel, $og, $agent, $set);
//                    $commissionAmount += $countAmount['amount'];//分佣计算金额
//                    $formula .= $countAmount['method'];//分佣计算方式
//                    $commissionRate = $countAmount['rate'];//分佣比例
//                    $commission += $countAmount['commission'];//佣金
//                }
//            }
        }

        //加减运费
        if ($is_commission && $is_general) {
            if ($plus_freight) {//加运费
                $freight_amount = static::freightPlus('', $orderModel);
                $commissionAmount += $freight_amount;
                $formula .= "+" . static::getMethodName('freight');
                $freight_rate = static::getRate($agent, $set, $orderModel);
                $commission += $freight_amount / 100 * $freight_rate;
            }
            if ($minus_freight) {//减运费
                $freight_amount = static::freightMinus('', $orderModel);
                $commissionAmount -= $freight_amount;
                $formula .= "-" . static::getMethodName('freight');
                $freight_rate = static::getRate($agent, $set, $orderModel);
                $commission -= $freight_amount / 100 * $freight_rate;
            }
        }

        return [
            'commission_amount' => $commissionAmount,
            'formula' => $formula,
            'commission_rate' => $commissionRate,
            'commission' => $commission,
            'orderGoods' => $orderGoods
        ];
    }

    public static function getAlone()
    {
        return 10;

    }

    /**
     * 预计分红
     * @param $orderModel
     * @param $agent
     * @param $set
     * @return float|int
     */
    public static function expectedDividends($orderModel, $set)
    {
        $orderGoods = $orderModel->hasManyOrderGoods;
        $agentLevel = AgentLevel::uniacid()->select('id', 'first_level', 'second_level', 'third_level')->get()->toArray();

        $agentLevel[]['id'] = 0;
        $agentLevel[]['first_level'] = $set['first_level'];
        $agentLevel[]['second_level'] = $set['second_level'];
        $agentLevel[]['third_level'] = $set['third_level'];

        $firstCommission = [];
        $secondCommission = [];
        $thirdCommission = [];
        $agentData = [];
        $commission = 0;

        foreach ($orderGoods as $key => $og) {

            //获取商品分销设置信息
            $commissionGoods = Commission::getGoodsById($og->goods_id)->first();
            if (!$commissionGoods->is_commission) {
                continue;
            }

            if ($commissionGoods['has_commission'] == '1') {
                //商品独立佣金
                $rule = unserialize($commissionGoods['rule']);
                foreach ($agentLevel as $agent) {

                    $agentRule = $rule['level_' . $agent['id']];
                    if ($agentRule['first_level_rate'] > 0) {
                        $firstCommission[$agent['id']] = ($og['payment_amount']) / 100 * $agentRule['first_level_rate'];
                    } elseif ($agentRule['first_level_pay'] > 0) {
                        $firstCommission[$agent['id']] = $agentRule['first_level_pay'] * $og['total'];
                    }
                    if ($agentRule['second_level_rate'] > 0) {
                        $secondCommission[$agent['id']] = ($og['payment_amount']) / 100 * $agentRule['second_level_rate'];
                    } elseif ($agentRule['second_level_pay'] > 0) {
                        $secondCommission[$agent['id']] = $agentRule['second_level_pay'] * $og['total'];
                    }
                    if ($agentRule['third_level_rate'] > 0) {
                        $thirdCommission[$agent['id']] = ($og['payment_amount']) / 100 * $agentRule['third_level_rate'];
                    } elseif ($agentRule['third_level_pay'] > 0) {
                        $thirdCommission[$agent['id']] = $agentRule['third_level_pay'] * $og['total'];
                    }
                }
                if ($set['level'] >= 1) {
                    $agentData[0] = max($firstCommission);
                }
                if ($set['level'] >= 2) {
                    $agentData[1] = max($secondCommission);
                }
                if ($set['level'] == 3) {
                    $agentData[2] = max($thirdCommission);
                }
                $commission += array_sum($agentData);

            } else {
                $countAmount = 0;
                if (isset($set['culate_method_plus'])) {
                    foreach ($set['culate_method_plus'] as $key => $plus) {
                        $methods = $key . 'Plus';
                        $countAmount += static::$methods($orderGoods, $orderModel);
                    }
                }
                if (isset($set['culate_method_minus'])) {
                    foreach ($set['culate_method_minus'] as $key => $minus) {
                        $methods = $key . 'Minus';
                        $countAmount -= static::$methods($orderGoods, $orderModel);
                    }
                }
                if ($orderModel->plugin_id == 32 && app('plugins')->isEnabled('store-cashier')) {
                    $store_order = StoreOrder::getOrderByOrderId($orderModel->id)->first();
                    $storeSetting = StoreSetting::getStoreSettingByStoreId($store_order->store_id)->where('key', 'commission')->first();
                    if ($storeSetting) {
                        $set = $storeSetting->value;
                        $firstCommission[0] = ($countAmount['amount']) / 100 * $set['first_level'];
                        $secondCommission[0] = ($countAmount['amount']) / 100 * $set['second_level'];
                        $thirdCommission[0] = ($countAmount['amount']) / 100 * $set['third_level'];
                        foreach ($agentLevel as $agent) {
                            $firstRate[$agent['id']] = ($countAmount['amount']) / 100 * $set['rule']['level_' . $agent['id']]['first_rate'];
                            $secondRate[$agent['id']] = ($countAmount['amount']) / 100 * $set['rule']['level_' . $agent['id']]['second_level'];
                            $thirdRate[$agent['id']] = ($countAmount['amount']) / 100 * $set['rule']['level_' . $agent['id']]['third_level'];
                        }

                        if ($set['level'] >= 1) {
                            $agentData[] = max($firstCommission);
                        }
                        if ($set['level'] >= 2) {
                            $agentData[] = max($secondCommission);
                        }
                        if ($set['level'] == 3) {
                            $agentData[] = max($thirdCommission);
                        }
                    }
                } else {
                    foreach ($agentLevel as $agent) {

                        $firstCommission[$agent['id']] = ($countAmount['amount']) / 100 * $agent['first_level'];
                        $secondCommission[$agent['id']] = ($countAmount['amount']) / 100 * $agent['second_level'];
                        $thirdCommission[$agent['id']] = ($countAmount['amount']) / 100 * $agent['third_level'];
                    }
                    if ($set['level'] >= 1) {
                        $agentData[] = max($firstCommission);
                    }
                    if ($set['level'] >= 2) {
                        $agentData[] = max($secondCommission);
                    }
                    if ($set['level'] == 3) {
                        $agentData[] = max($thirdCommission);
                    }
                }

                $commission += array_sum($agentData);//佣金
            }

        }
        return $commission;
    }

    /**
     * @param $commissionData
     * @param $times
     */
    public static function getIncomeDetail($commissionData, $times)
    {
        \Log::info('分佣时间:',$commissionData['recrive_at']);
        if($commissionData['recrive_at']){
             $recrive_at = date("Y-m-d H:i:s", $commissionData['recrive_at']);
        }else{
//            $recrive_at = $commissionData['updated_at'] == '1970-01-01 08:00' ? $commissionData['created_at'] : $commissionData['updated_at'] ;
            $recrive_at = date('Y-m-d', $commissionData['recrive_at']) == '1970-01-01' ? $commissionData['created_at'] : $commissionData['updated_at'] ;
        }
        $data = [
            'commission' => [
                'title' => trans('Yunshop\Commission::index.title'),
                'data' => [
                    '0' => [
                        'title' => trans('Yunshop\Commission::index.commission'),
                        'value' => $commissionData['commission'] . "元",
                    ],
//                    '1' => [
//                        'title' => '分销层级',
//                        'value' => $item['hierarchy'] . "级",
//                    ],
                    '2' => [
                        'title' => '佣金比例',
                        'value' => $commissionData['commission_rate'] . "%",
                    ],
                    '3' => [
                        'title' => '结算天数',
                        'value' => $commissionData['settle_days'] . "天",
                    ],
                    '4' => [
                        'title' => '佣金方式',
                        'value' => $commissionData['formula'],
                    ],
                    '5' => [
                        'title' => '分佣时间',
                        'value' => $recrive_at,
                    ],
                    '6' => [
                        'title' => '结算时间',
                        'value' => date("Y-m-d H:i:s", $times)
                    ],
                ]

            ],
            'order' => [
                'title' => '订单',
                'data' => [
                    '0' => [
                        'title' => '订单号',
                        'value' => $commissionData['order']['order_sn'],
                    ],
                    '1' => [
                        'title' => '状态',
                        'value' => $commissionData['order']['status_name'],
                    ],
                ]
            ]
        ];
        $data['goods']['title'] = '商品';
        foreach ($commissionData['order_goods'] as $key => $order_good) {

            $data['goods']['data'][$key][] = [
                'title' => '名称',
                'value' => $order_good['title'],
            ];
            $data['goods']['data'][$key][] = [
                'title' => '金额',
                'value' => $order_good['goods_price'] . "元",
            ];
        }
        return $data;
    }

    /**
     * @param $commissionData
     * @param $config
     * @param $data
     */
    public static function getIncomeData($commissionData, $config, $data)
    {
        $incomeData = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $commissionData['member_id'],
            'incometable_type' => $config['class'],
            'incometable_id' => $commissionData['id'],
            'type_name' => $config['title'],
            'amount' => $commissionData['commission'],
            'status' => '0',
            'detail' => json_encode($data),//收入明细数据
            'create_month' => date("Y-m"),
        ];
        return $incomeData;
    }

    public static function getHighestRate($set)
    {
        $firstLevel = 0;
        $secondLevel = 0;
        $thirdLevel = 0;
        $agentData = 0;
        $agentLevel = AgentLevel::uniacid()->get();


        return $agentData;
    }

    public static function getGoodsHighestRate($set, $rule)
    {
        $firstLevel = 0;
        $secondLevel = 0;
        $thirdLevel = 0;
        $agentData = 0;
        $agentLevel = AgentLevel::uniacid()->get();
        foreach ($agentLevel as $agent) {
            if ($rule['level_' . $agent['level']]['first_level_rate'] > 0) {
                $firstLevel[] = $rule['level_' . $agent['level']]['first_level_rate'];
            } elseif ($rule['level_' . $agent['level']]['first_level_pay'] > 0) {
                $firstLevel[] = $rule['level_' . $agent['level']]['first_level_pay'];
            }

            $secondLevel[] = $agent['second_level'];
            $thirdLevel[] = $agent['third_level'];
        }

        if ($set['level'] >= 1) {
            $agentData[] = max($firstLevel);
        }
        if ($set['level'] >= 2) {
            $agentData[] = max($secondLevel);
        }
        if ($set['level'] == 3) {
            $agentData[] = max($thirdLevel);
        }
    }

}