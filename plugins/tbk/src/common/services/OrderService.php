<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/9
 * Time: 15:03
 */

namespace Yunshop\Tbk\common\services;




use Yunshop\Tbk\common\models\TbkOrder;

class OrderService
{
    private $err_array = [];

    public function importOrder($filePath) {
        \Excel::load($filePath, function($reader) {
            //$data = $reader;
            //$d = $this->getRow($reader);
            $this->handleOrders($this->getRow($reader));

            //dd($d);
        });
    }


    private function handleOrders($values)
    {
        foreach ($values as $rownum => $col) {
            $orderData = [
                'uniacid' => \YunShop::app()->uniacid,
                'create_time' => trim($col[0]),
                'item_title' => trim($col[2]),
                'num_iid' => trim($col[3]),
                'seller_shop_title' => trim($col[5]),
                'item_num' => trim($col[6]),
                'price' => trim($col[7]),
                'tk_status' => trim($col[8]),
                'order_type' => trim($col[9]),
                'income_rate' => trim($col[10]),
                'pub_share_pre_fee' => trim($col[13]),
                'pay_price' => trim($col[14]),    //实际支付金额（结算金额）
                'commission_rate' => trim($col[11]),
                'alipay_total_price' => trim($col[12]),
                'commission' => trim($col[15]),
                'earning_time' => trim($col[16]),
                'total_commission_rate' => trim($col[17]),
                'total_commission_fee' => trim($col[18]),
                'tech_fee' => trim($col[19]),
                'subsidy_rate' => trim($col[20]),
                'subsidy_fee' => trim($col[21]),
                'subsidy_type' => trim($col[22]),
                'order_sn' => trim($col[25]),
                'auction_category' => trim($col[26]),
                'site_id' => trim($col[27]),
                'adzone_id' => trim($col[29]),
                'adzone_name' => trim($col[30]),
            ];

            //dd($orderData);
            if (empty($orderData['order_sn'])) {
                continue;
            }

            $order = TbkOrder::select('id', 'order_sn', 'yz_order_sn', 'yz_order_status')->where('order_sn', $orderData['order_sn'])->first();
            if ($order) {
                if ($order->yz_order_sn && $order->yz_order_status == 1) {
                    // todo, 订单流程结束
                    continue;
                }

                if ($order->tk_status != $orderData['tk_status']) {
                    // todo, 淘宝客订单状态更新，需同步更新此表状态 update order status
                    $order->update(["tk_status" => $order->tk_status]);
                }
                continue;
            }

            $TbkOrderModel = new TbkOrder();

            $TbkOrderModel->fill($orderData);
            $TbkOrderModel->save();
            //return $TbkOrderModel;
        }
        //$this->setErrorMsg();
    }

    /**
     * @name 获取表格内容
     * @author
     * @return array
     */
    private function getRow($sheet)
    {
        $values = [];
        //$sheet = $this->reader->getActiveSheet();
        $sheet = $sheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnCount = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $row = 2;
        while ($row <= $highestRow)
        {
            $rowValue = array();
            $col = 0;
            while ($col < $highestColumnCount)
            {
                $rowValue[] = (string)$sheet->getCellByColumnAndRow($col, $row)->getValue();
                ++$col;
            }
            $values[] = $rowValue;
            ++$row;
        }
        return $values;
    }
}