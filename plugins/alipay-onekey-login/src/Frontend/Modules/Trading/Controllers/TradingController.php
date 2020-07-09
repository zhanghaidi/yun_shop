<?php
namespace Yunshop\Love\Frontend\Modules\Trading\Controllers;

use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\services\finance\BalanceChange;
use Yunshop\Love\Common\Services\CommonService;
use Yunshop\Love\Common\Services\ConstService;
use Yunshop\Love\Common\Services\LoveChangeService;
use Yunshop\Love\Frontend\Modules\Trading\Models\LoveTradingModel;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/26
 * Time: 上午9:43
 */
class TradingController extends BaseController
{
    public $transactionActions = ['revoke', 'purchase'];

    /**
     * 交易列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function tradingCenter()
    {
        //plugin.love.Frontend.Modules.Trading.Controllers.trading.trading-center
        $status = \YunShop::request()->get('status');
        $own = \YunShop::request()->get('own');
        $tradingData = LoveTradingModel::getLoveTradings($status, $own)->orderBy('id','desc')->get();


        if ($tradingData) {
            foreach ($tradingData as &$item){
                $item->own = 0;
                if($item->member_id == \YunShop::app()->getMemberId()){
                    $item->own = 1;
                }
            }
            return $this->successJson('成功', $tradingData);
        }
        return $this->errorJson('未检测到数据!', $tradingData);
    }

    /**
     * 获取云币数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSellLove()
    {
        //plugin.love.Frontend.Modules.Trading.Controllers.trading.get-sell-love
        $set = Setting::get('plugin.love_trading');
        $usable = CommonService::getLoveMemberModelById(\YunShop::app()->getMemberId());
        if (!$usable->love){
            $usable->love = [
                'uniacid' => \YunShop::app()->uniacid,
                'member_id' => \YunShop::app()->getMemberId(),
                'usable' => "0.00",
                'froze' => "0.00",
                'created_at' => "",
                'updated_at' => "",
                'activation_at' => 0
            ];
        }
        $data = [
            'love' => $usable->love,
            'set' => $set,
        ];
        return $this->successJson('获取数据成功!', $data);
    }

    /**
     * 保存 出售云币
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSellLove()
    {
        // plugin.love.Frontend.Modules.Trading.Controllers.trading.save-sell-love&amount
        $amount = \YunShop::request()->amount;
        if (!$amount) {
            return $this->errorJson('未检测到数据!');
        }

        $set = Setting::get('plugin.love_trading');
        $loveName = \Yunshop\Love\Common\Services\SetService::getLoveName();
        $usable = CommonService::getLoveMemberModelById(\YunShop::app()->getMemberId());
        $memberId = \YunShop::app()->getMemberId();

        if ($amount < $set['trading_limit']) {
            return $this->errorJson('出售的' . $loveName . '不正确!');
        }
        if ($amount % $set['trading_fold'] != 0) {
            return $this->errorJson('出售的' . $loveName . '不正确!');
        }
        if ($amount > $usable->love->usable) {
            return $this->errorJson('出售的' . $loveName . '不正确!');
        }

        $loveData = [
            'member_id' => $memberId,
            'change_value' => $amount,
            'operator' => ConstService::OPERATOR_ORDER,
            'operator_id' => '',
            'remark' => '出售' . $loveName . $amount,
            'relation' => ''
        ];
        (new LoveChangeService())->sellAward($loveData);

        $tradingData = [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $memberId,
            'buy_id' => 0,
            'status' => 0,
            'type' => 0,
            'amount' => $amount,
            'poundage' => $set['poundage'] ? $set['poundage'] : 0,
            'created_at' => time()
        ];
        $result = LoveTradingModel::insert($tradingData);

        if ($result) {
            return $this->successJson('出售成功!');
        }
    }

    /**
     * 交易撤回
     * @return \Illuminate\Http\JsonResponse
     */
    public function revoke()
    {
        //plugin.love.Frontend.Modules.Trading.Controllers.trading.revoke&id=3
        $tradingId = \YunShop::request()->get('id');
        $tradingData = LoveTradingModel::find($tradingId);

        if (!$tradingData || $tradingData->status != 0) {
            return $this->errorJson('撤回失败!未检测到数据或以交易!', []);
        }

        (new LoveChangeService())->revokeAward($this->getRecordData($tradingData->amount, $tradingData->member_id, $tradingId, '交易撤回'));

        $result = LoveTradingModel::where('id', $tradingId)->delete();
        if ($result) {
            return $this->successJson('撤回成功!');
        }
    }

    /**
     * 收购云币
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchase()
    {
        //plugin.love.Frontend.Modules.Trading.Controllers.trading.purchase&id=3
        $set = Setting::get('plugin.love_trading');
        $loveName = \Yunshop\Love\Common\Services\SetService::getLoveName();

        $memberId = \YunShop::app()->getMemberId();
        $tradingId = \YunShop::request()->get('id');
        $tradingData = LoveTradingModel::find($tradingId);

        if (!$tradingData || $tradingData->status != 0) {
            return $this->errorJson('收购失败!未检测到数据或以交易!', []);
        }
        $amount = $tradingData->amount * $set['trading_money'];
        $sellAmount = $amount - $amount / 100 * $tradingData->poundage;
        //消费购买
        (new BalanceChange())->consume($this->getRecord($amount, $memberId, $tradingId, '购买' . $loveName));

        // 出售人增加余额
        (new BalanceChange())->award($this->getRecord($sellAmount, $tradingData->member_id, $tradingId, '出售' . $loveName));

        // 增加爱心值
        (new LoveChangeService())->purchaseAward($this->getRecordData($tradingData->amount, $memberId, $tradingId, '收购获得'));
        //修改交易记录
        $data = ['buy_id' => $memberId, 'status' => 1];
        $result = LoveTradingModel::where('id', $tradingId)->update($data);
        if ($result) {
            return $this->successJson('收购成功!');
        }
    }

    /**
     * @param $changeValue
     * @param $memberId
     * @param $tradingId
     * @param $text
     * @return array
     */
    private function getRecord($changeValue, $memberId, $tradingId, $text)
    {
        return [
            'member_id' => $memberId,
            'change_value' => $changeValue,
            'operator' => ConstService::OPERATOR_MEMBER,
            'operator_id' => $tradingId,
            'remark' => $text . $changeValue,
            'relation' => '',
        ];
    }

    /**
     * @param $changeValue
     * @param $memberId
     * @param $tradingId
     * @param $text
     * @return array
     */
    private function getRecordData($changeValue, $memberId, $tradingId, $text)
    {
        return [
            'member_id' => $memberId,
            'change_value' => $changeValue,
            'operator' => ConstService::OPERATOR_ORDER,
            'operator_id' => $tradingId,
            'remark' => $text . $changeValue,
            'relation' => ''
        ];
    }

}