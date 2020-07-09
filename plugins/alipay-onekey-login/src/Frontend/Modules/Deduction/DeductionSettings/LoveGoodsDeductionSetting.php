<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/12
 * Time: 上午10:45
 */

namespace Yunshop\Love\Frontend\Modules\Deduction\DeductionSettings;

use app\common\models\Goods;
use app\frontend\modules\deduction\DeductionSettingInterface;
use Yunshop\Love\Frontend\Models\GoodsLove;

class LoveGoodsDeductionSetting implements DeductionSettingInterface
{
    protected $setting;

    public function getWeight()
    {
        return 10;
    }

    function __construct(Goods $goods)
    {
        $this->setting = GoodsLove::where('goods_id', $goods->id)->first();
    }

    public function isEnableDeductDispatchPrice()
    {
        return \Setting::get('love.deduction_freight');
    }

    public function getMaxPriceProportion()
    {
        if ($this->setting->deduction_proportion > 0) {
            return $this->setting->deduction_proportion;
        }
        return false;
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
        if ($this->setting->deduction_proportion_low > 0) {
            return $this->setting->deduction_proportion_low;
        }
        return false;
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