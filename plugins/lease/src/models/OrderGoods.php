<?php

namespace Yunshop\LeaseToy\models;

use Illuminate\Support\Facades\DB;


/**
* 
*/
class OrderGoods extends \app\common\models\OrderGoods
{

    //protected $appends = ['buttons', 'lease_toy_goods'];
    
    public function hasOneLeaseOrderGoods()
    {
        return $this->hasOne('Yunshop\LeaseToy\models\orderGoods\LeaseToyOrderGoodsModel', 'order_goods_id', 'id');

    }

    // public function getLeaseToyGoodsAttribute()
    // {
    //     if ($this->hasOneLeaseOrderGoods) {
    //         return [
    //             'deposit' => $this->hasOneLeaseOrderGoods->deposit,
    //             'lease_price' => $this->hasOneLeaseOrderGoods->lease_price,
    //         ];
    //     }
    // }
}