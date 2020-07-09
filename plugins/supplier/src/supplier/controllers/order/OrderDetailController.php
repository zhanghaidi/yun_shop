<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/3
 * Time: 上午10:14
 */

namespace Yunshop\Supplier\supplier\controllers\order;


use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use app\common\models\DispatchType;
use Yunshop\Supplier\common\controllers\SupplierCommonController;
use Yunshop\Supplier\common\models\SupplierOrderJoinOrder;
use Yunshop\Supplier\supplier\models\SupplierOrder;
use app\common\services\Session;
use app\backend\modules\member\models\Member;
use app\common\services\DivFromService;

class OrderDetailController extends SupplierCommonController
{
    public function index($request)
    {
        $orderId = $request->query('id');

        $order_verify = SupplierOrder::getOrderBySupplierIdAndOrderId(Session::get('supplier')['id'], $orderId)->first();
        if (!$order_verify) {
            throw new ShopException('订单ID['.$orderId.']不属于您!');
        }

        $order = SupplierOrderJoinOrder::getOrderDetailById($orderId);
        if (!$order) {
            return $this->message('订单不存在！', Url::absoluteWeb('supplier.supplier.controllers.order.supplier-order'), 'error');
        }

        if(!empty($order->express)){
            $express = $order->express->getExpress($order->express->express_code, $order->express->express_sn);
            $dispatch['express_sn'] = $order->express->express_sn;
            $dispatch['company_name'] = $order->express->express_company_name;
            $dispatch['data'] = $express['data'];
            $dispatch['thumb'] = $order->hasManyOrderGoods[0]->thumb;
            $dispatch['tel'] = '95533';
            $dispatch['status_name'] = $express['status_name'];
        }

        return view('Yunshop\Supplier::supplier.order.detail', [
            'order'         => $order ? $order->toArray() : [],
            'dispatch'      => $dispatch,
            'div_from'      => $this->getDivFrom($order),
            'var'           => \YunShop::app()->get(),
            'ops'           => 'Yunshop\Supplier::supplier.order.ops',
            'modals'        => 'Yunshop\Supplier::supplier.order.modals'
        ])->render();
    }

    private function getDivFrom($order)
    {
        if (!$order || !$order->hasManyOrderGoods) {
            return ['status' => false];
        }
        $goods_ids = [];
        foreach ($order->hasManyOrderGoods as $key => $goods) {
            $goods_ids[] = $goods['goods_id'];
        }

        $memberInfo = Member::select('realname', 'idcard')->where('uid', $order->uid)->first();

        $result['status'] = DivFromService::isDisplay($goods_ids);
        $result['member_name'] = $memberInfo->realname;
        $result['member_card'] = $memberInfo->idcard;

        return $result;
    }
}