<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/22
 */

namespace Yunshop\LeaseToy\admin;

use app\common\components\BaseController;
use Request;
use Yunshop\LeaseToy\models\LeaseOrderModel;
use app\common\exceptions\AdminException;
use Illuminate\Support\Facades\DB;
use app\backend\modules\refund\models\RefundApply;
use Yunshop\LeaseToy\models\Order;
use Yunshop\LeaseToy\services\PayMentService;
use app\common\models\Member;
use Yunshop\LeaseToy\services\MessageService;
use Yunshop\LeaseToy\models\RightsLogModel;
use Yunshop\LeaseToy\models\AreaLeaseReturnLogModel;

class LeaseReturnController extends BaseController
{
    
    protected $leaseReturn;

    protected $refundApply;

    public function refuse(Request $request)
    {

        $this->validate([
            'lease_id' => 'required',
            'refund_id' => 'required',
        ]);
        $this->leaseReturn = LeaseOrderModel::find($request->input('lease_id'));
        $this->refundApply = RefundApply::find($request->input('refund_id'));
        if (!isset($this->leaseReturn)) {
            throw new AdminException('退还记录不存在');
        }
        if (!isset($this->refundApply)) {
            throw new AdminException('退还记录不存在');
        }


        DB::beginTransaction();  
        try{  

            $this->leaseReturn->return_status = 0;
            $this->refundApply->reject(\Request::only(['reject_reason']));
            $this->refundApply->order->refund_id = 0;
            $this->refundApply->order->save();
            $this->leaseReturn->save();
            DB::commit();
            return $this->message('操作成功', '');
        }catch (\Exception $e) {  
            DB::rollBack();
            return $this->message('操作失败', '');
        }  
    }


    public function pass($lease = 0, $refund = 0)
    {

        $this->leaseReturn = LeaseOrderModel::find($lease);
        $this->refundApply = RefundApply::find($refund);
        if (!isset($this->leaseReturn)) {
            throw new AdminException('退还记录不存在');
        }
        if (!isset($this->refundApply)) {
            throw new AdminException('退还记录不存在');
        }


        DB::beginTransaction();
        try{  

            $this->leaseReturn->return_status = LeaseOrderModel::APPLY_ADOPT;
            $this->leaseReturn->save();
            $this->refundApply->pass();
            DB::commit();
            return true;
            //return $this->message('操作成功', '');
        }catch (\Exception $e) {  
            DB::rollBack();
            return false;
            //return $this->message('操作失败', '');
        } 
    }

    public function waitSendBack(Request $request)
    {
        $this->validate([
            'lease_id' => 'required',
            'refund_id' => 'required',
        ]);
        $lease_id = $request->input('lease_id');
        $refund_id = $request->input('refund_id');

        $bool = $this->pass($lease_id, $refund_id);

        if ($bool) {
            return $this->message('操作成功', '');
        }

        return $this->message('操作失败', '');

    }
    public function directPass(Request $request)
    {
        $this->validate([
            'lease_id' => 'required',
            'refund_id' => 'required',
        ]);

        $lease_id = $request->input('lease_id');
        $refund_id = $request->input('refund_id');
        $order_id = $request->input('order_id');

        $bool = $this->pass($lease_id, $refund_id);

        if (!$bool) {
            return $this->message('操作失败', '');
        }

        $leaseReturn = LeaseOrderModel::find($lease_id);

        $leaseReturn->return_status = LeaseOrderModel::STAY_CONFIRM;

        $set = $leaseReturn->save();

        if (!$set) {
            return $this->message('租赁订单退还状态改变失败', '');
        }

        return $this->message('操作成功', '');
        
    }

