<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/24
 * Time: 下午6:48
 */

namespace Yunshop\Supplier\common\models;



use app\framework\Database\Eloquent\Builder;

class SupplierGoodsJoinGoods extends \app\backend\modules\goods\models\Goods
{
    protected $search_fields = ['yz_goods.title'];

    /**
     * @name 获取供应商商品列表
     * @author yangyang
     * @param $params
     * @return mixed
     */
    public static function getSupplierGoodsList($params)
    {
        $list = SupplierGoodsJoinGoods::builder($params)->select(['yz_supplier_goods.id as sg_id','yz_supplier_goods.*','yz_goods.*'])->isSupplierId($params);
        return $list;
    }

    /**
     * @name 构造器
     * @author yangyang
     * @param null $params
     * @return mixed
     */
    public static function builder($params = null)
    {
        $builder = SupplierGoodsJoinGoods::with(
            [
                'beLongsToSupplier' => self::supplier_builder($params)
            ]
        )->joinGoods($params);
        return $builder;
    }

    /**
     * @name 关联供应商表
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function beLongsToSupplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    /**
     * @name 供应商匿名函数
     * @author yangyang
     * @param null $params
     * @return mixed
     */
    public static function supplier_builder($params)
    {
//        unset($params['supplier']);
        return function ($query) use ($params) {
            return $query->search($params);
        };
    }

    /**
     * @name 商城商品表关联供应商商品表
     * @author yangyang
     * @param $order_builder
     * @param $params
     * @return mixed
     */
    public function scopeJoinGoods($order_builder, $params){
        return $order_builder->join('yz_supplier_goods','yz_goods.id','=','yz_supplier_goods.goods_id')->isSupplierId($params)->search($params)->uniacid();
    }

    /**
     * @name 复写goods里面的方法
     * @author yangyang
     * @param $query
     * @return mixed
     */
    public function scopeIsPlugin($query)
    {
        return $query->where('yz_goods.is_plugin', 1);
    }

    /**
     * @name 供应商检索
     * @author yangyang
     * @param $query
     * @param $params
     * @return mixed
     */
    public function scopeIsSupplierId($query, $params)
    {
        if (!empty($params['supplier_id'])) {
             $query->where('supplier_id', $params['supplier_id']);
             return $query;
        }
    }

    public function scopeUniacid($query)
    {
        return $query->where('yz_goods.uniacid', \YunShop::app()->uniacid);
    }
    public function scopePluginId(Builder $query, $pluginId = null)
    {
        if(!isset($pluginId)){
            $pluginId = \Yunshop\Supplier\common\models\Supplier::PLUGIN_ID;
        }
        return parent::scopePluginId($query, $pluginId);
    }
}