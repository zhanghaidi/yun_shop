<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/24
 * Time: 下午4:35
 */

namespace app\frontend\modules\order\services;
use app\common\exceptions\AppException;
use app\common\models\DispatchType;
use app\common\models\Order;
use app\common\models\order\OrderGoodsChangePriceLog;
use app\common\modules\orderGoods\OrderGoodsCollection;
use \app\common\models\MemberCart;
use app\frontend\modules\order\services\behavior\OrderCancelPay;
use app\frontend\modules\order\services\behavior\OrderCancelSend;
use app\frontend\modules\order\services\behavior\OrderChangePrice;
use app\frontend\modules\order\services\behavior\OrderClose;
use app\frontend\modules\order\services\behavior\OrderDelete;
use app\frontend\modules\order\services\behavior\OrderForceClose;
use app\frontend\modules\order\services\behavior\OrderOperation;
use app\frontend\modules\order\services\behavior\OrderPay;
use app\frontend\modules\order\services\behavior\OrderReceive;
use app\frontend\modules\order\services\behavior\OrderSend;
use app\frontend\modules\orderGoods\models\PreOrderGoods;
use app\frontend\modules\orderGoods\models\PreOrderGoodsCollection;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use app\common\facades\Setting;

class OrderService
{
    public static $ju_url = "https://open.erp321.com/api/open/query.aspx";

    /**
     * 获取订单商品对象数组
     * @param Collection $memberCarts
     * @return OrderGoodsCollection
     * @throws \Exception
     */
    public static function getOrderGoods(Collection $memberCarts)
    {
        if ($memberCarts->isEmpty()) {
            throw new AppException("购物车记录为空");
        }
        $result = $memberCarts->map(function ($memberCart) {
            if (!($memberCart instanceof MemberCart)) {
                throw new \Exception("请传入" . MemberCart::class . "的实例");
            }
            /**
             * @var $memberCart MemberCart
             */

            $data = [
                'goods_id' => (int)$memberCart->goods_id,
                'goods_option_id' => (int)$memberCart->option_id,
                'total' => (int)$memberCart->total,
            ];
            $orderGoods = app('OrderManager')->make('PreOrderGoods', $data);
            /**
             * @var PreOrderGoods $orderGoods
             */
            $orderGoods->setRelation('goods', $memberCart->goods);
            $orderGoods->setRelation('goodsOption', $memberCart->goodsOption);
            return $orderGoods;
        });

        return new PreOrderGoodsCollection($result);
    }

    /**
     * 获取订单号
     * @return string
     */
    public static function createOrderSN()
    {
        $orderSN = createNo('SN', true);
        while (1) {
            if (!Order::where('order_sn', $orderSN)->first()) {
                break;
            }
            $orderSN = createNo('SN', true);
        }
        return $orderSN;
    }

    /**
     * 获取支付流水号
     * @return string
     */
    public static function createPaySN()
    {
        $paySN = createNo('PN', true);
        while (1) {
            if (!\app\common\models\OrderPay::where('pay_sn', $paySN)->first()) {
                break;
            }
            $paySN = createNo('PN', true);
        }
        return $paySN;
    }

    /**
     * 订单操作类
     * @param OrderOperation $orderOperation
     * @return string
     * @throws AppException
     */
    private static function OrderOperate(OrderOperation $orderOperation)
    {
        if (!isset($orderOperation)) {
            throw new AppException('未找到该订单');
        }
        DB::transaction(function () use ($orderOperation) {
            $orderOperation->handle();
        });
    }

