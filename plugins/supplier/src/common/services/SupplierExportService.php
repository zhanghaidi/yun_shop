<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/15
 * Time: 下午8:02
 */

namespace Yunshop\Supplier\common\services;

use \app\backend\modules\order\services\ExportService;

class SupplierExportService extends ExportService
{
    public function setColumns()
    {
        $columns = [
            [
                "title" => "用户备注",
                "field" => "note",
                "width" => 24
            ],
            [
                "title" => "供应商",
                "field" => "supplier_username",
                "width" => 24
            ],
            [
                "title" => "订单成本",
                "field" => "order_profit",
                "width" => 12
            ],
            [
                "title" => "省",
                "field" => 'province',
                "width" => 12
            ],
            [
                "title" => "市",
                "field" => 'city',
                "width" => 12
            ],
            [
                "title" => "区",
                "field" => 'district',
                "width" => 12
            ],
            [
                "title" => "抵扣金额",
                "field" => 'deduction',
                "width" => 16
            ],
            [
                "title" => "优惠券优惠",
                "field" => 'coupon',
                "width" => 16
            ],
            [
                "title" => "全场满减优惠",
                "field" => 'enoughReduce',
                "width" => 16
            ],
            [
                "title" => "单品满减优惠",
                "field" => 'singleEnoughReduce',
                "width" => 16
            ],
        ];

        $this->columns = array_merge($this->columns, $columns);
    }

    protected function setOrder($order)
    {
        //$order_goods_information = unserialize($order['order_goods_information']);
        $order['order_profit'] = $order['supplier_profit'];
        $order['supplier_username'] = $order['be_longs_to_supplier']['username'];

        $order += $this->orderExportAddress($order);

        $order['deduction'] = $this->getExportDiscount($order, 'deduction');
        $order['coupon'] = $this->getExportDiscount($order, 'coupon');
        $order['enoughReduce'] = $this->getExportDiscount($order, 'enoughReduce');
        $order['singleEnoughReduce'] = $this->getExportDiscount($order, 'singleEnoughReduce');


        return $order;
    }
    protected function orderExportAddress($order)
    {
        $address = explode(' ', $order['address']);
        $order['province'] = !empty($address[0])?$address[0]:'';
        $order['city'] = !empty($address[1])?$address[1]:'';
        $order['district'] = !empty($address[2])?$address[2]:'';

        return $order;
    }

    protected function getExportDiscount($order, $key)
    {
        $export_discount = [
            'deduction' => 0,    //抵扣金额
            'coupon'    => 0,    //优惠券优惠
            'enoughReduce' => 0,  //全场满减优惠
            'singleEnoughReduce' => 0,    //单品满减优惠
        ];

        foreach ($order['discounts'] as $discount) {

            if ($discount['discount_code'] == $key) {
                $export_discount[$key] = $discount['amount'];
            }
        }

        if (!$export_discount['deduction']) {

            foreach ($order['deductions'] as $k => $v) {
                
                $export_discount['deduction'] += $v['amount'];
            }
        }
        
        return $export_discount[$key];
    }

    /*private function setOrderProfit($order_goods_information)
    {
        $order_profit = 0;
        foreach ($order_goods_information as $info) {
            $order_profit += $info['cost_price'];
        }
        return $order_profit;
    }*/
}