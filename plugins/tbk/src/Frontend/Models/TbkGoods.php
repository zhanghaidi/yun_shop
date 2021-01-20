<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/14
 * Time: 下午2:09
 */

namespace Yunshop\Tbk\Frontend\Models;

use Yunshop\Tbk\common\models\TbkCoupon;

/**
 * 爱心值商品抵扣设置
 * Class GoodsLove
 * @package Yunshop\Love\Frontend\Models
 * @property LoveCoin loveCoin
 */
class TbkGoods extends TbkCoupon
{
    //protected $hidden = ['goods'];
    //private

    /**
     * 设置全局作用域 拼接 uniacid()
     */
    public static function boot()
    {
        parent::boot();
    }


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

    }

    public function setGoods($goods){

        //$this->goods = $goods;

        $tbkCoupon = $this->whereGoodsId($goods->id)->first();

        if (!isset($tbkCoupon)) {
            return;
        }

        $this->fill($tbkCoupon->getAttributes());

        $goods->setRelation('tbkCoupon', $this);
        //print_r($goods);
        //exit;
    }

    /**
     * 商品ID 检索
     * @param $query
     * @param $goodsId
     * @return mixed
     */
    public function scopeOfGoodsId($query, $goodsId)
    {
        return $query->where('goods_id', $goodsId);
    }

}