<?php
namespace Yunshop\GoodsPackage\frontend\package\models;
use app\common\models\GoodsSpecItem;

class Goods extends \app\common\models\Goods
{
    public static function getGoods($id)
    {
        $goodsModel = static::uniacid()->with(['hasManyParams' => function (BaseModel $query) {
            return $query->select('goods_id', 'title', 'value');
        }])->with(['hasManySpecs' => function (BaseModel $query) {
            return $query->select('id', 'goods_id', 'title', 'description');
        }])->with(['hasManyOptions' => function (BaseModel $query) {
            return $query->select('id', 'goods_id', 'title', 'thumb', 'product_price', 'market_price', 'stock', 'specs', 'weight');
        }])->find($id);
        //商品规格图片处理
        if ($goodsModel->hasManyOptions && $goodsModel->hasManyOptions->toArray()) {
            foreach ($goodsModel->hasManyOptions as &$item) {
                $item->thumb = replace_yunshop(yz_tomedia($item->thumb));
            }
        }
        if (!$goodsModel) {
            return [];
        }
        if ($goodsModel->has_option) {
            $goodsModel->min_price = $goodsModel->hasManyOptions->min("product_price");
            $goodsModel->max_price = $goodsModel->hasManyOptions->max("product_price");
            $goodsModel->stock = $goodsModel->hasManyOptions->sum('stock');
        }
        $goodsModel->setHidden(
            [
                'deleted_at',
                'created_at',
                'updated_at',
                'cost_price',
                'real_sales',
                'is_deleted',
                'reduce_stock_method',

                'uniacid',
                'brand_id',
                'display_order',
                'description',
                'content',
                'goods_sn',
                'product_sn',
                'is_new',
                'is_hot',
                'is_discount',
                'is_recommand',
                'is_comment',
                'comment_num',
                'is_plugin',
                'plugin_id',
                'no_refund',
                'virtual_sales',
            ]);
        if ($goodsModel->thumb) {
            $goodsModel->thumb = yz_tomedia($goodsModel->thumb);
        }
        if ($goodsModel->thumb_url) {
            $thumb_url = unserialize($goodsModel->thumb_url);
            foreach ($thumb_url as &$item) {
                $item = yz_tomedia($item);
            }
            $goodsModel->thumb_url = $thumb_url;
        }
        foreach ($goodsModel->hasManySpecs as &$spec) {
            $spec['specitem'] = GoodsSpecItem::select('id', 'title', 'specid', 'thumb')->where('specid', $spec['id'])->get();
            foreach ($spec['specitem'] as &$specitem) {
                $specitem['thumb'] = yz_tomedia($specitem['thumb']);
            }
        }
        return $goodsModel;

    }


    protected $appends = [];
    // 通过id获取商品价格
    public static function getGoodsPriceByGoodsId($goods_id){
        $goods = self::uniacid()->select('price')->where('id','=',$goods_id)->first();
        return $goods->price;
    }

    // 通过id获取商品
    public static function getGoodsByGoodsId($goods_id){
        return self::uniacid()->select('id','title','price')->where('id','=',$goods_id)->first();
    }

    // 通过id获取商品信息，包括规格等
    public static function getGoodsInfoByGoodsId($goods_id){
        $goods = self::uniacid()->select('id','title','price')->where('id','=',$goods_id)->first();
        return $goods->toArray();
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
