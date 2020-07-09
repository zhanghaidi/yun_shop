<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/22 下午4:23
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Modules\Love\Controllers;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use Yunshop\Love\Backend\Modules\Love\Models\LoveRechargeRecords;
use Yunshop\Love\Common\Models\Member;

class RechargeController extends BaseController
{
    /**
     * @var Member
     */
    private $memberModel;

    /**
     * @var LoveRechargeRecords
     */
    private $rechargeModel;


    public function preAction()
    {
        parent::preAction();
        $this->memberModel = $this->getMemberModel();
    }


    public function index()
    {
        $value = $this->value();
        if ($value) {
            return $this->recharge();
        }

        return view('Yunshop\Love::Backend.Love.recharge', $this->resultData());
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function recharge()
    {
        $result = $this->tryRecharge();

        if ($result === true && $this->rechargeModel->status == 1) {
            return $this->message('充值成功', $this->successUrl());
        }
        return view('Yunshop\Love::Backend.Love.recharge', $this->resultData());
    }

    /**
     * @return bool|\Laracasts\Flash\FlashNotifier
     */
    private function tryRecharge()
    {
        $this->rechargeModel = new LoveRechargeRecords();

        $this->rechargeModel->fill($this->getRechargeData());
        $validator = $this->rechargeModel->validator();
        if ($validator->fails()) {
            return $this->error($validator->messages()->first());
        }
        return $this->rechargeModel->save();
    }

    /**
     * @return array
     */
    private function getRechargeData()
    {
        return [
            'type'          => 0,       //todo 后台充值、商城付款，应该在支付模型中设置常量
            'status'        => LoveRechargeRecords::STATUS_ERROR,
            'remark'        => $this->remark(),
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => $this->memberModel->uid,
            'order_sn'      => LoveRechargeRecords::createOrderSn('RL','order_sn'),
            'value_type'    => $this->valueType(),
            'change_value'  => $this->value(),
            'money'         => '0',
        ];
    }

    private function successUrl()
    {
        return Url::absoluteWeb('plugin.love.Backend.Modules.Love.Controllers.recharge.index',array('member_id' => $this->memberModel->uid));
    }

    /**
     * @return array
     */
    private function resultData()
    {
        return [
            'memberInfo'    => $this->memberModel,
            'rechargeMenu'  => $this->getRechargeMenu()
        ];
    }

    /**
     * @return mixed
     * @throws ShopException
     */
    private function getMemberModel()
    {
        $member_id = $this->memberId();

        $memberModel = Member::ofUid($member_id)->withLove()->first();
        if (!$memberModel) {
            throw new ShopException('会员不存在');
        }
        return $memberModel;
    }

    /**
     * @return int
     * @throws ShopException
     */
    private function memberId()
    {
        $member_id = (int)\YunShop::request()->member_id;
        if (!$member_id) {
            throw new ShopException('参数错误');
        }
        return $member_id;
    }

    /**
     * @return int
     */
    private function valueType()
    {
        return (int)\YunShop::request()->value_type;
    }

    /**
     * @return double
     */
    private function value()
    {
        return \YunShop::request()->love;
    }

    /**
     * @return string
     */
    private function remark()
    {
        return \YunShop::request()->remark;
    }

    /**
     * @return array
     */
    private function getRechargeMenu()
    {
        return array(
            'title' => '会员充值',
            'name' => '粉丝',
            'type' => 'point',
            'profile' => '会员信息',
            'froze_value' => '冻结值',
            'usable_value' => '可用值',
            'charge_value' => '充值数'
        );
    }
}
