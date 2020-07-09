<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午2:30
 */

namespace Yunshop\Supplier\supplier\controllers\order;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\Order;
use app\frontend\modules\order\services\OrderService;

class OperationController extends BaseController
{
    private $param;
    private $order;

    public function preAction()
    {
        parent::preAction();
        $this->param = \YunShop::request()->get();
        if (!isset($this->param['order_id'])) {
            return $this->message('order_id不能为空!','', 'error');

        }
        $this->order = Order::find($this->param['order_id']);
        if (!isset($this->order)) {
            return $this->message('未找到该订单!','', 'error');

        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function pay()
    {
        OrderService::orderPay($this->param);

        return $this->successJson();

    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function cancelPay()
    {
        OrderService::orderCancelPay($this->param);

        return $this->message('成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.order.supplier-order'));
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function send()
    {
        OrderService::orderSend($this->param);
        return $this->message('成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.order.supplier-order'));
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function cancelSend()
    {
        OrderService::orderCancelSend($this->param);
        return $this->message('成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.order.supplier-order'));
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function Receive()
    {
        OrderService::orderReceive($this->param);
        return $this->message('成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.order.supplier-order'));
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function Delete()
    {
        OrderService::orderDelete($this->param);
        return $this->message('成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.order.supplier-order'));
    }

    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function Close()
    {
        OrderService::orderClose($this->param);

        return $this->message('成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.order.supplier-order'));
    }
}