<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-03-23
 * Time: 16:47
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


use Yunshop\Supplier\common\models\InsuranceOrder;

class PreInsuranceOrder extends InsuranceOrder
{
    public $order;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function setOrder($preOrder)
    {
//        $this->order = $preOrder;
//        $preOrder->insuranceOrders->push($this);
//        $this->supplier_id = request()->supplier_id;
    }

    public function save(array $options = [])
    {
        return parent::save($options);
    }
}