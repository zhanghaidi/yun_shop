<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/2
 * Time: 下午2:11
 */

namespace Yunshop\Mryt\store\models;

class Goods extends \app\backend\modules\goods\models\Goods
{
    public function hasOneCashierGoods()
    {
        return $this->hasOne(CashierGoods::class, 'goods_id', 'id');
    }

    public static function getGoodsByName($keyword)
    {
        return static::uniacid()->select('id', 'title', 'thumb', 'market_price', 'price', 'real_sales', 'sku', 'plugin_id', 'stock')
            ->where('title', 'like', '%' . $keyword . '%')
            ->where('status', 1)
            //->where('is_plugin', 0)
            ->where('plugin_id', 0)
            ->get();
    }

    public function atributeNames()
    {

    }

    public function rules()
    {

    }

    public static function getGoodsData($store_data)
    {
        return [
            'uniacid'       => \YunShop::app()->uniacid,
            'type'          => $store_data['type']?:1,
            'status'        => 1,
            'display_order' => 0,
            'title'         => $store_data['store_name'], //'门店名称',
            'thumb'         => $store_data['thumb'], //'门店图片',
            'sku'           => '个',
            'market_price'  => 1,
            'price'         => 1,
            'cost_price'    => 1,
            'stock'         => '9999999',
            'weight'        => 0,
            'is_plugin'     => 0,
            'brand_id'      => 0,
            'plugin_id'     => 31
        ];
    }

    public static function saveGoods($store_data, $widgets_data, $goods_model = '')
    {

        $goods_id = $goods_model->id;
        if (!$goods_model) {
            $goods_model = new self();
        }
        $goods_model->fill(self::getGoodsData($store_data));
        $goods_model->widgets = (new CashierGoods())->getDeductWidgets($widgets_data);
        $goods_model->save();
        if ($goods_id) {
            return Goods::find($goods_id);
        }
        return $goods_model;
    }

    public static function getWidgets()
    {
        $widgets_data = \Setting::get('plugin.store_widgets');
        if(!empty($widgets_data)){
            return $widgets_data;
        }
        return [
            'cashier' => [
                'is_open' => 0,
                'is_write_information' => 0,
                'shop_commission' => 0,
                'settlement_day' => 0,
                'is_cash_pay' => 0,
                'shop_award_point' => 0,
                'profit' => [
                    'full-return' => [
                        'is_open' => 0
                    ]
                ],
                'plugins' => [
                    'love' => [
                        'award_shop' => 0
                    ]
                ]
            ],
            'sale' => [
                'max_point_deduct' => 0,
                'min_point_deduct' => 0,
                'point' => 0
            ],
            'discount' => [
                'discount_method' => 1,
                'discount_value' => []
            ],
            'team_dividend' => [
                'is_dividend' => 1,
                'has_dividend' => 1,
                'has_dividend_rate' => 0
            ],
            'area_dividend' => [
                'is_dividend' => 1,
                'has_dividend' => 1,
                'has_dividend_rate' => 0
            ],
            'single_return' => [
                'is_single_return' => 1,
                'return_rate' => 0
            ],
            'love' => [
                'deduction' => 1,
                'deduction_proportion' => 0,
                'award' => 1,
                'award_proportion' => 0
            ],
            'merchant' => [
                'is_open_bonus_staff' => 1,
                'is_open_bonus_center' => 1,
                'staff_bonus' => 0
            ],
            'commission' => [
                'is_commission' => 1,
                'show_commission_button' => 1,
                'has_commission' => 1,
                'rule' => [
                    'level_0' => [
                        'first_level_rate' => 0,
                        'second_level_rate' => 0,
                        'third_level_rate' => 0,
                    ]
                ]
            ],
            "store_asset" =>  [
                "asset_id" => "0",
                "consumption_rate" => "0",
                "store_id"        => "0"
            ]
        ];
    }
}