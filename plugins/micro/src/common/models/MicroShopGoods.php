<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/10
 * Time: 下午6:41
 */

namespace Yunshop\Micro\common\models;


use app\common\models\BaseModel;
use app\common\models\Goods;

class MicroShopGoods extends BaseModel
{
    protected $table = 'yz_micro_shop_goods';
    protected $guarded = [''];

    public static function getGoodsList()
    {
        return self::builder()->get();
    }

    public static function getGoodsByGoodsId($goods_id)
    {
        return self::builder()->byGoodsId($goods_id);
    }

    public static function getGoods($shop_id, $goods_id)
    {
        return self::builder()->byGoodsId($goods_id)->byShopId($shop_id)->first();
    }

    public static function getGoodsByMemberId($member_id)
    {
        return self::builder()->byMemberId($member_id)->get();
    }

    public static function builder()
    {
        return self::with([
            'hasOneGoods'
        ])->uniacid();
    }

    public function hasOneGoods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }

    public function scopeByGoodsId($query, $goods_id)
    {
        return $query->where('goods_id', $goods_id);
    }

    public function scopeByShopId($query, $shop_id)
    {
        return $query->where('shop_id', $shop_id);
    }

    public function scopeByMemberId($query, $member_id)
    {
        return $query->where('member_id', $member_id);
    }
}