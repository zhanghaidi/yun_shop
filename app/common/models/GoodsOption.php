<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 19:35
 */

namespace app\common\models;

use app\common\exceptions\AppException;
use app\common\facades\SiteSetting;

/**
 * Class GoodsOption
 * @package app\common\models
 * @property int uniacid
 * @property int goods_id
 * @property int product_price
 * @property int market_price
 * @property int title
 * @property int stock
 */
class GoodsOption extends \app\common\models\BaseModel
{
    public $table = 'yz_goods_option';

    public $guarded = [];
    public $timestamps = false;

    /**
     * 库存是否充足
     * @param $num
     * @throws AppException
     * @author shenyang
     */
    public function reduceStock($num)
    {
        //拍下立减
        if ($this->goods->reduce_stock_method != 2) {
            if (!$this->stockEnough($num)) {
                throw new AppException('(ID:' . $this->id . ')下单失败,商品规格:' . $this->title . ' 库存不足');
            }
            \Cache::decrement("goods_option.{$this->id}.stock", $num);
        }
    }

    public function reduceDBStock($num)
    {
        if ($this->goods->reduce_stock_method != 2) {
            if (!$this->stockEnough($num)) {
                throw new AppException('(ID:' . $this->id . ')下单失败,商品规格:' . $this->title . ' 库存不足');
            }
            //拍下立减
            if ($this->goods->reduce_stock_method != 2) {
                $this->decrement("stock", $num);
            }
        }
    }
    /**
     * 库存是否充足
     * @param $num
     * @return bool
     * @author shenyang
     */
    public function stockEnough($num)
    {
        if ($this->goods->reduce_stock_method != 2) {
            if ($this->stock - $num < 0) {
                return false;
            }
        }
        return true;
    }

    public function goods()
    {
        return $this->belongsTo(app('GoodsManager')->make('Goods'), 'goods_id', 'id');
    }

    public function save()
    {
        $result = parent::save();
        if (SiteSetting::get('base.stock_cache') && $result) {
            \Cache::forget("goods_option.{$this->id}.stock");
        }
        return $result;
    }

    public function getStockAttribute()
    {
        return $this->getCache('stock');
    }

    public function getCache($key)
    {
        $cache = \Cache::get("goods_option.{$this->id}.{$key}");
        if (!isset($cache)) {
            $cache = $this->attributes[$key];
            \Cache::put("goods_option.{$this->id}.{$key}", $cache, 30);
        }
        return $cache;
    }
}