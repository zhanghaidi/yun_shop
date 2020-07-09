<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-03-23
 * Time: 16:43
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Supplier\common\models;


use app\common\models\BaseModel;

class InsuranceOrder extends BaseModel
{
    public $table = 'yz_supplier_insurance_order';
    public $timestamps = true;
    protected $guarded = [''];

    public function order()
    {
        return $this->hasOne(\app\backend\modules\order\models\Order::class, 'id', 'order_id');
    }

    public function hasManyInsOrder()
    {
        return $this->hasMany(self::class);
    }

    public function hasOneSupplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }
}