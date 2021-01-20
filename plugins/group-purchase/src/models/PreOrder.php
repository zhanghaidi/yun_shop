<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/29
 * Time: 15:05
 */

namespace Yunshop\GroupPurchase\models;

use app\frontend\models\OrderAddress;
use Yunshop\StoreCashier\common\models\Store;
use app\common\models\member\Address;

class PreOrder extends \app\frontend\modules\order\models\PreOrder
{
    protected $attributes = [
        'is_virtual' => 1,
    ];

    public function beforeCreating()
    {
        parent::beforeCreating();

        $orderAddress = $this->getOrderAddress();
        if(!$orderAddress->validator()->fails()){
            // 存在地址时
            $this->setRelation('orderAddress', $this->getOrderAddress());
        }

    }

    /**
     * 获取订单配送地址模型
     * @return OrderAddress
     */
    private function getOrderAddress()
    {
        $data = request()->input();
        $address = Address::getAllAddress();
        $addressList = self::addressServiceForIndex($data,$address);
        $order_address = new OrderAddress();
        $order_address->address = $data['address'];
        $order_address->mobile = request()->input('mobile');
        $order_address->province_id = $addressList['province_id'];
        $order_address->city_id = $addressList['city_id'];
        $order_address->district_id = $addressList['district_id'];
        $order_address->street_id = '';
        $order_address->realname = request()->input('realname','');

        return $order_address;
    }


    /**
     * 服务列表数据 index() 增加省市区ID值
     */
    private function addressServiceForIndex($data,$address)
    {
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
}