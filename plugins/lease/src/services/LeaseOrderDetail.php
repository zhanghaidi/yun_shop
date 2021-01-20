<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/10
 */

namespace Yunshop\LeaseToy\services;

use Yunshop\LeaseToy\models\LeaseOrderModel;
use Yunshop\LeaseToy\models\orderGoods\LeaseToyOrderGoodsModel;

class LeaseOrderDetail
{
    
    static public function getLeaseReturn($order) 
    {
       $leaseOrder = LeaseOrderModel::where('order_id', $order->id)->first();
        
       return $leaseOrder->deposit_total;
    }

    static public function detailInfo($order)
    {
        $data = LeaseOrderModel::where('order_id', $order->id)->first();

        if (!$data) return [];

        if (!empty($data->return_status) && $data->return_status == LeaseOrderModel::RETURNED) {
            return ['data'=> $data->toArray(), 'button' => []];
        }

        if (!empty($data->return_status)) {

            if ($data->isConfirm()) {
                $button[] = [
                    'name'=> '待确认',
                    'api' => 'plugin.lease-toy.api.retreat.return.returnDetail',
                    'value' => LeaseOrderModel::STAY_CONFIRM
                ];
            } else {
                $button[] = [
                    'name'=> $data->isApply() ? '审核中': '退还中',
                    'api' => 'plugin.lease-toy.api.retreat.return.index',
                    'value' => LeaseOrderModel::RETURNED
                ];
            }
        } else {
            $button[] = [
                'name'=>'申请退还',
                'api' => 'plugin.lease-toy.api.retreat.return.leaseApply',
                'value' => LeaseOrderModel::PLUGIN_ID
            ];
        }
        return ['data'=> $data->toArray(), 'button' => $button];

    }
    static public function LeaseOrderGoodsDetail($order_goods_id)
    {
        $data = LeaseToyOrderGoodsModel::where('order_goods_id', $order_goods_id)->first();
        
        return $data ? $data->toArray() : [];
    }
}
