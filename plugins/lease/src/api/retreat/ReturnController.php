<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/10
 */

namespace Yunshop\LeaseToy\api\retreat;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use Yunshop\LeaseToy\models\LeaseOrderModel;
use Yunshop\LeaseToy\models\retreat\OrderReturnExpress;
use Yunshop\LeaseToy\models\retreat\OrderReturnAddress;
use app\common\models\refund\RefundApply;
use app\frontend\modules\refund\services\RefundService;
use Yunshop\LeaseToy\models\Order;
use Illuminate\Support\Facades\DB;
use Request;

class ReturnController extends ApiController
{

    public function index(Request $request)
    {
        $this->validate([
            'order_id' => 'required|integer',
        ]);

        $leaseOrder = LeaseOrderModel::whereOrderId($request->query('order_id'))->first();
        if (!isset($leaseOrder)) {
            throw new AppException('租赁订单不存在');
        }

        return $this->successJson('成功', $leaseOrder->toArray());

    }

    public function returnDetail(Request $request)
    {
        $this->validate([
            'order_id' => 'required|integer',
        ]);

        $leaseOrder = LeaseOrderModel::whereOrderId($request->query('order_id'))->ReturnBuilder()->first();
        if (!isset($leaseOrder)) {
            throw new AppException('租赁订单不存在');
        }

        return $this->successJson('成功', $leaseOrder->toArray());

    }

    //退还申请
    public function leaseApply(Request $request)
    {
        $this->validate([
            'order_id' => 'required|integer'
        ]);

        $order = Order::find($request->query('order_id'));
        if (!isset($order)) {
            throw new AppException('订单不存在');
        }
        if ($order->uid != \YunShop::app()->getMemberId()) {
            throw new AppException('无效申请,该订单属于其他用户');
        }

        if (RefundApply::where('order_id', $request->input('order_id'))->where('status', '>=', RefundApply::WAIT_CHECK)->count()) {
            throw new AppException('申请已提交,处理中');
        }

        $leaseOrder = LeaseOrderModel::whereOrderId($order->id)->first();
        if (!isset($leaseOrder)) {
            throw new AppException('租赁订单不存在');
        }

        $data = [
            'reason' => '租赁归还',
            'order_id' => $request->query('order_id'),
            'refund_type' => 1,
            'content' => '',
            'images' => [],
            'refund_sn' => RefundService::createOrderRN(),
        ];
        $refundApply = new RefundApply($data);
        $refundApply->create_time = time();
        $refundApply->price = $leaseOrder->deposit_total;

        DB::beginTransaction();
        try{  
            $refundApply->save();
            $leaseOrder->return_status = LeaseOrderModel::RETURN_APPLY;
            $order->refund_id = $refundApply->id;
            $leaseOrder->save();
            $order->save();
            DB::commit();
            return $this->successJson('成功', $leaseOrder->toArray());
        }catch (\Exception $e) {
            DB::rollBack();
            
            $this->errorJson('no','租赁订单退还状态改变失败');
            //throw new AppException('租赁订单退还状态改变失败');
        } 
    }


    public function submitReturn(Request $request)
    {
        $this->validate([
            'order_id' => 'required|filled|integer',
            'lease_id' => 'required|filled|integer',
            'express_code' => 'required|string',
            'express_company_name' => 'required|string',
            'express_sn' => 'required|filled|string',
            'mobile' => 'required|integer',
            'address' => 'required|string'
        ]);
        $LeaseOrder = LeaseOrderModel::find($request->query('lease_id'));

        if (!$LeaseOrder || $LeaseOrder->order_id != $request->query('order_id')) {
            throw new AppException('未获取到该租赁订单或已删除');
        }

        if ($LeaseOrder->return_status != LeaseOrderModel::APPLY_ADOPT) {
            throw new AppException('退还申请记录不存在');
        }


        $express = Request::only(['order_id','lease_id', 'express_code', 'express_sn', 'express_company_name']);
        
        $returnExpress = new OrderReturnExpress($express);

        if (!$returnExpress->save()) {
            throw new AppException('租赁订单快递信息保存失败');
        }

        $address = Request::only(['order_id','lease_id', 'realname', 'address', 'mobile']);

        $returnAddress = new OrderReturnAddress($address);

        if (!$returnAddress->save()) {
            throw new AppException('租赁订单退还地址保存失败');
        }

        $LeaseOrder->return_status = LeaseOrderModel::STAY_CONFIRM;

        $set = $LeaseOrder->save();

        if (!$set) {
            throw new AppException('租赁订单退还状态改变失败');
        }

        $this->successJson('returninfo');
    }
}