<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/2
 * Time: 下午2:33
 */

namespace Yunshop\Mryt\store\models;


use app\common\models\BaseModel;
use Yunshop\Mryt\store\models\Store;

class StoreGoods extends BaseModel
{
    public $table = 'yz_store_goods';
    public $timestamps = true;
    protected $guarded = [''];

    public function scopeByStoreId($query, $store_id)
    {
        return $query->where('store_id', $store_id);
    }

    public static function getModelByGoodsIdAndByStoreId($goodsId, $storeId)
    {
        return self::select()->byGoodsId($goodsId)->byStoreId($storeId)->first();
    }

    public static function getGoodsIdsByStoreId($store_id)
    {
        return self::select()->byStoreId($store_id)->pluck('goods_id');
    }

    public function scopeByGoodsId($query, $goodsId)
    {
        return $query->where('goods_id', $goodsId);
    }

    public function scopeByGoodsIds($query, $goods_ids)
    {
        return $query->whereIn('goods_id', $goods_ids);
    }

    public function scopeHasOneStore($query, $city_id)
    {
        return $query->whereHas('hasOneStore', function($store)use($city_id){
            $store->where('city_id', $city_id);
        });
    }

    public function hasOneStore()
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }

    public function hasOneGoods()
    {
        return $this->hasOne(\app\common\models\Goods::class, 'id', 'goods_id');
    }
}