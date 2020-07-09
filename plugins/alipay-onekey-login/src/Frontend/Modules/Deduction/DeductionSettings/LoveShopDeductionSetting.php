<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/12
 * Time: 上午10:45
 */

namespace Yunshop\Love\Frontend\Modules\Deduction\DeductionSettings;

use app\frontend\modules\deduction\DeductionSettingInterface;

class LoveShopDeductionSetting implements DeductionSettingInterface
{
    public function getWeight(){
        return 30;
    }
    public function isEnableDeductDispatchPrice()
    {
        return \Setting::get('love.deduction_freight');
    }

    public function getMaxPriceProportion()
    {
        return \Setting::get('love.deduction_proportion');
    }

    public function getMaxFixedAmount()
    {
        return false;
    }

    public function getMaxDeductionType()
    {
        return 'GoodsPriceProportion';
    }

    public function getMinPriceProportion()
    {
        return \Setting::get('love.deduction_proportion_low');
    }

    public function getMinFixedAmount()
    {
        return false;
    }

    public function getMinDeductionType()
    {
        return 'GoodsPriceProportion';
    }

    public function isMaxDisable()
    {
        return !\Setting::get('love.deduction');
    }

    public function isMinDisable()
    {
        return !\Setting::get('love.deduction');
    }

    public function isDispatchDisable()
    {
        return !\Setting::get('love.deduction');
    }
}