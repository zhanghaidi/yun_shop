<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/17
 * Time: 17:11
 */

namespace Yunshop\JdSupply\services;

use app\common\exceptions\ShopException;
use app\common\models\Address;
use app\common\models\Street;
use Yunshop\JdSupply\models\JdSupplyError;
use Yunshop\JdSupply\models\Order;
use Yunshop\JdSupply\services\sdk\JdClient;
use Yunshop\JdSupply\services\sdk\JdNewClient;
use Yunshop\JdSupply\services\sdk\JdNewRequest;
use Yunshop\JdSupply\services\sdk\JdRequest;

class CreateOrderService
{
    public $order_data;


    /**
     * 提交订单到第三方
     * @param Order $order
     * @return mixed
     * @throws ShopException
     */
    public static function createOrder($order)
    {

        $spu = self::getJdGoodsId($order->hasManyJdSupplyOrderGoods);

        if (empty($spu)) {
            throw new ShopException('第三方商品不存在');
        }

        $order_data['spu'] = $spu;
        $order_data['address'] = self::getMemberAddress($order->address);
        $order_data['orderSn'] = $order->order_sn;


        return self::confirmOrder($order_data);

    }
    /**
     * 补单
     */
    public static function reOrder($order)
    {

        $order_data['orderSn'] = $order->order_sn;
        $set = \Setting::get('plugin.jd_supply');

        $request = new JdNewRequest($set['app_secret'], $set['app_key']);

        $request->addBody(json_encode($order_data));

        $response = JdNewClient::patch('/v2/order', $request);

        $data =  json_decode($response, true);
        if (!isset($data['code']) || $data['code'] != 1) {
            JdSupplyError::jdError($order_data['thirdOrder'], $request->getAllParam(), $data, 'reOrder');
        }

        return $data;

    }


    /**
     * 请求第三方订单确认接口
     * @param $order_data
     * @return mixed
     */
    public static function confirmOrder($order_data)
    {
        $set = \Setting::get('plugin.jd_supply');

        $request = new JdNewRequest($set['app_secret'], $set['app_key']);

        $request->addBody(json_encode($order_data));

        $response = JdNewClient::post('/v2/order', $request);

        $data =  json_decode($response, true);
        if (!isset($data['code']) || $data['code'] != 1) {
            JdSupplyError::jdError($order_data['thirdOrder'], $request->getAllParam(), $data, 'create_order');
        }

        return $data;
    }

    /**
     * 获取请求第三方订单数据
     * @param $order_address
     * @return mixed
     */
    public static function getMemberAddress($order_address)
    {
        $order_data['consignee'] = $order_address->realname;
        $order_data['phone'] = $order_address->mobile;
        $order_data['province'] = Address::where('id',$order_address->province_id)->value('areaname');
        $order_data['city'] = Address::where('id',$order_address->city_id)->value('areaname');
        $order_data['area'] = Address::where('id',$order_address->district_id)->value('areaname');
        $order_data['street'] = Street::where('id', $order_address->street_id)->value('areaname')?:'其他';
        $order_data['description'] =  last(explode(' ', $order_address->address));

        return $order_data;
    }


    /**
     * 获取请求第三方下单接口的 商品参数
     * @param $order_goods
     * @return string
     */
    public static function getJdGoodsId($order_goods)
    {
        if ($order_goods->isEmpty()) {
            return '';
        }
        $data = $order_goods->map(function ($item) {
            $goods['sku'] = $item->jd_option_id;
            $goods['number'] = $item->total;
            return $goods;
        })->toArray();
        return $data;
    }
}