<?php
namespace Yunshop\GoodsPackage\common\models;

class Goods extends \app\common\models\Goods
{
    /**
     * @param $keyword
     * @return mixed
     */
    public static function getGoodsByName($keyword)
    {
        return static::uniacid()->select('id', 'title', 'thumb', 'market_price', 'price', 'real_sales', 'sku','plugin_id','stock')
            ->where('title', 'like', '%' . $keyword . '%')
            ->where('status', 1)
            //->where('is_plugin', 0)
            ->whereNotIn('plugin_id', [20, 31,32, 60])//屏蔽门店、码上点餐、第三方插件接口的虚拟商品
            ->get();
    }

    // 通过id获取商品价格
    public static function getGoodsPriceByGoodsId($goods_id){
        $goods = self::uniacid()->select('price')->where('id','=',$goods_id)->first();
        return $goods->price;
    }
    // 通过id获取商品
    public static function getGoodsByGoodsId($goods_id){
        return self::uniacid()->select('id','title','price')->where('id','=',$goods_id)->first();
    }

    // 通过多个id获取多个商品
    public static function getGoodsListByGoodsIds($GoodsIds){
        return self::uniacid()->select('id','title','price')->whereIn('id',$GoodsIds)->get();
    }

    // 通过多个id获取多个商品的总价
    public static function getGoodsListPriceSumByGoodsIds($GoodsIds){
        return self::uniacid()->select('price')->whereIn('id',$GoodsIds)->sum('price');
    }
}
