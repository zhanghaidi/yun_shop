<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/7/15
 * Time: 下午 02:22
 */

namespace Yunshop\Supplier\common\services\withdraw;


use Yunshop\Supplier\supplier\models\SupplierWithdraw;
use Illuminate\Support\Facades\Log;
use app\common\exceptions\ShopException;
use app\backend\modules\finance\controllers\BalanceWithdrawController;
use Yunshop\Supplier\admin\controllers\withdraw\WithdrawOperationController;

class SupplierAutomaticService
{
    /**
     * @var Withdraw
     */
    private $withdrawModel;


    public function __construct(SupplierWithdraw $withdrawModel)
    {
        $this->withdrawModel = $withdrawModel;
        \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $withdrawModel->uniacid;
    }


    /**
     * 提现免审核
     *
     * @throws ShopException
     */
    public function freeAudit()
    {
        $this->withdrawAudit();
        $this->withdrawPay();
        Log::debug("供应商提现免审核ID:{$this->withdrawModel->id}自动审核打款完成");
    }


    /**
     * 提现审核
     *
     * @throws ShopException
     */
    private function withdrawAudit()
    {
        $withdraw_id = $this->withdrawModel->id;
        // 通过审核
        $type = 2;

        $this->withdrawUpdate($withdraw_id, $type);
    }


    /**
     * 提现打款
     *
     * @throws ShopException
     */
    private function withdrawPay()
    {
        $SupplierWithdraw = new WithdrawOperationController;

        $SupplierWithdraw->tryPay($this->withdrawModel);
    }

    /**
     * 提现 model 数据保存
     * @return bool
     * @throws ShopException
     */
    private function withdrawUpdate($withdraw_id, $type)
    {
        $withdraw = SupplierWithdrawService::verifyWithdrawIsEmpty(SupplierWithdraw::getWithdrawById($withdraw_id));
        $withdraw->status = $type;

        if (!$withdraw->save()) {
            Log::debug("供应商提现审核失败:{$this->withdrawModel->id}数据修改失败");
            throw new ShopException("供应商提现审核失败:{$this->withdrawModel->id}数据修改失败");
        }

        return true;
    }
}