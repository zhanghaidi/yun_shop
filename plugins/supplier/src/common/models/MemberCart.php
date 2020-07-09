<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:50 PM
 */

namespace Yunshop\Supplier\common\models;


use app\common\models\Member;

class MemberCart extends \app\common\models\MemberCart
{
    public function getGroupId()
    {
        return $this->goods->plugin_id.'-'.$this->goods->hasOneSupplierGoods->supplier_id;
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class,'goods_id');
    }
}