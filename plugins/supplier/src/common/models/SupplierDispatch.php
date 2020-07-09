<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/29
 * Time: 下午8:04
 */

namespace Yunshop\Supplier\common\models;


use app\backend\modules\goods\models\Dispatch;
use app\common\models\BaseModel;

class SupplierDispatch extends BaseModel
{
    public $table = 'yz_supplier_dispatch';
    protected $guarded = [''];

    /**
     * @name 获取配送模板列表
     * @author yangyang
     * @param null $supplier_id
     * @return mixed
     */
    public static function getList($supplier_id = null)
    {
        $list = SupplierDispatch::with(
            [
                'hasOneDispatch' => self::dispatchBuilder()
            ]
        )->supplierId($supplier_id);
        return $list;
    }

    /**
     * @name 关联商城配送模板表
     * @author yangyang
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneDispatch()
    {
        return $this->hasOne(Dispatch::class, 'id', 'dispatch_id');
    }

    /**
     * @name 构造器
     * @author yangyang
     * @return \Closure
     */
    public static function dispatchBuilder()
    {
        return function ($query) {
            return $query->uniacid();
        };
    }

    /**
     * @name 供应商id搜索
     * @author yangyang
     * @param $query
     * @param $supplier_id
     * @return mixed
     */
    public function scopeSupplierId($query, $supplier_id)
    {
        if (!isset($supplier_id)) {
            return $query;
        }
        return $query->where('supplier_id', $supplier_id);
    }
}