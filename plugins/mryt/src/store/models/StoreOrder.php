<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/1
 * Time: 下午5:19
 */

namespace Yunshop\Mryt\store\models;

use app\common\models\BaseModel;
use app\common\models\order\OrderDeduction;

/**
 * Class StoreOrder
 * @package Yunshop\StoreCashier\common\models
 * @property Store hasOneStore
 * @property int pay_type_id
 */
class StoreOrder extends BaseModel
{
    public $table = 'yz_plugin_store_order';
    public $timestamps = true;
    protected $guarded = [''];
    const PAGE_SIZE = 10;
    const INCOME_TYPE_NAME = '门店提现';
    const HAS_WITHDRAW = 1;

    public function storeOrder(){
        return $this->hasOne(StoreOrder::class);
    }

    public static function getOrderList($params)
    {
        return self::builder()->search($params);
    }

    public static function getOrderByOrderId($order_id)
    {
        return self::builder()->byOrderId($order_id);
    }

    public static function builder()
    {
        return self::with([
            'hasOneStore',
            'hasOneOrder',
            'hasManyOrderDeduction'
        ]);
    }

    public function scopeByOrderId($query, $order_id)
    {
        return $query->where('order_id', $order_id);
    }

    public function hasOneStore()
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }

    public function hasOneOrder()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function order()
    {
        return $this->hasOne(\app\backend\modules\order\models\Order::class, 'id', 'order_id');
    }

    public function hasManyOrderDeduction()
    {
        return $this->hasMany(OrderDeduction::class, 'order_id', 'order_id');
    }

    public function scopeSearch($query, $params)
    {

        $store_params = $params['store'];
        // 核销员ID搜索
        if ($store_params['clerk_id']) {
            $query->where('verification_clerk_id', $store_params['clerk_id']);
        }
        $query->whereHas('hasOneStore', function ($store) use ($store_params) {

            // todo 门店id搜索
            if ($store_params['store_id']) {
                $store->where('id', $store_params['store_id']);
            }

            // todo 门店省市区搜索
            if ($store_params['province_id']) {
                $store->where('province_id', $store_params['province_id']);
            }
            if ($store_params['city_id']) {
                $store->where('city_id', $store_params['city_id']);
            }
            if ($store_params['district_id']) {
                $store->where('district_id', $store_params['district_id']);
            }
            if ($store_params['street_id']) {
                $store->where('street_id', $store_params['street_id']);
            }
            // todo 门店名称搜索
            if ($store_params['store_name']) {
                $store->where('store_name', 'like',
                    '%' . $store_params['store_name'] . '%');
            }
            // todo 门店分类搜索
            if ($store_params['category']) {
                $store->where('category_id', $store_params['category']);
            }
            // todo 门店微信搜索
            if ($store_params['member']) {
                $store->whereHas('hasOneMember',
                    function ($member) use ($store_params) {
                        $member->select('uid', 'nickname', 'realname', 'mobile',
                            'avatar')
                            ->where('realname', 'like',
                                '%' . $store_params['member'] . '%')
                            ->orWhere('mobile', 'like',
                                '%' . $store_params['member'] . '%')
                            ->orWhere('nickname', 'like',
                                '%' . $store_params['member'] . '%');
                    });
            }
        });

        $query->whereHas('hasOneOrder', function ($order) use ($params) {
            if ($params['order']['order_sn']) {
                $order->where('order_sn', 'like',
                    "%{$params['order']['order_sn']}%");
            }
            if ($params['order']['member']) {
                $order->whereHas('belongsToMember',
                    function ($member) use ($params) {
                        $member->select('uid', 'nickname', 'realname', 'mobile',
                            'avatar')
                            ->where('realname', 'like',
                                '%' . $params['order']['member'] . '%')
                            ->orWhere('mobile', 'like',
                                '%' . $params['order']['member'] . '%')
                            ->orWhere('nickname', 'like',
                                '%' . $params['order']['member'] . '%');
                    });
            }

            // todo 支付方式
            if ($params['order']['pay_type']) {
                $order->where('pay_type_id', $params['order']['pay_type'])->where('status', '>', '0');
            }
            // todo 支付方式
            if ($params['order']['status'] != '') {
                $order->where('status', $params['order']['status']);
            }
            // todo 操作时间范围
            if ($params['order']['field']) {
                $range = [strtotime($params['time_range']['start']), strtotime($params['time_range']['end'])];
                $order->whereBetween($params['order']['field'], $range);
            }

        });

        return $query;
    }
}