    public function examineReturn(Request $request)
    {
        $this->validate([
            'order_id' => 'required',
            'as_id' => 'required',
            'return_deposit' => 'numeric|min:0',
            'be_overdue' => 'numeric|min:0',
            'be_damaged' => 'numeric|min:0',
        ]);
        $order = Order::find($request->input('order_id'));
        $leaseReturn = $order->hasOneLeaseToyOrder;

        if (!isset($order) || !isset($leaseReturn)) {
            throw new AdminException('租赁订单不存在');
        }
        
        $refundApply = RefundApply::find($order->refund_id);
        if (!($refundApply)) {
            throw new AdminException('退还记录不存在');
        }

        $AreaStore = \Yunshop\AreaStore\common\models\AreaStore::find( $request->input('as_id'));

        if (!isset($AreaStore)) {
            throw new AdminException('该分站不存在');
        }


        $be_overdue = $request->input('be_overdue');
        $be_damaged = $request->input('be_damaged');
        $return_deposit = max(($leaseReturn->deposit_total - $be_overdue - $be_damaged), 0);
        $data = Request::only(['order_id', 'be_overdue', 'be_damaged', 'explain', 'as_id']);
        $data['return_deposit'] = $return_deposit;
        $data['uniacid'] = \Yunshop::app()->uniacid;
        $data['as_name'] = $AreaStore->hasOneWeiQing->username;
        $area_lease_log = AreaLeaseReturnLogModel::getModel($request->input('order_id'));

        $area_lease_log->fill($data);
        $validator = $area_lease_log->validator($area_lease_log->getAttributes());
        if ($validator->fails()) {//检测失败
            $this->error($validator->messages());
        }
        $bool = $area_lease_log->save();
        if ($bool) {
            return $this->message('操作成功', '');
        }

        return $this->message('操作失败', '');

    }

    public function leaseRefund(Request $request)
    {
        $this->validate([
            'order_id' => 'required',
            'return_pay_type_id' => 'required',
            'return_deposit' => 'numeric|min:0',
            'be_overdue' => 'numeric|min:0',
            'be_damaged' => 'numeric|min:0',
        ]);
        $order = Order::find($request->input('order_id'));
        $leaseReturn = $order->hasOneLeaseToyOrder;

        if (!isset($order) || !isset($leaseReturn)) {
            throw new AdminException('租赁订单不存在');
        }

        $refundApply = RefundApply::find($order->refund_id);
        if (!($refundApply)) {
            throw new AdminException('退还记录不存在');
        }

        $be_overdue = $request->input('be_overdue');
        $be_damaged = $request->input('be_damaged');
        $return_deposit = max(($leaseReturn->deposit_total -  $be_overdue - $be_damaged), 0);
        $data = Request::only(['order_id', 'be_overdue', 'be_damaged', 'explain', 'return_pay_type_id']);
        $data['return_deposit'] =  $return_deposit;
        $leaseReturn->fill($data);
        $leaseReturn->return_status = LeaseOrderModel::RETURNED;
        $leaseReturn->return_time = time();

        $validator = $leaseReturn->validator($leaseReturn->getAttributes());
        if ($validator->fails()) {//检测失败
            $this->error($validator->messages());
        }

        DB::beginTransaction();
        try{  
            $leaseReturn->save();
            (new PayMentService())->pay($leaseReturn->id);
            $refundApply->status = RefundApply::COMPLETE;
            $refundApply->save();
            // $order->close();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            if ($e) {
                return $this->message($e->getMessage(), '');
            }
            return $this->message('操作失败', '');
        } 
        //返还权益
        RightsLogModel::uniacid()->where('order_id', $order->id)->delete();
        //归还通知
        $this->notice($leaseReturn, $order);
        
        return $this->message('操作成功', '');
    }
    public function notice($lease, $order)
    {
        $member = Member::getMemberByUid($lease->member_id)->with('hasOneFans')->first();

        $orderGoods = $order->hasManyOrderGoods;

        $data = [
            'lease' => $lease,
            'orderGoods' => $orderGoods
        ];

        MessageService::leaseReturn($member, $data);

    }
}