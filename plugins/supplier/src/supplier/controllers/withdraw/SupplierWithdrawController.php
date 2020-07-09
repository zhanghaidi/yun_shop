<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/27
 * Time: 上午10:38
 */

namespace Yunshop\Supplier\supplier\controllers\withdraw;


use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use app\common\services\Session;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\controllers\SupplierCommonController;
use Yunshop\Supplier\common\services\VerifyWithdraw;
use Yunshop\Supplier\common\services\withdraw\SupplierWithdrawService;
use Yunshop\Supplier\supplier\models\SupplierWithdraw;

class SupplierWithdrawController extends SupplierCommonController
{
    public function apply()
    {
        $result = VerifyWithdraw::verifyWithdraw(Session::get('supplier')['id']);
        if ($result) {
            return $this->message($result . '可以提现！', Url::absoluteWeb('plugin.supplier.supplier.controllers.order.supplier-order'), 'error');
        }
        $supplier = Supplier::getSupplierById(Session::get('supplier')['id']);
        if ((\YunShop::request()->apply_type == 1) && !$supplier->bank_username) {
            return $this->message('请完善收款信息！', Url::absoluteWeb('plugin.supplier.supplier.controllers.info.index'), 'error');
        }
        $order_information = SupplierWithdraw::getSureOrderInformation(Session::get('supplier')['id'], '', 1);
        $set = \Setting::get('plugin.supplier');
        if ($set['service_type'] == 0) {
            $money = $order_information['total_profit'] - $set['service_money'];
        } else {
            $money = $order_information['total_profit'] - ($order_information['total_profit'] * $set['service_money'] / 100);
        }
        $money = $money <= 0 ? 0 : $money;
        $apply_data = [
            'supplier_id'   => Session::get('supplier')['id'],
            'member_id'     => Session::get('supplier')['member_id'],
            'status'        => 1,
            'service_type'  => $set['service_type'],
            'service_money' => $set['service_money'],
            'apply_money'   => $order_information['total_profit'],
            'money'         => $money,
            'order_ids'     => $order_information['order_ids'],
            'uniacid'       => \YunShop::app()->uniacid,
            'apply_sn'      => SupplierWithdraw::ApplySn(),
            'type'          => \YunShop::request()->apply_type
        ];
        SupplierWithdraw::createApply($apply_data);
        return $this->message('提现成功，等待审核', Url::absoluteWeb('plugin.supplier.supplier.controllers.order.supplier-order.index'));
    }

    public function detail()
    {
        $withdraw_id = \YunShop::request()->withdraw_id;

        $withdraw_verify = SupplierWithdraw::getWithdrawBySupplierIdAndWithdrawId(Session::get('supplier')['id'], $withdraw_id)->first();
        if (!$withdraw_verify) {
            throw new ShopException('未找到提现记录');
        }

        $withdraw = SupplierWithdrawService::verifyWithdrawIsEmpty(SupplierWithdraw::getWithdrawById($withdraw_id));
        return view('Yunshop\Supplier::supplier.withdraw.supplier_withdraw_detail', [
            'withdraw'      => $withdraw,
            'order_count'   => count(explode(',', $withdraw->toArray()['order_ids'])),
            'var'           => \YunShop::app()->get()
        ])->render();
    }
}