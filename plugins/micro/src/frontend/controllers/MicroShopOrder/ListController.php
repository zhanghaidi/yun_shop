<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/17
 * Time: 下午8:14
 */

namespace Yunshop\Micro\frontend\controllers\MicroShopOrder;

use app\common\components\ApiController;
use Yunshop\Micro\common\models\MicroShopBonusLog;

class ListController extends ApiController
{
    public function index()
    {
        $status = \YunShop::request()->order_status;
        $apply_status = \YunShop::request()->apply_status;
        $list = MicroShopBonusLog::getBonusLogByMemberId(\YunShop::app()->getMemberId())->byOrderStatus($status)->applyStatus($apply_status)->isLower(0)->orderBy('id', 'desc')->paginate(10);
        $list->map(function ($log){
            $log->price = $log->goods_price * $log->goods_total;
            $log->order_time = $log->order_status == 1 ? ('支付时间：' . $log->pay_time) : '';
            $log->order_time .= ' ';
            $log->order_time .= $log->order_status == 3 ? ('完成时间：' . $log->complete_time) : '';
        });

        return $this->successJson('成功', [
            'status'    => 1,
            'list'      => $list
        ]);
    }
}