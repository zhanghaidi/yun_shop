<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2018/11/7
 * Time: 3:28 PM
 */

namespace Yunshop\Love\Common\Services;


use app\common\exceptions\ShopException;
use app\common\facades\Setting;
use app\common\services\finance\BalanceChange;
use Illuminate\Support\Facades\DB;
use Yunshop\Love\Common\Models\LoveTradingModel;

class LoveRecycleService
{
    /**
     * @var array
     */
    private $trading_set;

    /**
     * @var LoveTradingModel
     */
    private $tradingModel;

    public function __construct()
    {
        $this->trading_set = Setting::get('plugin.love_trading');
    }

    /**
     * 爱心值回购接口
     *
     * @param $tradingModel
     * @return bool
     */
    public function recycle($tradingModel)
    {
        $this->tradingModel = $tradingModel;


        DB::transaction(function () {
            $this->_recycle();
        });
        return true;
    }


    /**
     * @return bool
     * @throws ShopException
     */
    private function _recycle()
    {
        $result = $this->updateRecycleModel();
        if (!$result) {
            throw new ShopException("公司回购错误：修改交易数据错误ID{$this->tradingModel->id}");
        }
        $result = $this->updateMemberBalance();
        if (!$result) {
            throw new ShopException("公司回购错误：更新会员余额错误ID{$this->tradingModel->id}");
        }
        return true;
    }

    /**
     * 修改交易记录状态
     *
     * @return bool
     */
    private function updateRecycleModel()
    {
        $this->tradingModel->type = 1;
        $this->tradingModel->status = 1;

        return $this->tradingModel->save();
    }

    /**
     * 修改会员余额
     *
     * @return bool
     */
    private function updateMemberBalance()
    {
        $data = $this->getBalanceChangeData();
        $result = (new BalanceChange())->award($data);
        if ($result === true) {
            return true;
        }
        return false;
    }

    /**
     * 调用余额变动接口数据
     *
     * @return array
     */
    private function getBalanceChangeData()
    {
        $amount = $this->getRecycleMoney();

        return [
            'member_id'     => $this->tradingModel->member_id,
            'change_value'  => $amount,
            'operator'      => ConstService::OPERATOR_SHOP,
            'operator_id'   => $this->tradingModel->id,
            'remark'        => '出售' . LOVE_NAME . '-公司回购 获得' . $amount,
            'relation'      => '',
        ];
    }

    /**
     * 扣除手续费后实际交易余额值
     *
     * @return float
     */
    private function getRecycleMoney()
    {
        $amount = $this->tradingAmount();

        $poundage = bcdiv(bcmul($amount, $this->tradingModel->poundage, 2), 100, 2);

        return bcsub($amount, $poundage, 2);
    }

    /**
     * 爱心值转换余额后总金额
     *
     * @return float
     */
    private function tradingAmount()
    {
        $trading_rate = $this->tradingRate();

        return bcmul($this->tradingModel->amount, $trading_rate, 2);
    }

    /**
     * 爱心值转换余额比例
     *
     * @return float
     */
    private function tradingRate()
    {
        $rate = $this->trading_set['trading_money'];

        return $rate ? $rate : 1;
    }



}
