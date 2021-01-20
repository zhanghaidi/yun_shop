<?php

namespace Yunshop\LeaseToy\models\orderGoods;


class NormalOrderGoodsPrice extends \app\frontend\modules\orderGoods\price\option\NormalOrderGoodsPrice
{

    private $price;
    /**
     * 成交价
     * @return mixed
     */
  /*  public function getPrice()
    {
        // 商品销售价 - 等级优惠金额 - 单品满减优惠金额
        return max($this->leaseDay() - ($this->getVipDiscountAmount() * $this->leaseGoodsTerm()['days']) - $this->getFullReductionAmount(), 0);
        return max($this->getGoodsPrice() - $this->getVipDiscountAmount() - $this->getFullReductionAmount(), 0);
    }*/

    /**
     * 成交价
     * @return mixed
     */
    public function getPrice()
    {
//        if (isset($this->price)) {
//            return $this->price;
//        }
        // 商品销售价 - 等级优惠金额 - 单品满减优惠金额
        $this->price =  $this->leaseDay() - ($this->getVipDiscountAmount() * $this->leaseGoodsTerm()['days']);
        $this->price = max($this->price, 0);
        return $this->price;
    }

    //优惠后的商品价格
    public function discountGold()
    {
        $term = $this->leaseGoodsTerm()['term_discount'];
        //优惠后的商品价格
        $price = $this->aGoodsPrice() * (1 - ($term / 100)) * $this->rentFree();
        return $price;
    }

    //商品租赁总价
    public function leaseDay() 
    {
        //租期价格 * 租期天数
        return $this->discountGold() * $this->leaseGoodsTerm()['days'];

    }

    //获取租赁商品
    public function leaseGoods()
    {
        return $this->orderGoods->hasOneLeaseGoods;
    }

    //获取商品押金
    public function getGoodsDeposit()
    {
        $goods_deposit = $this->orderGoods->hasOneLeaseGoods->goods_deposit;
        //押金 * 数量
        return $goods_deposit * $this->depositFree();
    }

    //免租金
    public function rentFree()
    {
        $rent_free = $this->goodsRightsFreeRent()['rent_free'];
        if ($rent_free) {
            return max($this->orderGoods->total - $rent_free['free_num'], 0);
        }

        return $this->orderGoods->total;
    }
    
    //免押金
    public function depositFree()
    {
        $deposit_free = $this->goodsRightsFreeDeposit()['deposit_free'];

        if ($deposit_free) {
            return max($this->orderGoods->total - $deposit_free['free_num'], 0);
        }

        return $this->orderGoods->total;
    }

      /**
     * 商品的会员等级折扣金额
     * @return mixed
     */
    // public function getVipDiscountAmount()
    // {
    //     return $this->goods()->getVipDiscountAmount() * $this->orderGoods->total;
    // }

    //获取租期优惠
    public function leaseGoodsTerm()
    {
        if (isset($this->orderGoods->days)) {
            return $this->orderGoods->days;
        }
        return $this->orderGoods->leaseToydays();
    }
    //获取使用等级权益免租金
    public function goodsRightsFreeRent()
    {
        if (isset($this->orderGoods->lease_goods)) {
            return $this->orderGoods->lease_goods;
        }
        return $this->orderGoods->leaseToyGoods();
    }
    //获取使用等级权益免押金
    public function goodsRightsFreeDeposit()
    {
        if (isset($this->orderGoods->lease_goods)) {
            return $this->orderGoods->lease_goods;
        }
        return $this->orderGoods->leaseToyGoods();
    }
}