    /**
     * 取消付款
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderCancelPay($param)
    {
        $orderOperation = OrderCancelPay::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 取消发货
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderCancelSend($param)
    {
        $orderOperation = OrderCancelSend::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 关闭订单
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderClose($param)
    {
        $orderOperation = OrderClose::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 强制关闭订单
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderForceClose($param)
    {
        $orderOperation = OrderForceClose::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 用户删除(隐藏)订单
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderDelete($param)
    {
        $orderOperation = OrderDelete::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 根据流水号合并支付
     * @param array $param
     * @throws AppException
     */
    public static function ordersPay(array $param)
    {
        \Log::info('---------订单支付ordersPay(order_pay_id:' . $param['order_pay_id'] . ')--------', $param);
        /**
         * @var \app\frontend\models\OrderPay $orderPay
         */
        $orderPay = \app\frontend\models\OrderPay::find($param['order_pay_id']);
        if (!isset($orderPay)) {
            throw new AppException('支付流水记录不存在');
        }

        if (isset($param['pay_type_id'])) {
            if ($orderPay->pay_type_id != $param['pay_type_id']) {
                \Log::error("---------支付回调与与支付请求的订单支付方式不匹配(order_pay_id:{$orderPay->id},orderPay->payTypeId:{$orderPay->pay_type_id} != param[pay_type_id]:{$param['pay_type_id']})--------", []);
                $orderPay->pay_type_id = $param['pay_type_id'];

            }
        }
        $orderPay->pay();

        \Log::info('---------订单支付成功ordersPay(order_pay_id:' . $orderPay->id . ')--------', []);

    }

    /**
     * 后台支付订单
     * @param array $param
     * @return string
     * @throws AppException
     */

    public static function orderPay(array $param)
    {
        /**
         * @var OrderOperation $orderOperation
         */
        $orderOperation = OrderPay::find($param['order_id']);

        if (isset($param['pay_type_id'])) {
            $orderOperation->pay_type_id = $param['pay_type_id'];
        }
        $orderOperation->order_pay_id = (int)$param['order_pay_id'];

        $result = self::OrderOperate($orderOperation);
        //是虚拟商品或有标识直接完成
        if ($orderOperation->isVirtual() || $orderOperation->mark) {
            // 虚拟物品付款后直接完成
            $orderOperation->dispatch_type_id = 0;
            $orderOperation->save();
            self::orderSend(['order_id' => $orderOperation->id]);
            $result = self::orderReceive(['order_id' => $orderOperation->id]);
        } elseif (isset($orderOperation->hasOneDispatchType) && !$orderOperation->hasOneDispatchType->needSend()) {
            // 不需要发货的物品直接改为待收货
            self::orderSend(['order_id' => $orderOperation->id]);
        }

        return $result;
    }

    /**
     * 收货
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderReceive($param)
    {
        $orderOperation = OrderReceive::find($param['order_id']);

        return self::OrderOperate($orderOperation);
    }

    /**
     * 发货
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function orderSend($param)
    {
        // \Log::info('---param---', $param);
        $orderOperation = OrderSend::find($param['order_id']);
        $orderOperation->params = $param;
        // \Log::info('----1orderOperation--', $orderOperation);
        return self::OrderOperate($orderOperation);
    }

    /**
     * 改变订单价格
     * @param $param
     * @return string
     * @throws AppException
     */
    public static function changeOrderPrice($param)
    {
        $order = OrderChangePrice::find($param['order_id']);
        /**
         * @var $order OrderChangePrice
         */
        if (!isset($order)) {
            throw new AppException('(ID:' . $order->id . ')未找到订单');
        }
        $orderGoodsChangePriceLogs = self::getOrderGoodsChangePriceLogs($param);

        $order->setOrderGoodsChangePriceLogs($orderGoodsChangePriceLogs);//todo
        $order->setOrderChangePriceLog();
        $order->setDispatchChangePrice($param['dispatch_price']);

        return self::OrderOperate($order);
    }

