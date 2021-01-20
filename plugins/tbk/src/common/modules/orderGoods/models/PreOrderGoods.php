<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/10
 * Time: 12:12
 */
namespace Yunshop\Tbk\common\modules\orderGoods\models;

class PreOrderGoods extends \app\frontend\modules\orderGoods\models\PreOrderGoods
{
    public $tbkOrder;
    public function getPrice()
    {
//        dd($this->total);
        return $this->tbkOrder['alipay_total_price'] * $this->total;
        // goods_id
    }

}