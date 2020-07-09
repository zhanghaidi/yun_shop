<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/16
 * Time: 下午6:48
 */

namespace Yunshop\Love\Frontend\Modules\Deduction;

use app\common\models\Goods;
use Illuminate\Container\Container;
use Yunshop\Love\Frontend\Modules\Deduction\DeductionSettings\LoveGoodsDeductionSetting;
use Yunshop\Love\Frontend\Modules\Deduction\DeductionSettings\LoveShopDeductionSetting;
use Yunshop\Love\Frontend\Modules\Deduction\DeductionSettings\LoveStoreDeductionSetting;

class LoveDeductionSettingManager extends Container
{
    public function __construct()
    {

        /**
         * 积分抵扣商品设置
         */
        $this->bind('goods', function (LoveDeductionSettingManager $deductionSettingManager,array $params) {
            list($goods) = $params;
            return new LoveGoodsDeductionSetting($goods);
        });
        /**
         * 积分抵扣商城设置
         */
        $this->bind('shop', function (LoveDeductionSettingManager $deductionSettingManager) {
            return new LoveShopDeductionSetting();
        });
        /**
         * 积分抵扣门店设置
         */
        $this->bind('storelove', function (LoveDeductionSettingManager $deductionSettingManager, array $params) {
            list($goods) = $params;

            return new LoveStoreDeductionSetting($goods);
        });
        //$deductionSettingCollection = new DeductionSettingCollection();

    }

    /**
     * @param Goods $goods
     * @return LoveDeductionSettingCollection
     */
    public function getDeductionSettingCollection($goods)
    {

        $deductionSettingCollection = collect();
        foreach ($this->getBindings() as $key => $value) {
            $deductionSettingCollection->push($this->make($key, [$goods]));
        }
        // 按权重排序
        $deductionSettingCollection = $deductionSettingCollection->sortBy(function ($deductionSetting) {
            return $deductionSetting->getWeight();
        });

        return new LoveDeductionSettingCollection($deductionSettingCollection);
    }
}