<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/19
 */

namespace Yunshop\LeaseToy\api\order;

use app\common\components\ApiController;
use Yunshop\LeaseToy\models\LeaseOrderModel;

class LeaseApplyController extends ApiController
{
    
    static public function getLeaseReturn($order) 
    {
       $leaseOrder = LeaseOrderModel::where('order_id', $order->id)->first();
        
        if ($leaseOrder) {
            return $leaseOrder->deposit_total;
        }
    }
}
