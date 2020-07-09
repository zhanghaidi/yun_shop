<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/22
 * Time: 17:30
 */

namespace Yunshop\Supplier\frontend;


use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\models\Order;
use Illuminate\Support\Facades\DB;
use app\frontend\modules\order\services\OrderService;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\models\SupplierOrder;
use Yunshop\Supplier\common\models\SupplierOrderJoinOrder;
use app\common\repositories\ExpressCompany;


class OrderController extends ApiController
{

    /**
     * 全部訂單
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $member_id = \YunShop::app()->getMemberId();
        $supplier_model = Supplier::getSupplierByMemberId($member_id);

        if ($supplier_model->member_id != $member_id) {
            $memberSupplier = Supplier::uniacid()->where('member_id',$member_id)->first();
            if (!empty($memberSupplier)) {
                return $this->errorJson('没有权限,跳转会员中心!', ['url'=> yzAppFullUrl('member')]);
            } else {
                return $this->errorJson('没有权限,跳转供应商申请!', ['url'=> yzAppFullUrl('member/supplier')]);
            }
        }

        /*$uniacid = \YunShop::app()->uniacid;
        $list = SupplierOrderJoinOrder::IsPlugin()->where('uniacid', $uniacid)->with('hasManyOrderGoods')->whereHas('beLongsToSupplierOrder', function ($query) use ($supplier_model) {
            $query->where('supplier_id', $supplier_model->id);
        })->orderBy('yz_order.id', 'desc')->paginate(15)->toArray();*/

        // 测试版 正常
        /*$list = SupplierOrderJoinOrder::select('yz_order.*')
            ->with('hasManyOrderGoods')
            ->join('yz_supplier_order',function ($join) use ($supplier_model){
                $join->on('yz_order.id', '=', 'yz_supplier_order.order_id')
                    ->where('yz_supplier_order.supplier_id', $supplier_model->id);
            })
            ->orderBy('yz_order.id', 'desc')
            ->paginate(15)
            ->toArray();*/

        $orderIds = SupplierOrder::select()
            ->where('supplier_id', $supplier_model->id)
            ->orderBy('order_id', 'desc')
            ->pluck('order_id');

        // $uniacid = \YunShop::app()->uniacid;
        $list = SupplierOrderJoinOrder::with(['hasManyOrderGoods'])
            // ->where('uniacid', $uniacid)
            ->whereIn('id', $orderIds)
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->toArray();

