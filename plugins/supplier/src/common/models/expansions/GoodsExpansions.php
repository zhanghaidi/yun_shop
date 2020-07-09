<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/19
 * Time: 上午9:51
 */
namespace Yunshop\Supplier\common\models\expansions;

use app\common\models\ModelExpansion;
use Yunshop\Supplier\common\models\SupplierGoods;

class GoodsExpansions extends ModelExpansion
{
    public function supplierGoods($model){
        return $model->hasOne(SupplierGoods::class,'goods_id');
    }
}