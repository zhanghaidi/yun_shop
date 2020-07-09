<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/31
 * Time: 10:05
 */

namespace Yunshop\Supplier\frontend;


use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\models\Member;
use Yunshop\Supplier\common\models\Supplier;
use Yunshop\Supplier\common\models\SupplierOrder;
use Yunshop\Supplier\common\models\SupplierOrderJoinOrder;
use Yunshop\Supplier\supplier\models\SupplierWithdraw;

class IndexController extends ApiController
{
    public function index()
    {
        $set = Setting::get('plugin.supplier');
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=time();
        $today = [$beginToday, $endToday];
        $error_code = 0;
        $member_id = \YunShop::app()->getMemberId();
        $uniacid = \YunShop::app()->uniacid;
        $supplier_model = Supplier::getSupplierByMemberId($member_id);

         //判断是否供应商
        if (empty($supplier_model)){
             return $this->errorJson('你不是供应商!', ['error_code' => $error_code,'url'=> yzAppFullUrl('member/supplier')]);
        }

        $member_model = Member::getMemberById($member_id); //供应商信息
        $cost_money = SupplierWithdraw::getSureOrderInformation($supplier_model->id, 'profit',''); //可提现金额
        $money_total = SupplierOrder::where('supplier_id', $supplier_model->id)->sum('supplier_profit');  //累计金额
        $order_count = SupplierOrder::where('supplier_id', $supplier_model->id)
            ->whereHas('order',function ($query){
                $query->whereIn('status',[1,2,3]);
            })
            ->whereBetween('created_at', $today)->count(); //今日订单数
        $order_amount = SupplierOrderJoinOrder::IsPlugin()->where('uniacid', $uniacid)->whereIn('status',[1,2,3])->whereBetween('created_at', $today)->whereHas('beLongsToSupplierOrder', function ($query) use ($supplier_model) {
            $query->where('supplier_id', $supplier_model->id);
        })->sum('price'); //今日销售额

        $set['banner_1'] = yz_tomedia($set['banner_1']);
        $set['banner_2'] = yz_tomedia($set['banner_2']);
        $set['banner_3'] = yz_tomedia($set['banner_3']);

        //总数据
        $data = [
            'supplier_id' => $supplier_model->id,
            'member_model' => $member_model,
            'cost_money' => round($cost_money, 2),
            'money_total' => $money_total,
            'order_count' => $order_count,
            'order_amount' => $order_amount,
            'set' => $set,
        ];
        if (!empty($data)) {
            return $this->successJson('ok', ['data' => $data]);
        }
    }


    /**
     * 控制前端是否显示保单
     */
    public function policyControl(){

        $supplier_setting = \Setting::get('plugin.supplier');

        if (!empty($supplier_setting['insurance_policy']) &&  $supplier_setting['insurance_policy']){//开启状态
            return $this->successJson('ok', 1);
        }else{
            return $this->successJson('ok', 0);
        }
    }
}