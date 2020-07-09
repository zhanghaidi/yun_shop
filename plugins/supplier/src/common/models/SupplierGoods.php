<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午4:23
 */

namespace Yunshop\Supplier\common\models;



use app\common\models\BaseModel;

class SupplierGoods extends BaseModel
{
    public $table = 'yz_supplier_goods';
    protected $guarded = [''];

    /**
     * @name 获取供应商商品列表
     * @author yangyang
     * @param $params
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function getSupplierGoodsList($params)
    {
        $list = SupplierGoods::builder($params);

        return $list;
    }

    /**
     * @name 通过商品ID获取供应商商品信息
     * @author
     * @param $goods_id
     * @param int $type
     * @return mixed
     */
    public static function getSupplierGoodsById($goods_id, $type = 0)
    {
        $goods_model = SupplierGoods::builder()->where('goods_id', $goods_id)->first();
        $set = \Setting::get('plugin.supplier');
        if ($goods_model) {
            $supplier = $goods_model->hasOneSupplier;
            $supplier->logo = replace_yunshop(tomedia($supplier->logo));
            $supplier->is_open = $set['is_open_index'];
            return $supplier;
        }
        return $goods_model;
    }

    public static function getGoodsIdsBySid($sid)
    {
        $goods_models = self::select()->where('supplier_id', $sid)->get();
        if ($goods_models->isEmpty()) {
            return collect([]);
        }
        return $goods_models->pluck('goods_id');
    }

    /**
     * @name 构造器
     * @author yangyang
     * @param null $params
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    private static function builder($params = null)
    {
        $builder = SupplierGoods::with(
            [
                'hasOneGoods' => self::goods_builder($params),
                'hasOneSupplier' => self::supplier_builder($params),
                'hasOneMember'
            ]
        );
        return $builder;
    }

    /**
     * @name 商品匿名函数
     * @author yangyang
     * @param $params
     * @return \Closure
     */
    private static function goods_builder($params)
    {
        return function ($query) use ($params) {
            return $query->search($params);
        };
    }

    /**
     * @name 供应商匿名函数
     * @author yangyang
     * @param $params
     * @return \Closure
     */
    private static function supplier_builder($params)
    {
        return function ($query) use ($params) {
            return $query->search($params);
        };
    }

    /**
     * @name 关联商城商品表
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneGoods()
    {
        return $this->hasOne('\app\common\models\Goods', 'id', 'goods_id');
    }

    /**
     * @name 关联供应商表
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneSupplier()
    {
        return $this->hasOne(\Yunshop\Supplier\admin\models\Supplier::class, 'id', 'supplier_id');
    }

    /**
     * @name 关联会员表
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMember()
    {
        return $this->hasOne(\app\backend\modules\member\models\Member::class, 'uid', 'member_id');
    }

    /**
     * @name 关联区域管理
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneRegion()
    {
        return $this->hasOne(\Yunshop\RegionMgt\models\RegionSupplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * @name 公众号id搜索
     * @author yangyang
     * @param $query
     * @return mixed
     */
    public function scopeUniacid($query)
    {
        return $query->where('uniacid', \YunShop::app()->uniacid);
    }
}