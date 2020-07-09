<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/21
 * Time: 下午6:54
 */

namespace Yunshop\Supplier\admin\controllers\withdraw;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use Yunshop\Supplier\admin\models\SupplierWithdraw;
use Yunshop\Supplier\common\services\withdraw\SupplierWithdrawService;
use app\backend\modules\finance\services\WithdrawService;
use app\common\services\PayFactory;

class WithdrawOperationController extends BaseController
{
    public function index()
    {
        $withdraw_id = \YunShop::request()->withdraw_id;
        $type = \YunShop::request()->type;
        return $this->operation($withdraw_id, $type);
    }

    private function operation($withdraw_id, $type)
    {
        $withdraw = SupplierWithdrawService::verifyWithdrawIsEmpty(SupplierWithdraw::getWithdrawById($withdraw_id));
        $withdraw->status = $type;
        $withdraw->save();
        return $this->message('审核成功', Url::absoluteWeb('plugin.supplier.admin.controllers.withdraw.supplier-withdraw.detail', ['withdraw_id' => $withdraw->id]));
    }

    public function pay()
    {
        $withdraw_id = \YunShop::request()->id;

        /**
         * @var $withdrawModel SupplierWithdraw
         */
        $withdrawModel = SupplierWithdraw::find($withdraw_id);

        //增加打款状态4，打款中，支付宝可能没有回调
        $withdrawModel->status = 4;
        $withdrawModel->pay_time = time();
        $withdrawModel->save();

        $result = $this->tryPay($withdrawModel);

        if ($result === true) {
            $withdrawModel->status = 3;
            $withdrawModel->pay_time = time();
            $withdrawModel->save();
        }

        return $this->message('成功', Url::absoluteWeb('plugin.supplier.admin.controllers.withdraw.supplier-withdraw.detail', ['withdraw_id' => $withdrawModel->id]));

        //return $this->message('失败', Url::absoluteWeb('plugin.supplier.admin.controllers.withdraw.supplier-withdraw.detail', ['withdraw_id' => $withdraw->id]), 'error');
    }


    public function tryPay($withdrawModel)
    {
        try {

            return $this->_tryPay($withdrawModel);

        } catch (ShopException $exception) {
            $withdrawModel->status = 2;
            $withdrawModel->pay_time = null;
            $withdrawModel->save();

            throw new ShopException($exception->getMessage());
        }
    }


    private function _tryPay($withdrawModel)
    {
        switch ($withdrawModel->type) {

            case 1:
                $result = $this->manualWithdraw($withdrawModel);
                break;
            case 2:
                $result = $this->weChatWithdraw($withdrawModel);
                break;
            case 3:
                $result = $this->aliPayWithdraw($withdrawModel);
                break;
            case 4:
                $result = $this->yopWithdraw($withdrawModel);
                break;
            case 5:
                $result = $this->ConvergePayWithdraw($withdrawModel);
                break;
            default:
                throw new ShopException('未知打款类型');
        }

        return $result;
    }


    /**
     * 手动打款
     *
     * @param $withdrawModel
     * @return bool
     */
    private function manualWithdraw($withdrawModel)
    {
        return true;
    }


    /**
     * 微信打款
     *
     * @param $withdrawModel
     * @return bool
     * @throws ShopException
     */
    private function weChatWithdraw($withdrawModel)
    {
        $remark = '提现打款-微信-金额:' . $withdrawModel->money . '元';

        $result = PayFactory::create(1)->doWithdraw($withdrawModel->member_id, $withdrawModel->apply_sn, $withdrawModel->money, $remark);
        if ($result['errno'] == 0) {
            return true;
        }
        throw new ShopException("收入提现ID：{$withdrawModel->id}，提现失败：{$result['message']}");
    }

    /**
     * 支付宝打款
     *
     * @param $withdrawModel
     * @return bool
     * @throws ShopException
     */
    private function aliPayWithdraw($withdrawModel)
    {
        $remark = '提现打款-支付宝-金额:' . $withdrawModel->money . '元';
        $result = PayFactory::create(2)->doWithdraw($withdrawModel->member_id, $withdrawModel->apply_sn, $withdrawModel->money, $remark);

        if (is_array($result)) {

            if ($result['errno'] == 1) {
                throw new ShopException("收入提现ID：{$withdrawModel->id}，提现失败：{$result['message']}");
            }
            return true;
        }

        redirect($result)->send();
    }

    /**
     * 易宝打款
     * @param $withdrawModel
     * @return bool
     */
    private function yopWithdraw($withdrawModel)
    {

        $result = PayFactory::create(PayFactory::YOP)->doWithdraw($withdrawModel->member_id, $withdrawModel->apply_sn, $withdrawModel->money, 'sup');

        if ($result['errno'] == 200) {

            return false;
        }

        throw new ShopException("收入提现ID：{$withdrawModel->id}，提现失败：{$result['message']}");
    }

    /**
     * 汇聚打款
     *
     * @param $withdrawModel
     * @return bool
     * @throws ShopException
     * @throws \app\common\exceptions\AppException
     */
    private function ConvergePayWithdraw($withdrawModel)
    {
        $remark = '供应商提现打款-汇聚-金额:' . $withdrawModel->money . '元';

        $result = PayFactory::create(PayFactory::PAY_WECHAT_HJ)->doWithdraw($withdrawModel->member_id, $withdrawModel->apply_sn, $withdrawModel->money, $remark, 'supplier');

        if (!$result['verify']) {
            return true;
        }

        \Log::debug("-----收入提现ID：{$withdrawModel->id}-----.-----汇聚提现失败：{$result['msg']}-----");
        throw new ShopException("收入提现ID：{$withdrawModel->id}，提现失败：{$result['msg']}");
    }
}
