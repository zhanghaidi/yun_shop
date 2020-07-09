<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2018/11/28
 * Time: 11:56 AM
 */

namespace Yunshop\Love\Frontend\Modules\Love\Controllers;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\services\PayFactory;
use Illuminate\Support\Facades\Log;
use Yunshop\Commission\models\Agents;
use Yunshop\Love\Common\Services\ConstService;
use Yunshop\Love\Common\Services\SetService;
use Yunshop\Love\Frontend\Models\Member;
use Yunshop\Love\Frontend\Modules\Love\Models\LoveRechargeRecords;

class RechargeController extends ApiController
{
    protected $publicAction = ['alipay'];


    protected $ignoreAction = ['alipay'];


    private $memberModel;


    private $rechargeModel;


    private $recharge_rate_money;

    private $recharge_rate_love;

    /**
     * 爱心值充值页面接口
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function page()
    {
        $this->memberModel = $this->getMemberModel();
        $this->recharge_rate_money = SetService::getLoveSet('recharge_rate_money') ?: 1;
        $this->recharge_rate_love = SetService::getLoveSet('recharge_rate_love') ?: 1;
        $converge = $this->convergePay();

        $data = [
            'recharge_rate_money' => $this->recharge_rate_money,
            'recharge_rate_love'  => $this->recharge_rate_love,
            'member_usable'       => $this->memberModel->love->usable ?: 0,
            'converge_wechat_pay' => $converge['converge_wechat_pay'],
            'converge_alipay_pay' => $converge['converge_alipay_pay']
        ];

        return $this->successJson('ok', $data);
    }

    /**
     * 是否开启汇聚支付
     *
     * @return array
     */
    public function convergePay()
    {
        $converge_wechat_pay = false;
        $converge_pay_set = \Setting::get('plugin.convergePay_set');
        if (app('plugins')->isEnabled('converge_pay') && $converge_pay_set['converge_pay_status'] == 1) {
            if ($converge_pay_set['wechat']['wechat_status'] == 1 && \YunShop::request()->type == 2 ? $converge_pay_set['wechat']['XCX_appid'] : $converge_pay_set['wechat']['GZH_appid']) {
                $converge_wechat_pay = true;
            }

            $converge_alipay_pay = $converge_pay_set['alipay']['alipay_status'] == 1 ? true : false;
        }

        return [
            'converge_wechat_pay' => $converge_wechat_pay,
            'converge_alipay_pay' => $converge_alipay_pay
        ];
    }

    public function index()
    {

        $this->memberModel = $this->getMemberModel();
        $this->recharge_rate_money = SetService::getLoveSet('recharge_rate_money') ?: 1;
        $this->recharge_rate_love = SetService::getLoveSet('recharge_rate_love') ?: 1;

        //验证会员推客身份
        if (SetService::getLoveSet('recharge_condition') && app('plugins')->isEnabled('commission')) {
            $agentModel = Agents::where('member_id', $this->memberModel->uid)->first();
            $recharge_commission_level = unserialize(SetService::getLoveSet('recharge_commission_level')) ?: [];

            if (!$agentModel || !in_array($agentModel->agent_level_id, $recharge_commission_level)) {
                return $this->errorJson('会员推客等级不允许充值');
            }
        }

        $result = $this->addRechargeRecord();
        if (!$result) {
            return $this->errorJson('充值失败：数据异常');
        }

        $data = array(
            'uid'      => $this->rechargeModel->member_id,
            'order_sn' => $this->rechargeModel->order_sn
        );

        $pay_way = $this->getPayWay();

        if ($pay_way == PayFactory::PAY_WEACHAT
            || $pay_way == PayFactory::PAY_WECHAT_HJ
            || $pay_way == PayFactory::PAY_ALIPAY_HJ
        ) {
            return $this->successJson('支付接口对接成功', array_merge($data, $this->payOrder()));
        }
        if ($pay_way == PayFactory::PAY_ALIPAY) {
            return $this->successJson('支付接口对接成功', $data);
        }else {
                return $this->successJson('支付接口对接成功', array_merge($data));
        }
        return $this->errorJson("充值失败：未知支付方式");
    }


