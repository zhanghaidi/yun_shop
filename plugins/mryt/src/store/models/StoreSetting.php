<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/7
 * Time: 上午11:22
 */

namespace Yunshop\Mryt\store\models;


use app\common\models\BaseModel;

class StoreSetting extends BaseModel
{
    public $table = 'yz_store_setting';
    public $timestamps = true;
    protected $guarded = [''];
    protected $casts = [
        'value' => 'json'
    ];
    protected $hidden = ['store_id'];

    public static function getStoreSettingByStoreId($store_id)
    {
        return self::select()->byStoreId($store_id);
    }

    public static function getStoreSettingByStoreIdAndByKey($store_id, $key)
    {
        return self::select()->byStoreId($store_id)->byKey($key);
    }

    public function scopeByStoreId($query, $store_id)
    {
        return $query->where('store_id', $store_id);
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    public static function setStoreSetting($store_id)
    {
        $setting_data = self::getStoreDefaultSetting();
        foreach ($setting_data as $key => $value) {
            self::create([
                'store_id'  => $store_id,
                'key'       => $key,
                'value'     => $value
            ]);
        }
    }
    private static function getStoreDefaultSetting()
    {
        $setting = \Setting::get('plugin.store_setting');
        if(empty($setting)){
            $setting = self::getDefaultSetting();
        }
        return $setting;

    }
    private static function getDefaultSetting()
    {


        return [
            'store' => [
                'shop_commission' => 0,
                'settlement_day' => 0
            ],
            'point' => [
                'set' => [
                    "money_max" => 0,
                    "give_point" => 0,
                    "shop_award_point" => 0
                ]
            ],
            'love' => [
                'deduction' => 1,
                'deduction_proportion' => 0,
                'award' => 1,
                'award_proportion' => 0,
                'award_shop' => 0
            ],
            'discount' => [
                'discount_method' => 1,
                'discount_value' => []
            ],
            'commission' => [
                'is_commission' => 1,
                'hascommission' => 1,
                'level' => 3,
                'first_level' => 0,
                'second_level' => 0,
                'third_level' => 0,
                'rule' => []
            ],
            'team-dividend' => [
                'is_dividend' => 1,
                'has_dividend' => 1,
                'has_dividend_rate' => 0
            ],
            'area-dividend' => [
                'is_dividend' => 1,
                'has_dividend' => 1,
                'has_dividend_rate' => 0
            ],
            'merchant' => [
                'is_open_bonus_staff' => 1,
                'is_open_bonus_center' => 1,
                'staff_bonus' => 0
            ],
            'single-return' => [
                'is_single_return' => 1,
                'return_rate' => 0
            ],
            'full-return' => [
                'is_open' => 0
            ]
        ];
    }
}