<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/31
 * Time: 17:04
 */

namespace Yunshop\JdSupply\services;


use app\common\exceptions\AppException;
use app\common\models\Address;
use app\common\models\Street;
use Yunshop\JdSupply\models\JdGoodsOption;
use Yunshop\JdSupply\services\sdk\JdClient;
use Yunshop\JdSupply\services\sdk\JdNewClient;
use Yunshop\JdSupply\services\sdk\JdNewRequest;
use Yunshop\JdSupply\services\sdk\JdRequest;

class JdOrderValidate
{
    public static function orderValidate($preOrder)
    {
        $set = \Setting::get('plugin.jd_supply');


        $spu = self::getJdRequestSpu($preOrder->jd_order_goods);

        if (empty($spu) ||  count($spu) != count(array_filter($spu))) {
            throw new AppException('有商品已删除或下架');
        }
        $data['spu'] = $spu;
        $data['address'] = self::getMemberAddress($preOrder->orderAddress);


        $request = new JdNewRequest($set['app_secret'], $set['app_key']);
        $request->addBody(json_encode($data));

        $response = JdNewClient::post('/v2/order/beforeCheck', $request);
        $data =  json_decode($response, true);
        \Log::debug('----订单前置校验----',$data);
        if ($data['code'] == 1) {
            return [
                'param' => $data,
            ];
        } else {
            $data['msg'] = !empty($data['msg'])? $data['msg']:'服务器异常';
            throw new AppException($data['msg']);
        }

    }

    public static function orderValidate2($preOrder)
    {
        $set = \Setting::get('plugin.jd_supply');


        //$spu = self::getJdRequestSpu($preOrder['jd_order_goods']);

        $spu = [];

        $jd_goods_option = JdGoodsOption::getJdGoods($preOrder['jd_order_goods']['goods_id'], $preOrder['jd_order_goods']['goods_option_id'])->first();
        if ($jd_goods_option) {
            $spu['sku'] = $jd_goods_option->jd_option_id;
            $spu['number'] = $preOrder['jd_order_goods']['total'];
        }

        if (empty($spu)) {
            return 0;
        }
        $data['spu'][] = $spu;
        //$data['address'] = self::getMemberAddress($preOrder['orderAddress']);
        $data['address']['consignee'] = $preOrder['orderAddress']['username']?:'匿名';
        $data['address']['phone'] = $preOrder['orderAddress']['mobile'];
        $data['address']['province'] = $preOrder['orderAddress']['province'];
        $data['address']['city'] = $preOrder['orderAddress']['city'];
        $data['address']['area'] = $preOrder['orderAddress']['district'];
        $data['address']['street'] = $preOrder['orderAddress']['street']?:'其他';
        $data['address']['description'] =  last(explode(' ', $preOrder['orderAddress']['address']));

        $request = new JdNewRequest($set['app_secret'], $set['app_key']);
        $request->addBody(json_encode($data));

        $response = JdNewClient::post('/v2/order/beforeCheck', $request);

        $data =  json_decode($response, true);
        \Log::debug('----订单前置校验----',$data);
        if ($data['code'] == 1) {
            return 1;
        } else {
            return 0;
        }

    }


    public static function getJdRequestSpu($order_goods)
    {

        $data = $order_goods->map(function ($item) {
            $jd_goods_option = JdGoodsOption::getJdGoods($item->goods_id, $item->goods_option_id)->first();
            if ($jd_goods_option) {
                $goods['sku'] = $jd_goods_option->jd_option_id;
                $goods['number'] = $item->total;
                return $goods;
            }
            return false;
        })->toArray();
        return $data;
    }

    /**
     * 获取请求第三方订单验证地址数据
     * @param $order_address
     * @return mixed
     */
    public static function getMemberAddress($order_address)
    {
        $order_data['consignee'] = $order_address->realname?:'匿名';
        $order_data['phone'] = $order_address->mobile;
        $order_data['province'] = Address::where('id',$order_address->province_id)->value('areaname');
        $order_data['city'] = Address::where('id',$order_address->city_id)->value('areaname');
        $order_data['area'] = Address::where('id',$order_address->district_id)->value('areaname');
        $order_data['street'] = Street::where('id', $order_address->street_id)->value('areaname')?:'其他';
        $order_data['description'] =  trim(last(explode(' ', $order_address->address)));

        return $order_data;
    }

}