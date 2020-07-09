<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/26
 * Time: 下午3:45
 */

namespace Yunshop\Supplier\admin\controllers\order;


use app\common\components\BaseController;
use Yunshop\Supplier\common\models\SupplierOrderJoinOrder;
use app\backend\modules\member\models\Member;
use app\common\services\DivFromService;

class SupplierOrderDetailController extends BaseController
{
    public function index($request)
    {
        $orderId = $request->query('id');
        $order = SupplierOrderJoinOrder::getOrderDetailById($orderId);

        if(!empty($order->express)){
            $express = $order->express->getExpress($order->express->express_code, $order->express->express_sn);
            $dispatch['express_sn'] = $order->express->express_sn;
            $dispatch['company_name'] = $order->express->express_company_name;
            $dispatch['data'] = $express['data'];
            $dispatch['thumb'] = $order->hasManyOrderGoods[0]->thumb;
            $dispatch['tel'] = '95533';
            $dispatch['status_name'] = $express['status_name'];
        }

        return view('Yunshop\Supplier::admin.order.detail', [
            'order'         => $order ? $order->toArray() : [],
            'dispatch'      => $dispatch,
            'div_from'      => $this->getDivFrom($order),
            'var'           => \YunShop::app()->get(),
            'ops'           => 'Yunshop\Supplier::admin.order.ops',
            'edit_goods'    => 'plugin.supplier.admin.controllers.goods.goods-operation.edit'
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