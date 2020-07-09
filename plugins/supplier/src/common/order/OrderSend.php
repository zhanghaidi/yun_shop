<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/17
 * Time: 11:27
 */

namespace Yunshop\Supplier\common\order;

use app\common\exceptions\AppException;
use app\common\models\DispatchType;
use app\common\models\Order;
use Illuminate\Support\Facades\Validator;
use app\common\models\order\Express;
use app\common\repositories\ExpressCompany;

use Yunshop\Supplier\common\models\SupplierOrder;
use Yunshop\DeliveryDriver\models\DriverOrderModel;

class OrderSend  extends \app\frontend\modules\order\services\behavior\OrderSend
{
    /**
     * @return bool|void
     * @throws AppException
     */
    protected function updateTable()
    {
        $this->params = request()->input();
        $driver_id = request()->input('driver_id');
        $dispatch_type_id = request()->input('dispatch_type_id');

        if ($dispatch_type_id == DispatchType::DRIVER_DELIVERY && !empty($driver_id)) {
            $supplier_order = SupplierOrder::where('order_id', $this->id)->first();
            if ($supplier_order) {
                $this->setDriverOrder($driver_id, $supplier_order->supplier_id);
            }
            $this->dispatch_type_id = DispatchType::DRIVER_DELIVERY;

        }

        parent::updateTable();
    }

    //基地发货
    protected function setDriverOrder($driver_id, $supplier_id)
    {

        $db_driver_order_model = DriverOrderModel::where('order_id', $this->id)->first();

        !$db_driver_order_model && $db_driver_order_model = new DriverOrderModel();
        $data = [
            'driver_id' => $driver_id,
            'uniacid' => \YunShop::app()->uniacid,
            'order_id' => $this->id,
            'order_sn' => $this->order_sn,
            'order_price' => $this->price,
            'base_id' => $supplier_id,
            'plugin_id' => $this->plugin_id,
            'status' => 0,
        ];

        $db_driver_order_model->fill($data);
        $db_driver_order_model->save();
    }
}