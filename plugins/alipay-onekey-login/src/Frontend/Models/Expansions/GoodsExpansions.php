<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/19
 * Time: 上午9:51
 */
namespace Yunshop\Love\Frontend\Models\Expansions;

use app\common\models\ModelExpansion;
use Yunshop\Love\Common\Models\GoodsLove;

class GoodsExpansions extends ModelExpansion
{
    public function goodsLove($model){
        return $model->hasOne(GoodsLove::class,'goods_id');
    }
}