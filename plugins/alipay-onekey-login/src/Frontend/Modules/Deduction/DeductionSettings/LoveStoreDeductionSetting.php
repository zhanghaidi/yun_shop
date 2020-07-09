<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/22
 * Time: 下午1:46
 */

namespace Yunshop\Love\Frontend\Modules\Deduction\DeductionSettings;

use app\common\models\Goods;
use app\frontend\modules\deduction\DeductionSettingInterface;

class LoveStoreDeductionSetting implements DeductionSettingInterface
{
    public $setting = null;

    public function getWeight()
    {
        return 20;
    }

    function __construct(Goods $goods)
    {
        if (app('plugins')->isEnabled('store-cashier')) {
            $store = \Yunshop\StoreCashier\store\models\StoreGoods::where('goods_id', $goods->id)->first();

            if (!is_null($store)) {
                $this->setting = \Yunshop\StoreCashier\common\models\StoreSetting::getStoreSettingByStoreIdAndByKey($store->store_id, 'love')->first();
            }
        }
    }

    public function isEnableDeductDispatchPrice()
    {
        return \Setting::get('love.deduction_freight');
    }

    public function getMaxPriceProportion()
    {
        if (!is_null($this->setting) && $this->setting->value['deduction_proportion'] > 0) {
            return $this->setting->value['deduction_proportion'];
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
        if (!is_null($this->setting) && $this->setting->value['deduction_proportion_low'] > 0) {
            return $this->setting->value['deduction_proportion_low'];
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