    /**
     * 订单改价记录
     * {@inheritdoc}
     */
    private static function getOrderGoodsChangePriceLogs($param)
    {
        return collect($param['order_goods'])->map(function ($orderGoodsParams) use ($param) {

            $orderGoodsChangePriceLog = new OrderGoodsChangePriceLog($orderGoodsParams);
            if (!isset($orderGoodsChangePriceLog->belongsToOrderGoods)) {
                throw new AppException('(ID:' . $orderGoodsChangePriceLog->order_goods_id . ')未找到订单商品记录');

            }
            if ($orderGoodsChangePriceLog->belongsToOrderGoods->order_id != $param['order_id']) {
                throw new AppException('(ID:' . $orderGoodsChangePriceLog->order_goods_id . ',' . $param['order_id'] . ')未找到与商品对应的订单');
            }
            //todo 如果不清空,可能会在push时 保存未被更新的订单商品数据,此处需要重新设计
            $orderGoodsChangePriceLog->setRelations([]);
            return $orderGoodsChangePriceLog;
        });
    }

    /**
     * 自动收货
     * {@inheritdoc}
     */
    public static function autoReceive($uniacid)
    {
        \YunShop::app()->uniacid = $uniacid;
        \Setting::$uniqueAccountId = $uniacid;
        $days = (int)\Setting::get('shop.trade.receive');

        if (!$days) {
            return;
        }

        \app\backend\modules\order\models\Order::waitReceive()->where('auto_receipt', 0)->whereNotIn('dispatch_type_id', [DispatchType::SELF_DELIVERY, DispatchType::HOTEL_CHECK_IN, DispatchType::DELIVERY_STATION_SEND, DispatchType::DRIVER_DELIVERY, DispatchType::PACKAGE_DELIVER])->where('send_time', '<', (int)Carbon::now()->addDays(-$days)->timestamp)->normal()->chunk(1000, function ($orders) {
            if (!$orders->isEmpty()) {
                $orders->each(function ($order) {
                    try {
                        OrderService::orderReceive(['order_id' => $order->id]);
                    } catch (\Exception $e) {
                        \Log::error("订单:{$order->id}自动收货失败", $e->getMessage());

                    }
                });
            }
        });


    }

    /**
     * 自动关闭订单
     * {@inheritdoc}
     */
    public static function autoClose($uniacid)
    {
        \YunShop::app()->uniacid = $uniacid;
        \Setting::$uniqueAccountId = $uniacid;
        $days = (int)\Setting::get('shop.trade.close_order_days');
        if (!$days) {
            return;
        }
        $orders = \app\backend\modules\order\models\Order::waitPay()->where('create_time', '<', (int)Carbon::now()->addDays(-\Setting::get('shop.trade.close_order_days'))->timestamp)->normal()->get();
        if (!$orders->isEmpty()) {
            $orders->each(function ($order) {
                //dd($order->send_time);
                try {
                    OrderService::orderClose(['order_id' => $order->id]);
                } catch (\Exception $e) {
                    \Log::error("订单:{$order->id}自动关闭失败", $e->getMessage());
                }
            });
        }
    }

    /**
     * @param $order
     * @throws AppException
     */
    public static function fixVirtualOrder($order)
    {
        \YunShop::app()->uniacid = $order['uniacid'];
        \Setting::$uniqueAccountId = $order['uniacid'];

        if ($order['status'] == 1) {
            OrderService::orderSend(['order_id' => $order['id']]);
        }
        if ($order['status'] == 2) {
            OrderService::orderReceive(['order_id' => $order['id']]);
        }
    }

