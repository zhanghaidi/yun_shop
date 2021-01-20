<?php


namespace Yunshop\LeaseToy\models\orderGoods;

use Yunshop\LeaseToy\models\orderGoods\NormalOrderGoodsPrice;

class NormalOrderGoodsOptionPrice extends NormalOrderGoodsPrice
{
    protected function goods(){
        return $this->orderGoods->goodsOption;
    }
    protected function aGoodsPrice(){
        return $this->goods()->product_price;
    }
}