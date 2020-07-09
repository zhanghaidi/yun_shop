<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/16
 * Time: 下午7:09
 */

namespace Yunshop\Love\Frontend\Modules\Deduction;

use app\frontend\modules\deduction\GoodsDeduction;
use Yunshop\Love\Frontend\Models\GoodsLove;

class LoveGoodsDeduction extends GoodsDeduction
{
    public function getCode()
    {
        return 'love';
    }

    public function deductible($goods)
    {
        $goodsLove = GoodsLove::where('goods_id', $goods->id)->first();

        return isset($goodsLove) && $goodsLove->deduction;
    }
}