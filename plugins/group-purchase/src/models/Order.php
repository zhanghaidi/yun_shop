<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/26
 * Time: 10:12
 */

namespace Yunshop\GroupPurchase\models;


use app\common\models\BaseModel;

class Order extends BaseModel
{
    public $table = 'yz_group_purchase_order';
    public function getOrderAllDate($search)
    {
        $order_Model = self::uniacid();
        if (!empty($search['member'])) {
            $order_Model->whereHas('hasOneMember', function ($query) use ($search) {
                return $query->searchLike($search['member']);
            });
        }

        if (!empty($search['recommend_name'])) {
            $order_Model->whereHas('hasOneRecommend', function ($query) use ($search) {
                return $query->searchLike($search['recommend_name']);
            });
        }
//        dd($search['is_time']);
        if (!empty($search['is_time'])) {
            $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
            $order_Model->whereBetween('created_at', $range);
        }

        if (!empty($search['status'])) {
            if ($search['status'] == 3) {
                $order_Model->where('status', $search['status']);
            } else {
                $order_Model->where('status', '<>',3);
            }
        }
        $order_Model = $order_Model->select('id','order_sn','goods_price','price','created_at','buyer_name','status','recommend_name');
        return $order_Model;
    }

    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'uid');
    }

    public function hasOneRecommend()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'recommend_id');
    }

    public function getPurchaseOrderByOrderId($order_id)
    {
        return self::select()->byOrderId($order_id);
    }

    public function getPurchaseOrderByOrderSn($order_sn)
    {
        return self::where('order_sn',$order_sn)->first();
    }
}