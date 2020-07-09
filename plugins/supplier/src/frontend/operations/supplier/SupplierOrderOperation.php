<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 10:52
 */

namespace Yunshop\Supplier\frontend\operations\supplier;

use app\frontend\models\Order;
use app\frontend\modules\order\operations\OrderOperation;

abstract class SupplierOrderOperation extends OrderOperation
{
    protected $order;
    const SUPPLIER_SEND = 23;// 供应商确认发货
    const SUPPLIER_DRIVER_SEND = 'supplier_driver_send'; //供应商确认发货(司机配送)
    const SUPPLIER_CANCEL_SEND = 24;// 供应商取消发货
    const SUPPLIER_CLOSE = 26;// 供应商关闭订单
    public function __construct($order)
    {
        parent::__construct($order);
    }
}