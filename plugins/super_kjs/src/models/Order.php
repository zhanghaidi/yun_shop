<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/26
 * Time: 17:25
 */

namespace Yunshop\SuperKjs\models;

use app\backend\modules\member\models\Member;
use app\common\models\member\Address;
use app\common\models\Street;
use app\common\models\OrderGoods;
use app\common\events\order\AfterOrderCreatedEvent;
use app\common\events\order\AfterOrderReceivedEvent;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\order\models\PreOrder;
use app\frontend\models\OrderAddress;
use app\frontend\modules\order\services\OrderService;

class Order extends \app\backend\modules\order\models\Order
{
    public $table = 'yz_super_kjs_order';
    const ORDER_PLUGIN_ID = 998;

    /**
     * 完成订单
     */
    public function completeOrder($status)
    {
        $order_status['status'] = $status['status'];
        $purchase_order = self::where('order_sn',$status['order_sn'])->first();
        parent::where('id',$purchase_order->order_id)->update($order_status);
        $order_data = parent::where('id',$purchase_order->order_id)->first();
        event(new AfterOrderReceivedEvent($order_data));
        self::where('order_sn',$status['order_sn'])->update($order_status);

        return true;
    }

    /**
     * 插入总订单
     * @param $data
     * @return bool
     * @throws \app\common\exceptions\AppException
     */
    public function insertOrder($data)
    {
        //插入order数据表
        $order_data = self::getOrderData($data);
        $order = new PreOrder($order_data);
        $order_id = $order->generate();//获取orderID

        //插入order_goods数据表
        $order_goods = new OrderGoods();
        $order_goods->uniacid = $data['uniacid'];
        $order_goods->uid = $data['uid'];
        $order_goods->price = $data['price'];
        $order_goods->goods_price = $data['goods_price'];
        $order_goods->payment_amount = $data['goods_price'];
        $order_goods->goods_id = BasisSetting::getGoodsId();
        $order_goods->order_id = $order_id;
        $order_goods->title = "超级砍价商品";
        $order_goods->save();

        //插入地址数据表
        $data['order_id'] = $order_id;
//        $order->orderAddress = self::getAddress($data);

        //触发下单监听
        event(new AfterOrderCreatedEvent($order));

        //插入超级砍价order数据表
        self::getPurchaseOrderData($data);
        return true;
    }

    /**
     * 获取订单数据
     */
    public function getOrderData($data)
    {
        $order_data = '';
        $order_data->uniacid = $data['uniacid'];
        $order_data->uid = $data['uid'];
        $order_data->order_sn = OrderService::createOrderSN();
        $order_data->price = $data['price'];
        $order_data->goods_price = $data['goods_price'];
        $order_data->order_goods_price = $data['price'];
        $order_data->status = 0;
        $order_data->goods_total = $data['goods_total'];
        $order_data->create_time = time();
        $order_data->plugin_id = self::ORDER_PLUGIN_ID;
        return $order_data;
    }

    /**
     * 获取超级砍价订单数据
     */
    public function getPurchaseOrderData($data)
    {
        $member = Member::getMemberBaseInfoById($data['uid']);//获取购买者信息
        $recommend = Recommender::getMyReferral($data['uid']);//获取推荐者信息
        if (empty($recommend['uid'])) {
            $recommend['uid'] = 0;
        }
        $order_model = [
            'uniacid'          => $data['uniacid'],
            'order_id'         => $data['order_id'],
            'recommend_id'     => $recommend['uid'],
            'recommend_name'   => $recommend['nickname'],
            'uid'              => $data['uid'],
            'buyer_name'       => $member['nickname'],
            'order_sn'         => $data['order_sn'],
            'price'            => $data['price'],
            'goods_price'      => $data['goods_price'],
            'goods_total'      => $data['goods_total'],
            'status'           => $data['status'],
            'order_type'       => $data['is_hexiao'],
            'shipping_address' => $data[''],
            'store_address'    => $data[''],
            'created_at'       => time(),
        ];
        if ($data['is_hexiao'] = 0) {
            $order_model['store_address'] = $data['address'];
        } else {
            $order_model['shipping_address'] = $data['address'];
        }
        self::insert($order_model);
        return $order_model;
    }

    /**
     * 获取地址数据
     */
    public function getAddress($data)
    {
        $addressList = self::addressServiceForIndex($data);
        $order_address = new OrderAddress();
        $order_address->address = implode(' ', [$data['province'], $data['city'], $data['county'],'' ]);
        $order_address->mobile = $data['mobile'];
        $order_address->province_id = $addressList['province_id'];
        $order_address->city_id = $addressList['city_id'];
        $order_address->district_id = $addressList['district_id'];
        $order_address->street_id = 0;
        $order_address->order_id = $data['order_id'];
        $order_address->realname = $data['realname'];
        $order_address->save();
        return $order_address;
    }

    /**
   * 服务列表数据 index() 增加省市区ID值
   */
    private function addressServiceForIndex($data)
    {
        $address = Address::getAllAddress();
        $addressList = [];
        foreach ($address as $key) {
            if ($data['province'] == $key['areaname']) {
                //dd('od');
                $addressList['province_id'] = $key['id'];
            }
            if ($data['city'] == $key['areaname']) {
                $addressList['city_id'] = $key['id'];
            }
            if ($data['county'] == $key['areaname']) {
                $addressList['district_id'] = $key['id'];
            }
        }
        return $addressList;
    }

    protected function getMemberCarts()
    {
        $goodsId = BasisSetting::getGoodsId();
        $goodsParams = [
            'goods_id' => $goodsId,
        ];
        $result = collect();
        $result->push(MemberCartService::newMemberCart($goodsParams));
        return $result;
    }

    public function getOrderByOrderSn($order_sn)
    {
        return self::where('order_sn',$order_sn)->first();
    }

}