<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/28 上午10:44
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use Yunshop\Asset\Common\Services\DigitizationService;
use Yunshop\Love\Common\Services\SetService;

class WithdrawSetController extends BaseController
{
    public function see()
    {
        return view('Yunshop\Love::Backend.withdrawSet', $this->resultData());
    }

    public function store()
    {
        $requestData = \YunShop::request()->love;
        if ($requestData) {
            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message("设置保存成功", Url::absoluteWeb('plugin.love.Backend.Controllers.withdraw-set.see'));
            }
            $this->error($result);
        }
        return $this->see();
    }

    private function resultData()
    {
        return [
            'love'                 => $this->getLoveSet(),
            'digitizationList'     => $this->digitizationList(),
            'assetPluginStatus'    => $this->assetPluginStatus(),
            'integralPluginStatus' => $this->integralPluginStatus()
        ];
    }

    /**
     * @return array
     */
    private function getLoveSet()
    {
        $love_set = SetService::getLoveSet();

        $love_withdraw_set = Setting::get('withdraw.loveWithdraw', ['poundage_type' => 0,'roll_out_limit' => '0', 'poundage_rate' => '0']);

        $love_set['withdraw_proportion'] = $love_withdraw_set['poundage_rate'];
        $love_set['poundage_type'] = $love_withdraw_set['poundage_type'];

        return $love_set;
    }

    private function digitizationList()
    {
        if ($this->assetPluginStatus()) {
            return (new DigitizationService())->allDigitizationList();
        }
        return collect();
    }

    /**
     * @return bool
     */
    private function assetPluginStatus()
    {
        return app('plugins')->isEnabled('asset') ? true : false;
    }

    /**
     * @return bool
     */
    private function integralPluginStatus()
    {
        return app('plugins')->isEnabled('integral') ? true : false;
    }

}
