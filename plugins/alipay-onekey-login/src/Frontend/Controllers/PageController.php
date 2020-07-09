<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/30 上午10:26
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Controllers;


use app\common\components\ApiController;
use app\common\events\payment\RechargeComplatedEvent;
use app\common\facades\Setting;
use Yunshop\Love\Common\Models\LoveTimingQueueModel;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Common\Services\SetService;
use Yunshop\TeamDividend\admin\models\TeamDividendAgencyModel;

class PageController extends ApiController
{
    private $memberModel;


    /**
     * 爱心值页面配置信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (!$this->getMemberModel()) {
            return $this->errorJson('未获取到会员信息，请刷新重试');
        }
        return $this->successJson('ok', $this->getResult());
    }

    /*
     * 判断用户是否是经销商
     *
     */
    public function isTeamDividend()
    {
        $flag['flag'] = true;
        //todo 经销商插件状态判断
        if (app('plugins')->isEnabled('team-dividend') && SetService::getLoveSet('transfer') == 1 && SetService::getLoveSet('team_dividend_transfer') == 1) {
            // 获取用户登录的 id
            $member_id = \YunShop::app()->getMemberId();

            // 经销商表进行查询是否存在该 id
            $is_team_dividend = TeamDividendAgencyModel::where('uid', $member_id)->first();
            if (empty($is_team_dividend)) {
                $flag['flag'] = false;
            }
            // dd($is_team_dividend);您还不是经销商，不能进行爱心转让!
        }
        return $this->successJson('', $flag);
    }

    /**
     * 获取页面信息数组
     * @return array
     */
    private function getResult()
    {
        $trading_set = Setting::get('plugin.love_trading');
        //没有设置交易设置时获取默认交易设置
        !isset($trading_set) && $trading_set = $this->getDefaultTradingSet();
        return [
            'love_name'           => $this->getLoveName(),
            'usable'              => $this->getUsableValue(),
            'froze'               => $this->getFrozeValue(),
            'transfer_status'     => $this->getTransferStatus(),
            'transfer_proportion' => $this->getTransferPoundageProportion(),
            'transfer_fetter'     => $this->getTransferFetter(),
            'transfer_multiple'   => $this->getTransferMultiple(),
            'trading_set'         => $trading_set,
            'no_recharge'         => $this->getRechargeNum(0),
            'withdraw_status'     => SetService::getLoveSet('withdraw_status') ? true : false,
            'recharge_status'     => SetService::getLoveSet('recharge') ? true : false,
            'buttons'             => $this->getPayTypeButtons()
        ];
    }

    private function getPayTypeButtons()
    {
        $event = new RechargeComplatedEvent('');
        event($event);
        $result = $event->getData();
        $type = \YunShop::request()->type;
        if ($type == 2) {
            $button = [];
            foreach ($result as $item) {
                if (($item['value'] == 1 || $item['value'] == 28) && $item['value'] != 2) {
                    $button[] = $item;
                }
            }
            return $button;
        }
        return $result;
    }


    /**
     * 获取默认交易设置数据，（兼容交易设置未保存前段报错）
     *
     * @return array
     */
    private function getDefaultTradingSet()
    {
        return [
            'trading'       => '0',
            'trading_limit' => '',
            'trading_fold'  => '',
            'poundage'      => '',
            'trading_money' => '',
            'recycl'        => '',
        ];
    }

    public function getRechargeNum($status)
    {
        $queues = LoveTimingQueueModel::uniacid()->where('status', $status)->where('member_id', $this->getMemberId())->get();
        $amount = 0;
        foreach ($queues as $queue) {
            $amount += $queue->change_value / 100 * $queue->timing_rate;
        }
        return $amount;
    }

    private function getTransferFetter()
    {
        return SetService::getTransferFetter();
    }

    private function getTransferMultiple()
    {
        return SetService::getTransferMultiple();
    }

    /**
     * 获取转让让手续费比例
     * @return bool
     */
    private function getTransferPoundageProportion()
    {
        return SetService::getTransferPoundageProportion();
    }

    /**
     * 获取转让让开关状态
     * @return bool
     */
    private function getTransferStatus()
    {
        return SetService::getTransferStatus();
    }

    /**
     * 获取会员可用爱心值
     * @return string
     */
    private function getUsableValue()
    {
        return isset($this->memberModel->love->usable) ? $this->memberModel->love->usable : "0";
    }

    /**
     * 获取会员冻结爱心值
     * @return string
     */
    private function getFrozeValue()
    {
        return isset($this->memberModel->love->froze) ? $this->memberModel->love->froze : "0";
    }

    /**
     * 获取爱心值自定义名称
     * @return mixed|string
     */
    private function getLoveName()
    {
        return CommonService::getLoveName();
    }

    /**
     * 回去登陆会员 model 实例
     * @return mixed
     */
    private function getMemberModel()
    {
        return $this->memberModel = CommonService::getLoveMemberModelById($this->getMemberId());
    }

    /**
     * 获取登陆会员ID
     * @return int
     */
    private function getMemberId()
    {
        return \YunShop::app()->getMemberId();
    }

}
