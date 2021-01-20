<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/22
 * Time: 15:21
 */

namespace Yunshop\GroupPurchase\models;

use Yunshop\GroupPurchase\models\GroupPurchase;

class SaveGoods extends \app\backend\modules\goods\models\Goods
{

    public static function getGoodsData()
    {
        return [
            'uniacid'       => \YunShop::app()->uniacid,
            'type'          => 1,
            'status'        => 1,
            'display_order' => 0,
            'title'         => '拼团', //'门店名称',
            'thumb'         => 'http://test-1251768088.cosgz.myqcloud.com/images/3/2018/03/twe2V72PAepzd9eeD92W6SS9aAd8sE.jpg', //'门店图片',
            'sku'           => '个',
            'market_price'  => 1,
            'price'         => 1,
            'cost_price'    => 1,
            'stock'         => '9999999',
            'weight'        => 0,
            'is_plugin'     => 0,
            'brand_id'      => 0,
            'plugin_id'     => 60
        ];
    }

    public static function saveGoods($widgets_data, $goods_model = '')
    {
        $goods_id = $goods_model->id;
        if (!$goods_id) {
            //创建新的goods
            $goods_model = new self();
        }
        //为新的goods插入虚拟商品数据
        $goods_model->fill(self::getGoodsData());
        //更新插件设置信息
        $goods_model->widgets = (new GroupPurchase())->getDeductWidgets($widgets_data);
        $goods_model->save();
        if ($goods_id) {
            return Goods::find($goods_id);
        }
        return $goods_model;
    }

    public static function getWidgets()
    {
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
            ]
        ];
    }
}