<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/17
 * Time: 13:51
 */

namespace Yunshop\Supplier\frontend\operations\supplier;


use app\common\models\DispatchType;

class AcceptReceipt extends SupplierOrderOperation
{
    public function getApi()
    {
        return 'plugin.supplier.frontend.order.accept-receipt';
    }

    public function getName()
    {
        return '确认接单';
    }

    public function getValue()
    {
        return self::SUPPLIER_RECEIPT;
    }

    public function enable()
    {
        if($this->order->currentProcess() && $this->order->dispatch_type_id == DispatchType::DRIVER_DELIVERY) {
            return true;
        }

        return false;
    }
}