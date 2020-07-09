<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/24
 * Time: 4:23 PM
 */

namespace Yunshop\Love\Backend\Modules\Love\Controllers;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use Illuminate\Support\Facades\DB;
use Yunshop\Love\Backend\Modules\Love\Models\TimingLogModel;
use Yunshop\Love\Common\Models\Member;
use Yunshop\Love\Common\Services\CommonService;

class TimingRechargeController extends BaseController
{
    /**
     * @var Member
     */
    private $memberModel;


    private $timingLogModel;


    public function preAction()
    {
        parent::preAction();
        $this->memberModel = $this->getMemberModel();
    }


    public function index()
    {
        $value = $this->value();
        if ($value) {
            return $this->timingRecharge();
        }

        return view('Yunshop\Love::Backend.Love.timingRecharge', $this->resultData());
    }

    /**
     * @return array
     */
    private function resultData()
    {
        return [
            'rule_num' => 0,
            'memberInfo' => $this->memberModel,
            'rechargeMenu' => $this->rechargeMenu(),
        ];
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     * @throws ShopException
     */
    private function timingRecharge()
    {
        DB::beginTransaction();
        $result = $this->tryTimingRecharge();

        if ($result === true) {
            DB::commit();
            return $this->message('充值成功', $this->successUrl());
        }
        DB::rollBack();
        return view('Yunshop\Love::Backend.Love.timingRecharge', $this->resultData());
    }

    /**
     * @return string
     */
    private function successUrl()
    {
        return Url::absoluteWeb('plugin.love.Backend.Modules.Love.Controllers.timing-recharge.index', ['member_id' => $this->memberModel->uid]);
    }

    /**
     * @return bool|\Laracasts\Flash\FlashNotifier
     * @throws ShopException
     */
    private function tryTimingRecharge()
    {
        $this->timingLogModel = new TimingLogModel();

        $this->timingLogModel->timing_rule = $this->getTimingRule();
        if (!$this->validateTimingRule()) {
            return $this->error('定时充比例总和不能大于100%！');
        }

        $this->timingLogModel->fill($this->timingLogData());
        $validator = $this->timingLogModel->validator();
        if ($validator->fails()) {
            return $this->error($validator->messages()->first());
        }

        return $this->timingLogModel->save();
    }

    /**
     * @return array
     * @throws ShopException
     */
    private function timingLogData()
    {
        return [
            'uniacid' => \YunShop::app()->uniacid,
            'recharge_sn' => TimingLogModel::createOrderSn('RTL', 'recharge_sn'),
            'member_id' => $this->memberId(),
            'amount' => $this->value(),
            'total' => count($this->timingLogModel->timing_rule),
        ];
    }

    /**
     * 验证定时充值规则
     *
     * @return bool
     */
    public function validateTimingRule()
    {
        $count = 0;
        foreach ($this->getTimingRule() as $timingRule) {
            $count += $timingRule['timing_rate'];
        }
        return $count <= 100 ? true : false;
    }

    /**
     * 获得定时充值规则
     *
     * @return array
     */
    public function getTimingRule()
    {
        $timing = \YunShop::request()->timing;
        foreach ($timing as $key => $item) {
            if ($item['timing_days'] != '' && !$item['timing_rate']) {
                unset($item[$key]);
            }
        }
        return $timing;
    }

    /**
     * @return Member
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
     * @return double
     */
    private function value()
    {
        return trim(\YunShop::request()->change_value);
    }

    /**
     * @return array
     */
    private function rechargeMenu()
    {
        $love = CommonService::getLoveName();
        return array(
            'title' => $love . '充值',
            'name' => '粉丝',
            'profile' => '会员信息',
            'old_value' => '当前可用',
            'change_value' => '充值' . $love,
            'type' => 'love'
        );
    }
}