        if (empty($list)) {
            return $this->errorJson('没有订单!', 0);
        }
        return $this->successJson('ok', $list);
    }

    /**
     * 待付款
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitPay()
    {
        $member_id = \YunShop::app()->getMemberId();
        $supplier_model = Supplier::getSupplierByMemberId($member_id);
        $orderIds = SupplierOrder::select()
            ->where('supplier_id', $supplier_model->id)
            ->orderBy('order_id', 'desc')
            ->pluck('order_id');

        // $uniacid = \YunShop::app()->uniacid;
        $list = SupplierOrderJoinOrder::with(['hasManyOrderGoods'])
            ->where('status', 0)
            ->whereIn('id', $orderIds)
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->toArray();
        if (empty($list)) {
            return $this->errorJson('没有订单!', 0);
        }
        return $this->successJson('ok', $list);
    }

    /**
     * 待發貨
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitSend()
    {
        $member_id = \YunShop::app()->getMemberId();
        $supplier_model = Supplier::getSupplierByMemberId($member_id);
        /*$uniacid = \YunShop::app()->uniacid;
        $list = SupplierOrderJoinOrder::IsPlugin()->where('uniacid', $uniacid)->with('hasManyOrderGoods')->status(1)->whereHas('beLongsToSupplierOrder', function ($query) use ($supplier_model) {
            $query->where('supplier_id', $supplier_model->id);
        })->orderBy('yz_order.id', 'desc')->paginate(20)->toArray();*/
        $orderIds = SupplierOrder::select()
            ->where('supplier_id', $supplier_model->id)
            ->orderBy('order_id', 'desc')
            ->pluck('order_id');

        // $uniacid = \YunShop::app()->uniacid;
        $list = SupplierOrderJoinOrder::with(['hasManyOrderGoods'])
            ->where('status', 1)
            ->whereIn('id', $orderIds)
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->toArray();
        if (empty($list)) {
            return $this->errorJson('没有订单!', 0);
        }
        return $this->successJson('ok', $list);
    }

    /**
     * 待收貨
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitReceive()
    {
        $member_id = \YunShop::app()->getMemberId();
        $supplier_model = Supplier::getSupplierByMemberId($member_id);
        /*$uniacid = \YunShop::app()->uniacid;
        $list = SupplierOrderJoinOrder::IsPlugin()->where('uniacid', $uniacid)->with('hasManyOrderGoods')->status(2)->whereHas('beLongsToSupplierOrder', function ($query) use ($supplier_model) {
            $query->where('supplier_id', $supplier_model->id);
        })->orderBy('yz_order.id', 'desc')->paginate(20)->toArray();*/
        $orderIds = SupplierOrder::select()
            ->where('supplier_id', $supplier_model->id)
            ->orderBy('order_id', 'desc')
            ->pluck('order_id');

        // $uniacid = \YunShop::app()->uniacid;
        $list = SupplierOrderJoinOrder::with(['hasManyOrderGoods'])
            ->where('status', 2)
            ->whereIn('id', $orderIds)
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->toArray();
        if (empty($list)) {
            return $this->errorJson('没有订单!', 0);
        }
        return $this->successJson('ok', $list);
    }

    /**
     * 已完成
     * @return \Illuminate\Http\JsonResponse
     */
    public  function completed()
    {
        $member_id = \YunShop::app()->getMemberId();
        $supplier_model = Supplier::getSupplierByMemberId($member_id);
        /*$uniacid = \YunShop::app()->uniacid;
        $list = SupplierOrderJoinOrder::IsPlugin()->where('uniacid', $uniacid)->with('hasManyOrderGoods')->status(3)->whereHas('beLongsToSupplierOrder', function ($query) use ($supplier_model) {
            $query->where('supplier_id', $supplier_model->id);
        })->orderBy('yz_order.id', 'desc')->paginate(20)->toArray();*/

        $orderIds = SupplierOrder::select()
            ->where('supplier_id', $supplier_model->id)
            ->orderBy('order_id', 'desc')
            ->pluck('order_id');

        // $uniacid = \YunShop::app()->uniacid;
        $list = SupplierOrderJoinOrder::with(['hasManyOrderGoods'])
            ->where('status', 3)
            ->whereIn('id', $orderIds)
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->toArray();
        if (empty($list)) {
            return $this->errorJson('没有订单!', 0);
        }
        return $this->successJson('ok', $list);
    }

    /**
     * 关闭订单
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function closeOrder()
    {
        $error_code = 0;
        $params = \YunShop::request()->get();
        $order = SupplierOrderJoinOrder::IsPlugin()->where('id', $params['order_id'])->first();

        if (!isset($order)) {
            return $this->errorJson('未找到该订单!', $error_code);
        }

        OrderService::orderClose($params);
        return $this->successJson('关闭订单成功!');
    }

    /**
     * 发货(大宗司机)
     * @throws \app\common\exceptions\AppException
     */
    public function send()
    {
        $params = \YunShop::request()->get();
        $orderOperation = \Yunshop\Supplier\common\order\OrderSend::find($params['order_id']);

        if (!isset($orderOperation)) {
            return $this->errorJson('未找到该订单!', 0);
        }
        DB::transaction(function() use($orderOperation) {
            $orderOperation->handle();
        });

        return $this->successJson('操作成功');
    }

    /**
     * 发货
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    /*public function send()
    {
        $error_code = 0;
        $params = \YunShop::request()->get();
        $order = SupplierOrderJoinOrder::IsPlugin()->where('id', $params['order_id'])->first();
        if (!isset($order)) {
            return $this->errorJson('未找到该订单!', $error_code);
        }

        OrderService::orderSend($params);
        return $this->successJson('发货成功!');
    }*/

    /**
     * 取消发货
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function cancelSend()
    {
        $error_code = 0;
        $params = \YunShop::request()->get();
        $order = SupplierOrderJoinOrder::IsPlugin()->where('id', $params['order_id'])->first();
        if (!isset($order)) {
            return $this->errorJson('未找到该订单!', $error_code);
        }

        OrderService::orderCancelSend($params);
        return $this->successJson('取消发货成功!');

    }

    /**
     * 订单详情
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail()
    {
        $orderId = request()->order_id;
        $order = SupplierOrderJoinOrder::getOrderDetailById($orderId);

        if (empty($order)) {
            return $this->errorJson('没有找到该订单!');
        }
        return $this->successJson('获取订单成功!', $order);
    }

    /**
     * 获取快递公司
     * @return \Illuminate\Http\JsonResponse
     */
    public function expressCompany()
    {
        $order = Order::find(request('order_id'));
        if (!$order) {
            return $this->errorJson('未找到订单');
        }
        $expressCompanies = ExpressCompany::create()->all();
        return $this->successJson('成功', ['express_companies' => $expressCompanies, 'address' => $order->address]);
    }
}
