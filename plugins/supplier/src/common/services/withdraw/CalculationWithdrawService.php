<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/25
 * Time: 下午8:02
 */

namespace Yunshop\Supplier\common\services\withdraw;


class CalculationWithdrawService
{
    /**
     * @name 计算此次提现需要的数据
     * @author yangyang
     * @param $order_information
     * @param null $type
     * @return array|mixed
     */
    public static function calculation($order_information, $type = null)
    {
        //此次提现的所有supplier_order的id,用于更改apply_status
        $supplier_order_ids = [];
        //此次提现所有的order_id
        $order_ids          = [];
        //此次可提现金额
        $total_profit       = 0;
        foreach ($order_information as $info) {
            $supplier_order_ids[]   = $info['id'];
            $order_ids[]            = $info['order_id'];
            $total_profit           += $info['supplier_profit'];
        }
        $data = [
            'supplier_order_ids'    => implode(',', $supplier_order_ids),
            'order_ids'             => implode(',', $order_ids),
            'total_profit'          => $total_profit
        ];
        if (isset($type) && $type == 'profit') {
            return $data['total_profit'];
        }
        return $data;
    }
}