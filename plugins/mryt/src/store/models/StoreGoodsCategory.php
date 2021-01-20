<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/6
 * Time: 上午11:54
 */

namespace Yunshop\Mryt\store\models;


use app\common\models\BaseModel;

class StoreGoodsCategory extends BaseModel
{
    public $table = 'yz_store_goods_category';
    public $timestamps = true;
    protected $guarded = [''];

    public function scopeByStoreId($query, $store_id)
    {
        return $query->where('store_id', $store_id);
    }

    public function scopeByParentId($query, $parent_id)
    {
        return $query->where('parent_id', $parent_id);
    }
}