<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-03-23
 * Time: 15:16
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

namespace Yunshop\Supplier\frontend\insOrder\order;



use app\frontend\modules\deduction\OrderDeductionCollection;

class PreOrder extends \app\frontend\modules\order\models\PreOrder
{
    protected $attributes = [
        'plugin_id' => 93,
        'is_virtual' => 1,
    ];

    public function getOrderDeductions()
    {
        return new OrderDeductionCollection();
    }

    public function beforeCreating()
    {
        parent::beforeCreating();
        // 绑定供应商保单订单
        $this->setRelation('insuranceOrders', $this->newCollection());

        $ids = request()->ids;
        $supplier_id = request()->supplier_id;

        foreach ($ids as $key => $id) {
            $preInsuranceOrder = new PreInsuranceOrder(['ins_id' => $id, 'supplier_id' => $supplier_id]);
            $this->insuranceOrders->push($preInsuranceOrder);
        }
    }

    protected function getShopName(){
        return '';
    }
}