<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 11:01
 */

namespace Yunshop\Supplier\frontend\operations\supplier;


class CancelSend extends SupplierOrderOperation
{
    public function getApi()
    {
        return 'plugin.supplier.frontend.order.cancel-send';
    }

    public function getName()
    {
        return '取消发货';
    }

    public function getValue()
    {
        return self::SUPPLIER_CANCEL_SEND;
    }

    public function enable()
    {
        return true;
    }
}