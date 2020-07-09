<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 10:46
 */

namespace Yunshop\Supplier\frontend\operations\supplier;


class Close extends SupplierOrderOperation
{
    public function getApi()
    {
        return 'plugin.supplier.frontend.order.close-order';
    }

    public function getName()
    {
        return '关闭订单';
    }

    public function getValue()
    {
        return self::SUPPLIER_CLOSE;
    }

    public function enable()
    {
        return true;
    }
}