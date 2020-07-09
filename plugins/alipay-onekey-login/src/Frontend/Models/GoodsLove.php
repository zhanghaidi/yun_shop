<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/14
 * Time: 下午2:09
 */

namespace Yunshop\Love\Frontend\Models;


use app\frontend\modules\deduction\GoodsDeduction;
use Yunshop\Love\Common\Models\LoveCoin;

/**
 * 爱心值商品抵扣设置
 * Class GoodsLove
 * @package Yunshop\Love\Frontend\Models
 * @property LoveCoin loveCoin
 */
class GoodsLove extends \Yunshop\Love\Common\Models\GoodsLove
{
    protected $hidden = ['goods'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setRelation('loveCoin', new LoveCoin());

    }

    public function setGoods($goods){
        if(!$this->isEnable()){
            return false;
        }

        $this->goods = $goods;

        $goodsLove = $this->whereGoodsId($goods->id)->first();
        if (!isset($goodsLove)) {
            return;
        }

        $this->fill($goodsLove->getAttributes());
        $this->loveCoin->setMoney($goods->price * $this->getGoodsDeductionProportion());

        $goods->setRelation('goodsLove', $this);
    }
    public function isEnable(){

        //\Setting::get('love.goods_detail_show_love') &&
        if(\Setting::get('love.goods_detail_show_love') ==1 ){
            return true;
        }
        return false;
    }


    public function getGoodsDeductionProportion()
    {
        if (!$this->deduction) {
            // 商品未开启了抵扣
            return $this->loveCoin;
        }
        // 优先使用商品独立比例,其次使用全局比例
        if ($this->deduction_proportion > 0) {
            $proportion = $this->getGoodsMaxDeductionProportion();
        } else {
            $proportion = \Setting::get('love.deduction_proportion') / 100;
        }
        return $proportion;

    }


    private function getGoodsMaxDeductionProportion()
    {
        if (!$this->deduction) {
            return 0;
        }

        $result = $this->deduction_proportion / 100;

        return $result;
    }
}