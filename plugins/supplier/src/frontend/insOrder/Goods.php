<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-03-24
 * Time: 14:25
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

namespace Yunshop\Supplier\frontend\insOrder;


use app\framework\Database\Eloquent\Builder;

class Goods extends \app\frontend\models\Goods
{
    public function scopePluginId(Builder $query, $pluginId = 0)
    {
        return 93;
    }
}