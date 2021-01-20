<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/11
 * Time: 14:15
 */

namespace Yunshop\HelpUserBuying\store\models;

use app\common\models\GoodsCategory;
use Yunshop\StoreCashier\store\models\Goods as BaseGoods;

class StoreGoods extends BaseGoods
{
    public function hasOneCategory()
    {
        return $this->hasOne(GoodsCategory::class, 'goods_id', 'id');
    }
}