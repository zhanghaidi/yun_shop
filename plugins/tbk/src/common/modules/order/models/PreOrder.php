<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/10
 * Time: 12:12
 */
namespace Yunshop\Tbk\common\modules\order\models;

use Yunshop\Tbk\common\modules\orderGoods\models\PreOrderGoods;

class PreOrder extends \app\frontend\modules\order\models\PreOrder
{
    public $tbkOrder;
    protected function getPrice()
    {
        return $this->orderGoods->sum(function(PreOrderGoods $orderGoods){
//            dd($orderGoods->getPrice());
            return $orderGoods->getPrice();
        });
    }
}