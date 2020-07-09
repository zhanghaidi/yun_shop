<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/27 上午11:35
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Modules\Love\Controllers;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\common\models\Income;
use Illuminate\Support\Facades\DB;
use Yunshop\Love\Backend\Widgets\Income\LoveWithdrawWidget;
use Yunshop\Love\Common\Events\LoveWithdrawAppliedEvent;
use Yunshop\Love\Common\Events\LoveWithdrawApplyEvent;
use Yunshop\Love\Common\Events\LoveWithdrawApplyingEvent;
use Yunshop\Love\Common\Models\LoveWithdrawRecords;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Common\Services\ConstService;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Love\Common\Services\SetService;

class WithdrawController extends ApiController
{
    private $memberModel;

    public function preAction()
    {
        parent::preAction();

        $this->memberModel = $this->setMemberModel();
    }

    /**
     * 爱心值提现页面接口
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function page()
    {
        $set = Setting::get('withdraw.loveWithdraw', ['roll_out_limit' => 0, 'poundage_rate' => 0]);
        $love_name = CommonService::getLoveName();
        $integral_name = app('plugins')->isEnabled('integral') ? \Yunshop\Integral\Common\Services\SetService::getIntegralName() : '';
        $data = [
            'usable'                       => $this->memberModel->love->usable,
            'withdraw_multiple'            => $this->getWithdrawMultiple(),
            'withdraw_scale'               => $this->getWithdrawScale(),
            'withdraw_integral_scale'      => $this->getWithdrawIntegralScale(),
            'withdraw_poundage'            => $set['poundage_rate'] ?: 0,
            'withdraw_fetter'              => $set['roll_out_limit'] ?: 0,
            'integral_withdraw_status'     => SetService::getLoveSet('integral_withdraw_status') ?: 0,
            'integral_withdraw_proportion' => SetService::getLoveSet('integral_withdraw_proportion') ?: 0,
            'proportion_switch'            => SetService::getLoveSet('proportion_switch') ?: 0,
            'integral_name'                => $integral_name,
            'love_name'                        => $love_name,
        ];
        return $this->successJson('ok', $data);
    }

    /**
     * 爱心值提现接口
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //type 1 提现到收入   2 提现到消费积分
        $type = \YunShop::request()->type;
        DB::transaction(function () {
            $this->withdrawStart();
        });

        return $this->successJson('提现成功', ['type' => $type]);
    }

    private function withdrawStart()
    {
        $LoveWithdrawModel = new LoveWithdrawRecords();

        $LoveWithdrawModel->fill($this->loveWithdrawData());

        /**
         * 爱心值提现申请事件
         */
        event(new LoveWithdrawApplyEvent($LoveWithdrawModel));

        $validator = $LoveWithdrawModel->validator();
        if ($validator->fails()) {
            throw new AppException("ERROR:{$validator->messages()->first()}");
        }

        /**
         * 爱心值提现申请中事件
         */
        event(new LoveWithdrawApplyingEvent($LoveWithdrawModel));

        if (!$LoveWithdrawModel->save()) {
            throw new AppException("ERROR:Love withdraw data storage exception!");
        }
        /**
         * 爱心值提现申请后事件
         */
        event(new LoveWithdrawAppliedEvent($LoveWithdrawModel));

        //todo 以下代码可以放在observer 或 listener 中执行 190509

        $change_value = \YunShop::request()->type == 1 ? $this->getPostValue() : $this->getPostVal();

        $result = $this->updateLove($change_value);

        if ($result !== true) {
            throw new AppException("提现失败！更新记录失败");
        }