    public function alipay()
    {
        $order_sn = \YunShop::request()->order_sn;

        $this->rechargeModel = LoveRechargeRecords::where('order_sn', $order_sn)->first();
        if ($this->rechargeModel) {
            return $this->successJson('支付接口对接成功', $this->payOrder());
        }

        return $this->errorJson('充值订单不存在');
    }


    private function payOrder()
    {
        $pay = PayFactory::create($this->rechargeModel->type);

        $result = $pay->doPay($this->payData());
        Log::info('++++++++++++++++++', print_r($result, true));
        if ($this->rechargeModel->type == 1) {
            $result['js'] = json_decode($result['js'], 1);
        }
        Log::debug(LOVE_NAME . '充值 result', $result);
        return $result;
    }


    /**
     * 支付请求数据
     *
     * @return array
     * @Author yitian
     */
    private function payData()
    {
        $array = array(
            'subject'  => LOVE_NAME . '充值',
            'body'     => '充值个数' . $this->rechargeModel->change_value . ':' . \YunShop::app()->uniacid,
            'amount'   => $this->rechargeModel->money,
            'order_no' => $this->rechargeModel->order_sn,
            'extra'    => ['type' => 2],
            'ask_for'  => 'recharge'
        );
        return $array;
    }

    private function addRechargeRecord()
    {
        $this->rechargeModel = new LoveRechargeRecords();

        $this->rechargeModel->fill($this->getRechargeData());

        $validator = $this->rechargeModel->validator();
        if ($validator->fails()) {
            throw new AppException($validator->messages()->first());
        }
        return $this->rechargeModel->save();
    }

    private function getRechargeData()
    {
        return array(
            'type'         => $this->getPayWay(),
            'status'       => LoveRechargeRecords::STATUS_ERROR,
            'remark'       => $this->getRemark(),
            'uniacid'      => \YunShop::app()->uniacid,
            'member_id'    => \YunShop::app()->getMemberId(),
            'order_sn'     => LoveRechargeRecords::createOrderSn('RL', 'order_sn'),
            'value_type'   => $this->valueType(),
            'change_value' => $this->getChangeValue(),
            'money'        => $this->getRechargeMoney(),
        );
    }

    /**
     * @return int
     */
    private function valueType()
    {
        $set = $this->rechargeValueTypeSet();
        if ($set == ConstService::VALUE_TYPE_FROZE) {
            return ConstService::VALUE_TYPE_FROZE;
        }
        return ConstService::VALUE_TYPE_USABLE;
    }

    /**
     * @return string
     */
    private function rechargeValueTypeSet()
    {
        return SetService::getLoveSet('recharge_type');
    }

    /**
     * @return string
     */
    private function getRemark()
    {
        return "会员前端充值,每" . $this->recharge_rate_money . "元等于" . $this->recharge_rate_love . LOVE_NAME;
    }

    /**
     * @return string
     * @throws AppException
     */
    private function getChangeValue()
    {
        $recharge_money = $this->getRechargeMoney();

        return bcmul(bcdiv($recharge_money, $this->recharge_rate_money, 2), $this->recharge_rate_love, 2);
    }

    /**
     * 支付金额
     *
     * @return mixed
     * @throws AppException
     */
    private function getRechargeMoney()
    {
        $recharge_money = \YunShop::request()->recharge_money;
        if (!$recharge_money) {
            throw new AppException('充值金额不能为空');
        }
        if ($recharge_money <= 0) {
            throw new AppException('充值金额必须大于零');
        }
        return $recharge_money;
    }

    /**
     * @return int
     * @throws AppException
     */
    private function getPayWay()
    {
        $pay_way = (int)\YunShop::request()->pay_way;
        if (!$pay_way) {
            throw new AppException('支付方式不存在');
        }
        return $pay_way;
    }

    /**
     * @return Member
     * @throws AppException
     */
    private function getMemberModel()
    {
        $member_id = $this->getMemberId();

        $memberModel = Member::whereUid($member_id)->with('love')->first();
        if (!$memberModel) {
            throw new AppException('会员信息错误.');
        }
        return $memberModel;
    }

    /**
     * @return int
     * @throws AppException
     */
    private function getMemberId()
    {
        $member_id = \YunShop::app()->getMemberId();
        if (!$member_id) {
            throw new AppException('Please login in.');
        }
        return $member_id;
    }

}
