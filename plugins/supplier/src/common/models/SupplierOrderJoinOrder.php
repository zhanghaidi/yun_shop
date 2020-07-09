<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午4:24
 */

namespace Yunshop\Supplier\common\models;


use app\common\exceptions\AppException;
use Yunshop\Supplier\supplier\models\OrderGoods;

class SupplierOrderJoinOrder extends \app\backend\modules\order\models\Order
{
    protected $appends = ['status_name', 'pay_type_name','button_models'];

    protected $search_fields = ['yz_order.id', 'yz_order.order_sn'];

    /**
     * @name 获取供应商订单列表
     * @author yangyang
     * @param $params
     * @return mixed
     */
    public static function getSupplierOrderBuiler($params)
    {
        $builder = parent::builder($params)->select(['yz_supplier_order.id as soid', 'yz_supplier_order.*', 'yz_order.*'])->isSupplierId($params['supplier_id']);

        return $builder;
    }

    public static function getSupplierOrderList($params)
    {
        return self::getSupplierOrderBuiler($params);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('yz_order.status', $status);
    }

    public function scopeRefund($query)
    {
        return $query->where('yz_order.refund_id', '>', 0);
    }

    /**
     * @name 构造器
     * @author yangyang
     * @param null $params
     * @return mixed
     */
    public static function builder($params = null)
    {
        $builder = parent::with(
            [
                'beLongsToSupplier'
            ]
        )->joinOrder($params);
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

    public function beLongsToSupplierOrder()
    {
        return $this->hasOne(SupplierOrder::class, 'order_id', 'id');
    }

    /**
     * @name 复写order里面的方法
     * @author yangyang
     * @param $query
     * @return mixed
     */
    public function scopeIsPlugin($query)
    {
        return $query->where('is_plugin', 1);
    }

    public function hasManyOrderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'id');
    }

    public function scopeUniacid($query)
    {
//        //return $query->where('uniacid', \YunShop::app()->uniacid);
    }

    /**
     * @name 商城订单表关联供应商订单表
     * @author yangyang
     * @param $order_builder
     * @param $params
     * @return mixed
     */
    public function scopeJoinOrder($order_builder, $params){
        $query = $order_builder->join('yz_supplier_order','yz_order.id','=','yz_supplier_order.order_id')->isSupplierId($params)->orders($params)->isPlugin()->where('yz_order.uniacid', \YunShop::app()->uniacid);
        if ($params['supplier']) {
            $query->where('yz_supplier_order.supplier_id', $params['supplier']);
        }
        return $query;
        //return $order_builder->join('yz_supplier_order','yz_order.id','=','yz_supplier_order.order_id')->isSupplierId($params)->orders($params)->isPlugin()->where('yz_order.uniacid', \YunShop::app()->uniacid);
    }

    /**
     * @name 供应商id检索
     * @author yangyang
     * @param $query
     * @param $params
     * @return mixed
     */
    public function scopeIsSupplierId($query, $params)
    {
        if (!empty($params['supplier_id'])) {
            return $query->where('supplier_id', $params['supplier_id']);
        }
        return $query;
    }

    /**
     * @return array
     * @throws AppException
     */
    public function getOperationsSetting()
    {
        return app('OrderManager')->setting('supplier_order_operations')[$this->statusCode] ?: [];
    }
}