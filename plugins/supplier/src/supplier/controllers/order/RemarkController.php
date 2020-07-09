<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 下午8:12
 */

namespace Yunshop\Supplier\supplier\controllers\order;


use app\common\models\order\Remark;
use Yunshop\Supplier\common\controllers\SupplierCommonController;
use app\common\exceptions\AppException;
use Yunshop\Supplier\common\models\SupplierOrderJoinOrder;

class RemarkController extends SupplierCommonController
{
    public function index()
    {
        $db_remark_model = Remark::where('order_id', \YunShop::request()->order_id)->first();
        if (!$db_remark_model) {
            Remark::create(
                [
                    'order_id' => \YunShop::request()->order_id,
                    'remark' => \YunShop::request()->remark
                ]
            );
            show_json(1);
        }
        $db_remark_model->remark = \YunShop::request()->remark;
        $db_remark_model->save();
        show_json(1);
    }

    public function set()
    {
        $order = SupplierOrderJoinOrder::find(request()->input('order_id'));
        if(!$order){
            throw new AppException("未找到该订单".request()->input('order_id'));
        }

        if (request()->has('invoice')) {
            $order->invoice = request()->input('invoice');
            $order->save();
        }
        if(request()->has('remark')){
            $remark = $order->hasOneOrderRemark;
            if (!$remark) {
                $remark = new Remark([
                    'order_id' => request()->input('order_id'),
                    'remark' => request()->input('remark')
                ]);

                if(!$remark->save()){
                    return $this->errorJson();
                }
            } else {
                $reUp = Remark::where('order_id', request()->input('order_id') )
                    ->where('remark', $remark->remark)
                    ->update(['remark'=> request()->input('remark')]);

                if (!$reUp) {
                    return $this->errorJson();
                }
            }
        }
        //(new \app\common\services\operation\OrderLog($remark, 'special'));
        echo json_encode(["data" => '', "result" => 1]);
    }
}