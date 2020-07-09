<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-03-23
 * Time: 16:07
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Supplier\frontend\insOrder\order;


use Yunshop\Supplier\common\models\InsuranceGoods;

class PreOrderGoods extends \app\frontend\modules\orderGoods\models\PreOrderGoods
{
    public function insuranceGoods()
    {
        return $this->hasOne(InsuranceGoods::class, 'goods_id', 'goods_id');
    }
}