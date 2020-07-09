<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/26
 * Time: 14:37
 */

namespace Yunshop\JdSupply\services;


use app\common\exceptions\AppException;
use app\common\models\Address;
use app\common\models\Street;
use app\frontend\modules\order\services\OrderService;
use Yunshop\JdSupply\common\JdSupplyOrderStatus;
use Yunshop\JdSupply\models\JdGoodsOption;
use Yunshop\JdSupply\models\JdPushMessage;
use Yunshop\JdSupply\models\Order;
use Yunshop\JdSupply\services\sdk\JdClient;
use Yunshop\JdSupply\services\sdk\JdNewClient;
use Yunshop\JdSupply\services\sdk\JdNewRequest;
use Yunshop\JdSupply\services\sdk\JdRequest;

class JdOrderService
{
    //订单取消消息
    public static function cancel($data)
    {
        $order = Order::where('order_sn', $data['orderSn'])->first();
        if (is_null($order)) {
            return false;
        }
        $order->refund();
        JdPushMessage::create(['uniacid'=>\YunShop::app()->uniacid,'type'=>'order.cancel','order_id'=>$order->id]);
        return true;
    }

    //配送单生成成功消息
    public static function send($data)
    {
        $order = Order::where('order_sn', $data['orderSn'])->with(['hasOneJdSupplyOrder'])->with(['hasManyJdSupplyOrderGoods'])->first();
        if (is_null($order)) {
            return false;
        }
        $ret = JdSupplyOrderStatus::unlockOrder($order);
        //自动发货
        if ($ret) {
            OrderService::orderSend(['order_id'=>$order->id,'confirmsend'=>'yes']);
            JdPushMessage::create(['uniacid'=>\YunShop::app()->uniacid,'type'=>'order.delivery','order_id'=>$order->id]);
            return true;
        }
        return false;
    }

    //订单等待确认收货消息
    public static function waitReceipt()
    {

    }

    //订单配送退货消息
    public static function orderReturn()
    {

    }


    /**
     * 查询订单物流信息
     * @param $order_sn string 订单号
     * @param $jd_goods_option_id int 第三方商品规格id
     * @return mixed
     */
    public static function jdExpressInfo($order_sn, $jd_goods_option_id)
    {

        $set = \Setting::get('plugin.jd_supply');

        $request = new JdNewRequest($set['app_secret'], $set['app_key']);

        $request->addParam('orderSn', $order_sn);

        $request->addParam('sku', $jd_goods_option_id);
        $response = JdNewClient::get('/v2/logistic', $request);
        $data =  json_decode($response, true);

        return $data;
    }


    /**
     * 获取物流公司
     * @return mixed
     *
     */
    public static function jdExpressCompany()
    {

        $set = \Setting::get('plugin.jd_supply');

        $request = new JdNewRequest($set['app_secret'], $set['app_key']);


        $response = JdNewClient::get('/v2/logistic/firms', $request);

        $data =  json_decode($response, true);

//        if (!isset($data['code']) || $data['code'] != 1) {
//
//        }
        return $data;
    }
    /**
     * 自动提交订单
     */
    public static function autoCommitOrder($order_id)
    {
        $order = Order::isPlugin()->pluginId()->with(['hasManyOrderGoods', 'address','hasOneJdSupplyOrder','hasManyJdSupplyOrderGoods'])->find($order_id);

        $data = CreateOrderService::createOrder($order);

        if (!isset($data['code']) || $data['code'] != 1) {
            \Log::debug('----聚合供应链订单自动提交失败-----',$order_id);
            \Log::debug('----聚合供应链requestId-----',$data);
            return;
        }
        JdSupplyOrderStatus::waitSend($order->id);
        \Log::debug('----聚合供应链订单自动提交成功-----',$order_id);
        \Log::debug('----聚合供应链requestId-----',$data);
    }


}