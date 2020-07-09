<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 10:59
 */

namespace Yunshop\Supplier\frontend\operations\supplier;


use app\common\models\DispatchType;

class Send extends SupplierOrderOperation
{
    public function getApi()
    {
        return 'plugin.supplier.frontend.order.send';
    }

    public function getName()
    {
        return '确认发货';
    }

    public function getValue()
    {
        return self::SUPPLIER_SEND;
    }

    public function enable()
    {
        return true;
    }
}