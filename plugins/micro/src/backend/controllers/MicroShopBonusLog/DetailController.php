<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/23
 * Time: 下午4:55
 */

namespace Yunshop\Micro\backend\controllers\MicroShopBonusLog;


use app\common\components\BaseController;
use Yunshop\Micro\common\models\Order;

class DetailController extends BaseController
{
    public function index()
    {
        $order_id = \YunShop::request()->id;
        $order = Order::getOrderDetailById($order_id);
        if(!empty($order->express)){
            $express = $order->express->getExpress($order->express->express_code, $order->express->express_sn);
//            dd($express);
//            exit;
            $dispatch['express_sn'] = $order->express->express_sn;
            $dispatch['company_name'] = $order->express->express_company_name;
            $dispatch['data'] = $express['data'];
            $dispatch['thumb'] = $order->hasManyOrderGoods[0]->thumb;
            $dispatch['tel'] = '95533';
            $dispatch['status_name'] = $express['status_name'];
        }

        return view('order.detail', [
            'order'         => $order ? $order->toArray() : [],
            'dispatch' => $dispatch,
            'var'           => \YunShop::app()->get(),
            'ops'           => 'order.ops'
        ])->render();
    }
}