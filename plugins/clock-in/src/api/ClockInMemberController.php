<?php

namespace Yunshop\ClockIn\api;

use app\common\components\ApiController;
use app\common\facades\Setting;
use Yunshop\ClockIn\models\ClockContinuityModel;
use Yunshop\ClockIn\models\ClockPayLogModel;
use Yunshop\ClockIn\models\ClockRewardLogModel;
use Yunshop\ClockIn\services\ClockInService;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/16
 * Time: 下午2:29
 */
class ClockInMemberController extends ApiController
{
    public $_set;
    protected $pageSize = 20;

    public function preAction()
    {
        parent::preAction();

        $this->_set = Setting::get('plugin.clock_in');
    }

    public function statistic()
    {
        // plugin.clock-in.api.clock-in-member.statistic
        $data = [
            'pay_amount' => $this->getPayAmount(),
            'reward_amount' => $this->getRewardAmount(),
            'colck_num' => $this->getclockNum(),
        ];

        if ($data) {
            return $this->successJson('成功', $data);
        }
        return $this->errorJson('未检测到数据!', $data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClockList()
    {
        //plugin.clock-in.api.clock-in-member.get-clock-list
        $memberId = \YunShop::app()->getMemberId();

        $model = ClockPayLogModel::getPayLogByMemberId($memberId)
            ->where('pay_status', 1)
            ->select('id', 'clock_in_status', 'created_at', 'queue_id')
            ->orderBy('id', 'desc');

        $request = $model->paginate($this->pageSize)
            ->toArray();
        if ($request) {
            return $this->successJson('成功', $request);
        }
        return $this->errorJson('未检测到数据!', $request);
    }

    /**
     * @return mixed
     */
    public function getPayAmount()
    {
        $memberId = \YunShop::app()->getMemberId();
        $pay_amount = ClockPayLogModel::getPayLogByMemberId($memberId)->where('pay_status', 1)->sum('amount');
        return $pay_amount;
    }

    /**
     * @return mixed
     */
    public function getRewardAmount()
    {
        $memberId = \YunShop::app()->getMemberId();
        $reward_amount = ClockRewardLogModel::getRewardByMemberId($memberId)->sum('amount');
        return $reward_amount;
    }

    /**
     * @return mixed
     */
    public function getclockNum()
    {
        $memberId = \YunShop::app()->getMemberId();
        $colck_num = ClockPayLogModel::getPayLogByMemberId($memberId)->where('clock_in_status', 1)->count('id');
        return $colck_num;
    }


}