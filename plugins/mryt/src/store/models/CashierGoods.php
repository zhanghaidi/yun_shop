<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/2
 * Time: 下午2:33
 */

namespace Yunshop\Mryt\store\models;


use app\common\models\BaseModel;

class CashierGoods extends BaseModel
{
    public $table = 'yz_cashier_goods';
    public $timestamps = true;
    protected $guarded = [''];
    static protected $needLog = true;
    protected $casts = [
        'payment_types' => 'json'
    ];

    public static function getCashierGoods($goods_id)
    {
        return self::select()->byGoodsId($goods_id);
    }

    public function scopeByGoodsId($query, $goods_id)
    {
        return $query->where('goods_id', $goods_id);
    }

    public $attributes = [
        'is_open' => 0,
        'shop_commission' => 0,
        'settlement_day' => 0,
        'is_write_information' => 0,
        'plugins' => [
            'love' => [
                'deduction_proportion' => 0,
                "award_proportion" => 0,
                "award_shop" => 0
            ]
        ],
        'shop_award_point' => 0,
        'profit' => [
            'commission' => [
                'amount' => 0
            ],
            'team-dividend' => [
                'amount' => 0
            ],
            'area-dividend' => [
                'amount' => 0
            ],
            'merchant-staff' => [
                'amount' => 0
            ],
            'merchant-center' => [
                'amount' => 0
            ],
            'single-return' => [
                'amount' => 0
            ],
            'full-return' => [
                'is_open' => 0
            ]
        ]
    ];

    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }
        $saleModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $saleModel->delete();
        }

        foreach ($data['coupon_ids'] as $key_id => $id) {
            $data['coupon'][] = [
                'id' => $id,
                'name' => $data['coupon_names'][$key_id]
            ];
        }
        $data['goods_id'] = $goodsId;
        $data['plugins'] = serialize($data['plugins']);
        $data['profit'] = serialize($data['profit']);
        $data['coupon'] = serialize($data['coupon']);
        $data['shop_award_point'] = trim($data['shop_award_point']) ? trim($data['shop_award_point']) : 0;
        $data['shop_commission'] = trim($data['shop_commission']) ? trim($data['shop_commission']) : 0;
        $data['settlement_day'] = trim($data['settlement_day']) ? trim($data['settlement_day']) : 0;

        $data['payment_types']['cashPay'] = $data['is_cash_pay'];

        unset($data['coupon_ids']);
        unset($data['coupon_names']);
        $saleModel->fill($data);

        return $saleModel->save();
    }

    public static function getModel($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = static::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model = new static;

        return $model;
    }

    public function getDeductWidgets($widgets)
    {
        $point = trim($widgets['sale']['point']);
        if (!$point) {
            $point = 0;
        }
        $widgets['sale']['point'] = $point . '%';

        $widgets['sale']['award_balance'] = $widgets['sale']['award_balance']?:0;

        $max_point_deduct = trim($widgets['sale']['max_point_deduct']);
        if (!$max_point_deduct) {
            $max_point_deduct = 0;
        }
        $widgets['sale']['max_point_deduct'] = $max_point_deduct . '%';

        $min_point_deduct = trim($widgets['sale']['min_point_deduct']);
        if (!$min_point_deduct) {
            $min_point_deduct = 0;
        }
        $widgets['sale']['min_point_deduct'] = $min_point_deduct . '%';

        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {
            $widgets['love']['deduction_proportion'] = trim($widgets['love']['deduction_proportion']) ? trim($widgets['love']['deduction_proportion']) : 0;
            $widgets['love']['award_proportion'] = trim($widgets['love']['award_proportion']) ? trim($widgets['love']['award_proportion']) : 0;
            $widgets['love']['third_award_proportion'] = trim($widgets['love']['third_award_proportion']) ? trim($widgets['love']['third_award_proportion']) : 0;
        }
        $exist_team_dividend = app('plugins')->isEnabled('team-dividend');
        if ($exist_team_dividend) {
            $widgets['team_dividend']['has_dividend_rate'] = trim($widgets['team_dividend']['has_dividend_rate']) ? trim($widgets['team_dividend']['has_dividend_rate']) : 0;
        }
        $exist_area_dividend = app('plugins')->isEnabled('area-dividend');
        if ($exist_area_dividend) {
            $widgets['area_dividend']['has_dividend_rate'] = trim($widgets['area_dividend']['has_dividend_rate']) ? trim($widgets['area_dividend']['has_dividend_rate']) : 0;
        }
        $exist_merchant = app('plugins')->isEnabled('merchant');
        if ($exist_merchant) {
            $widgets['merchant']['staff_bonus'] = trim($widgets['merchant']['staff_bonus']) ? trim($widgets['merchant']['staff_bonus']) : 0;
        }
        $exist_single_return = app('plugins')->isEnabled('single-return');
        if ($exist_single_return) {
            $widgets['single_return']['return_rate'] = trim($widgets['single_return']['return_rate']) ? trim($widgets['single_return']['return_rate']) : 0;
        }
        $exist_asset_return = app('plugins')->isEnabled('asset');
        if ($exist_asset_return) {
            $widgets['store_asset']['asset_id'] = trim($widgets['store_asset']['asset_id']) ? trim($widgets['store_asset']['asset_id']) : 0;
            $widgets['store_asset']['consumption_rate'] = trim($widgets['store_asset']['consumption_rate']) ? trim($widgets['store_asset']['consumption_rate']) : 0;
            $widgets['store_asset']['cashier_id'] =  0;
        }
        return $widgets;
    }
}