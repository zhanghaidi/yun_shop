<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/17
 * Time: 14:13
 */

namespace Yunshop\Supplier\frontend\operations\supplier;


use app\common\models\DispatchType;

class DriverSend extends SupplierOrderOperation
{
    public function getApi()
    {
        return 'plugin.supplier.frontend.order.driver-send';
    }

    public function getName()
    {
        return '确认发货';
    }

    public function getValue()
    {
        return self::SUPPLIER_DRIVER_SEND;
    }

    public function enable()
    {
        return true;
    }
}