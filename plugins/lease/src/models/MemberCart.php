<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/2
 * Time: 下午4:12
 */

namespace Yunshop\LeaseToy\models;

use Illuminate\Database\Eloquent\Builder;
use Yunshop\LeaseToy\models\LeaseToyGoodsModel;

class MemberCart extends \app\frontend\models\MemberCart
{
    protected $table = 'yz_plugin_lease_member_cart';
    
    public function hasOneLeaseGoods()
    {
        return $this->hasOne('Yunshop\LeaseToy\models\LeaseToyGoodsModel', 'goods_id', 'goods_id');
    }

    /**
     * @param Builder $query
     * @param int $pluginId
     * @return mixed|Builder
     */
    public function scopePluginId(Builder $query, $pluginId = 0)
    {
        return $query;
    }
}