    /**
     * 聚水潭签名
     * 请求方式: post,业务参数以json的格式放入http-body中,系统参数跟随url
     * sign的组成方式:key,value 为传入的系统参数，按传递顺序)(加密 key中排除sign，method，partnerid,partnerkey)
     * MD5(method +partnerid + (key1+value1+key2+value2+……) +partnerkey)
     * 举例:以调用店铺查询接口为例:
     * sign的源串:shops.queryywv5jGT8ge6Pvlq3FZSPol345asdtoken181ee8952a88f5a57db52587472c3798ts1540370062ywv5jGT8ge6Pvlq3FZSPol2323
     * 请求的链接:https://c.jushuitan.com/api/open/query.aspx?method=shops.query&partnerid=ywv5jGT8ge6Pvlq3FZSPol345asd&token=181ee8952a88f5a57db52587472c3798&ts=1540370062&sign=aebf9d0146764d578d9c86e3b7783204
     * 注:请求的业务参数在http-body中
     */
    public static function generate_signature($action = '')
    {
        $jushuitanSetRs = Setting::get('shop.order');
        $jushuitanSetRs = array_filter($jushuitanSetRs);

        $sign_str = '';
        //系统参数
        $system_params = array(
            'method' => $action,
            'partnerid' => $jushuitanSetRs['jushuitan_partnerid'],
            'ts' => time(),
            'token' => $jushuitanSetRs['jushuitan_token'],
        );

        //普通接口:  加密key中排除sign，method，partnerid,partnerkey
        $no_exists_array = array('method', 'sign', 'partnerid', 'partnerkey');

        $sign_str = $system_params['method'] . $system_params['partnerid'];

        foreach ($system_params as $key => $value) {
            if (in_array($key, $no_exists_array)) {
                continue;
            }
            $sign_str .= $key . strval($value);
        }

        $sign_str .= $jushuitanSetRs['jushuitan_partnerkey'];
        $system_params['sign'] = md5($sign_str);
        \Log::info('----聚水潭签名参数---', $system_params);
        return $system_params;
    }


    /**
     * fixby-ly-jushuitanAPI 2020-07-21 18:11
     * url  聚水潭线上地址
     * data 请求聚水潭参数  参考：https://open.jushuitan.com/document/2137.html
     *action 聚水潭接口名称
     */
    public static function post($type= '', $data, $action)
    {
        $jushuitanSetRs = Setting::get('shop.order');
        $jushuitanSetRs = array_filter($jushuitanSetRs);
        if (!isset($jushuitanSetRs['jushuitan_partnerid']) || !isset($jushuitanSetRs['jushuitan_partnerkey']) ||
            !isset($jushuitanSetRs['jushuitan_token'])
        ){
            return null;
        }

        $url = OrderService::$ju_url;
        //生成带签名的系统参数
        $url_params = OrderService::generate_signature($action);
        $post_data = json_encode($data);

        try {
            $url .= '?' . http_build_query($url_params);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded'
            ));

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                print curl_error($ch);
            }
            curl_close($ch);
            \Log::info('----聚水潭'.$type.'请求信息---'.$url, $data);

            $res = DB::table('yz_order_jushuitan_log')->insert(
                ['order_sn' => $data[0]['so_id'], 'post_params' => $post_data, 'action' => $action, 'type' => $type, 'status' =>1,'res_content' => $result, 'create_time' => date('Y-m-d H:i:s', time())]
            );
            \Log::info('---请求返回结果----'.$res, $result);
            return json_decode($result, true);

        } catch (Exception $e) {

            $res = DB::table('yz_order_jushuitan_log')->insert(
                ['order_sn' => $data[0]['so_id'], 'post_params' => $post_data, 'action' => $action, 'type' => $type, 'status' => -1,'res_content' => $e, 'create_time' => date('Y-m-d H:i:s', time())]
            );
            \Log::info('----聚水潭'.$type.'请求失败---'.$res, $e);
            return null;
        }
    }

    /**
     * 发货，退款消息入库记录
     * fixby-ly-jushuitanAPI 2020-07-21 18:11
     */
    public static function orderMess($order_sn = '', $order = '', $type = '')
    {
        DB::table('yz_order_messages')->insert([
            'uniacid' =>  \YunShop::app()->uniacid,
            'order_sn' => $order_sn,
            'create_time' => time(),
            'order_id' => $order['id'],
            'type' => $type,
            'rele_user_id' => $order['uid'],
        ]);
    }

}