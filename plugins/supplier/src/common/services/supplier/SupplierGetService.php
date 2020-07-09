<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-04-15
 * Time: 00:06
 */

namespace Yunshop\Supplier\common\services\supplier;


class SupplierGetService
{
    public $member_id;

    public function __construct($member_id)
    {
        $this->member_id = $member_id;
    }

    public function getOrdersByMemberId()
    {
        $result = [];

        $order_models = \Yunshop\Supplier\common\models\SupplierOrderJoinOrder::getSupplierOrderBuiler([])
            ->where('uid', $this->member_id)
            ->status(3);

        $result['count'] = $order_models->count();
        $result['amount'] = $order_models->sum('price');

        return $result;
    }
}