<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-03-20
 * Time: 17:59
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


use app\framework\Database\Eloquent\Builder;
use Yunshop\Supplier\common\models\Insurance;

class Goods extends \app\frontend\models\Goods
{
    public function scopePluginId(Builder $query, $pluginId = 0)
    {
        return parent::scopePluginId($query, 93);
    }

    public $appends = [];

    public function insurancePrice()
    {
        $ids = request()->input('ids');
        $price = Insurance::whereIn('id', $ids)->sum('premium');

        return $price;
    }

    public function getVipDiscountAmount($price)
    {
        return 0;
    }

    public function getPriceAttribute()
    {
        return $this->insurancePrice();
    }
    public function getCostPriceAttribute()
    {
        return $this->insurancePrice();
    }
    public function getMarketPriceAttribute()
    {
        return $this->insurancePrice();
    }
}