        if (\YunShop::request()->type == 1) {

            $result = $this->insertIncome($change_value, $LoveWithdrawModel->id);

        } elseif (\YunShop::request()->type == 2) {
            $result = $this->insertIntegral($change_value);

        }
        if ($result !== true) {
            throw new AppException("提现失败！插入记录失败");
        }
    }

    /**
     * 提现记录数据
     *
     * @return array
     * @throws AppException
     */
    private function loveWithdrawData()
    {
        return [
            'uniacid'              => \Yunshop::app()->uniacid,
            'member_id'            => $this->memberModel->uid,
            'path'                 => \YunShop::request()->type,
            'processing_fee_ratio' => \YunShop::request()->type == 1 ? $this->getWithdrawBalanceProcessingFee() : $this->getWithdrawProcessingFee(),
            'conversion_ratio'     => \YunShop::request()->type == 1 ? $this->getWithdrawBalanceScale() : $this->getWithdrawIntegralScale(),
            'love_value'           => \YunShop::request()->type == 1 ? $this->getPostValue() : $this->getPostVal(),
        ];
    }

    private function insertIncome($change_value, $love_records_id)
    {
        //爱心值提现到收入的比例
        $scale = $this->getWithdrawScale();
        if (empty($scale)) {
            $scale = 1;
        }
        $change_value = $change_value * $scale;

        $love_name = CommonService::getLoveName();
        $data = [
            'uniacid'          => \YunShop::app()->uniacid,
            'member_id'        => $this->memberModel->uid,
            'incometable_type' => LoveWithdrawRecords::class,
            'incometable_id'   => $love_records_id,
            'type_name'        => $love_name . "提现",
            'amount'           => $change_value,
            'status'           => 0,
            'pay_status'       => 0,
            'detail'           => '',
            'create_month'     => date('Y-m', time())
        ];

        $result = Income::create($data);
        return $result ? true : false;
    }

    private function insertIntegral($change_value)
    {
        //爱心值提现到收入的比例
        if (app('plugins')->isEnabled('integral')) {

            $love_name = CommonService::getLoveName();
            $scale = SetService::getLoveSet('integral_withdraw_scale') ?: 1;
            $proportion = SetService::getLoveSet('integral_withdraw_proportion') ?: 0;

            //扣除兑换比例之后
            //$change_value = ($change_value * $scale) - (($change_value * $scale) * $proportion/100);
            //$change_value = bcsub(bcmul($change_value,$scale,4),bcmul(bcmul($change_value,$scale,4),bcdiv($proportion,100,4),4),2);
            $into_integral = bcmul($change_value, $scale, 4);
            $processing_fee_ratio = bcdiv($proportion, 100, 4);

            $change_value = bcsub($into_integral, bcmul($into_integral, $processing_fee_ratio, 4), 2);
            $data = [
                'uid'          => $this->memberModel->uid,
                'uniacid'      => \Yunshop::app()->uniacid,
                'change_value' => $change_value,
                'order_sn'     => \Yunshop\Integral\Backend\Models\IntegralRechargeModel::createOrderSn('IR'),
                'source_type'  => LoveWithdrawRecords::class,
                'source_id'    => 32,
                'remark'       => $love_name . '提现',
                'type'         => 1,
            ];
            $result = (new \Yunshop\Integral\Common\Services\IntegralChangeServer())->loveWithdrawalInto($data);
            return $result === true ? true : false;
        }

        return false;

    }


    private function updateLove($change_value)
    {
        $data = [
            'member_id'    => $this->memberModel->uid,
            'change_value' => $change_value,
            'operator'     => ConstService::OPERATOR_MEMBER,
            'operator_id'  => $this->memberModel->uid,
            'remark'       => '会员可用值提现，金额：' . $change_value,
            'relation'     => ''
        ];
        if (\YunShop::request()->type == 1) {
            return (new LoveChangeService())->withdrawal($data);
        } elseif (\YunShop::request()->type == 2) {
            return (new LoveChangeService())->withdrawalIntegral($data);
        }

    }


    private function setMemberModel()
    {
        $memberModel = CommonService::getLoveMemberModelById(\YunShop::app()->getMemberId());
        if (!$memberModel) {
            throw new AppException('未检测到会员信息');
        }
        return $memberModel;
    }


    private function getPostValue()
    {
        $change_value = \YunShop::request()->change_value;

        $this->validate(['change_value' => ['required', 'numeric', 'min:0.01', 'regex:/^[0-9]+(.[0-9]{1,2})?$/', 'max:99999999.99']]);

        $set = Setting::get('withdraw.loveWithdraw', ['roll_out_limit' => 0, 'poundage_rate' => 0]);
        if (!empty($set['poundage_rate']) && $change_value < $set['roll_out_limit']) {
            throw new AppException("提现值不能小于" . $set['roll_out_limit']);
        }

        if ($set['poundage_type'] == 1) {
            $poundage = $set['poundage_rate'];
        } else {
            $poundage = bcdiv(bcmul($change_value, $set['poundage_rate'], 4), 100, 2);
        }
        if (!empty($set['poundage_rate']) && ($change_value - $poundage) < 1) {
            throw new AppException('提现值扣除手续费不能小于 1');
        }

        $result = $this->getWithdrawMultiple();
        if ($result && fmod($change_value, $result) != 0) {
            throw new AppException('提现值必须是' . $result . '的倍数');
        }

        return trim($change_value);
    }


    private function getPostVal()
    {
        $love_set = SetService::getLoveSet();
        if ($love_set['integral_withdraw_status'] == 1) {
            $change_value = \YunShop::request()->change_value;

            $this->validate(['change_value' => ['required', 'numeric', 'min:0.01', 'regex:/^[0-9]+(.[0-9]{1,2})?$/', 'max:99999999.99']]);

            //会员余额
            $credit1 = $this->memberModel->credit1;

            //$service_charge = $change_value * $love_set['integral_withdraw_proportion']/100;
            $service_charge = bcmul($change_value, bcdiv($love_set['integral_withdraw_proportion'], 100, 4));
            if (SetService::getLoveSet('proportion_switch') == 1 && $service_charge > $credit1) {
                \Log::debug('爱心值提现失败积分不足扣除手续费');
                throw new AppException("提现失败:积分不足");
            }

            return trim($change_value);
        }
        \Log::debug('爱心值提现失败未开启提现到消费积分');
        throw new AppException("提现失败");

    }

    /**
     * 提现限制： 提现倍数
     * @return array|mixed|string
     */
    private function getWithdrawMultiple()
    {
        return SetService::getLoveSet('withdraw_multiple');
    }

    /**
     * 提现限制： 提现到余额比例
     * @return array|mixed|string
     */
    private function getWithdrawScale()
    {
        return SetService::getLoveSet('withdraw_scale') ?: 1;
    }


    /**
     * 提现限制： 提现到消费积分比例
     * @return array|mixed|string
     */
    private function getWithdrawIntegralScale()
    {
        return SetService::getLoveSet('integral_withdraw_scale') ?: 1;
    }


    /**
     * 提现限制： 提现到余额比例
     * @return array|mixed|string
     */
    private function getWithdrawBalanceScale()
    {
        return SetService::getLoveSet('withdraw_scale') ?: 1;
    }


    /**
     * 提现限制： 提现到消费积分手续费比例
     * @return array|mixed|string
     */
    private function getWithdrawProcessingFee()
    {
        return (SetService::getLoveSet('integral_withdraw_proportion') ?: 0) . '%';
    }

    /**
     * 提现限制： 提现到余额手续费
     * @return array|mixed|string
     */
    private function getWithdrawBalanceProcessingFee()
    {
        $balanceset = Setting::get('withdraw.loveWithdraw', ['roll_out_limit' => 0, 'poundage_rate' => 0]);
        if ($balanceset['poundage_type'] == 1) {
            $service_fee = ($balanceset['poundage_rate'] ?: 0) . '元';
        } else {
            $service_fee = ($balanceset['poundage_rate'] ?: 0) . '%';
        }
        return $service_fee;
    }

}
