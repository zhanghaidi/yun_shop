<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/20
 * Time: 14:26
 */

namespace Yunshop\SuperKjs\models;

use app\common\models\BaseModel;
use app\backend\modules\goods\services\CreateGoodsService;

class BasisSetting extends BaseModel
{
    public $table = 'yz_super_kjs_setting';
    public $fillable = [];

    public function saveSetting($setting_data,$goods_id)
    {
        $setting_model = (new self)->getSettingDate($setting_data,$goods_id);
        $setting_model->widgets = self::getDeductWidgets($setting_data);
        if (!self::uniacid()->first()) {
            $setting_status = self::insert($setting_model);
            return $setting_status;
        }
        $setting_status = self::uniacid()->where('goods_id',$goods_id)->update($setting_model);
        if (!$setting_status) {
            return $setting_status;
        }
        return $setting_model;
    }

    /**
     * 获取商品ID
     * @return mixed
     */
    public function getGoodsId()
    {
        return self::uniacid()->value('goods_id');
    }

    /**
     * 获取设置数据
     * @return mixed
     */
    public function getSettingData()
    {
        return self::first();
    }

    /**
     * 获取插件数据
     * @return array|mixed|\stdClass
     */
    public function getProfitData()
    {
        return json_decode(self::value('profit'),true);
    }

    /**
     * 获取会员积分设置数据
     */
    public function getMemberPoint()
    {
        return json_decode(self::value('member_award_point'), true);
    }

    /**
     * 获取满额返现数据
     * @return array|mixed|\stdClass
     */
    public function getFullReturn()
    {
        return json_decode(self::value('profit'), true);
    }

    /**
     * 设置数据转换json
     * @param $setting_data
     * @param $goods_id
     * @return array
     */
    public function getSettingDate($setting_data,$goods_id)
    {
        $set_data = [
            'is_open' => 0,
            'plugins' => json_encode([
                'love' => [
                    'award' => trim($setting_data['love']['award'])?trim($setting_data['love']['award']):0,
                    'deduction_proportion' => $setting_data['love']['award_proportion'],
                    'parent_award_proportion' => $setting_data['love']['parent_award_proportion'],
                    'second_award_proportion' => $setting_data['love']['second_award_proportion'],
                ]
            ]),
            'member_award_point' => json_encode([
                'member_award' => $setting_data['member']['award'],
                'award_point' => $setting_data['member']['award_point'],
                'award_point_1' => $setting_data['member']['award_point_1'],
                'award_point_2' => $setting_data['member']['award_point_2'],
            ]),
            'profit' => json_encode([
                'commission' => [
                    'is_commission' => $setting_data['commission']['is_commission'],
                    'has_commission' => $setting_data['commission']['has_commission'],
                    'level' => 3,
                    'first_level' => 0,
                    'second_level' => 0,
                    'third_level' => 0,
                    'rule' => [$setting_data['commission']['rule']]
                ],
                'team_dividend' => [
                    'is_dividend' => $setting_data['team_dividend']['is_dividend'],
                    'has_dividend' => $setting_data['team_dividend']['has_dividend'],
                    'has_dividend_rate' => $setting_data['team_dividend']['has_dividend_rate']
                ],
                'area_dividend' => [
                    'is_dividend' => $setting_data['area-dividend']['is_dividend'],
                    'has_dividend' => $setting_data['area-dividend']['has_dividend'],
                    'has_dividend_rate' => $setting_data['area-dividend']['has_dividend_rate']
                ],
                'single_return' => [
                    'is_single_return' => $setting_data['single-return']['is_single_return'],
                    'return_rate' => $setting_data['single-return']['return_rate']
                ],
                'full_return' => [
                    'is_open' => $setting_data['full-return']['is_open']
                ]
            ]),
            'goods_id' => $goods_id,
            'created_at' => time(),
            'uniacid' => \YunShop::app()->uniacid,
        ];
        return $set_data;
    }

    /**
     * 插件数据
     * @param $widgets
     * @return mixed
     */
    public function getDeductWidgets($widgets)
    {
        //积分
        $point = trim($widgets['sale']['point']);
        if (!$point) {
            $point = 0;
        }
        $widgets['sale']['point'] = $point . '%';

        $widgets['sale']['award_balance'] = $widgets['sale']['award_balance']?:0;
        // 最大抵扣
        $max_point_deduct = trim($widgets['sale']['max_point_deduct']);
        if (!$max_point_deduct) {
            $max_point_deduct = 0;
        }
        $widgets['sale']['max_point_deduct'] = $max_point_deduct . '%';
        // 最少抵扣
        $min_point_deduct = trim($widgets['sale']['min_point_deduct']);
        if (!$min_point_deduct) {
            $min_point_deduct = '';
        }
        $widgets['sale']['min_point_deduct'] = $min_point_deduct . '%';
        //爱心值
        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {
            $widgets['love']['deduction_proportion'] = trim($widgets['love']['deduction_proportion']) ? trim($widgets['love']['deduction_proportion']) : 0;
            $widgets['love']['award_proportion'] = trim($widgets['love']['award_proportion']) ? trim($widgets['love']['award_proportion']) : 0;
        }
        //团队分红
        $exist_team_dividend = app('plugins')->isEnabled('team-dividend');
        if ($exist_team_dividend) {
            $widgets['team_dividend']['has_dividend_rate'] = trim($widgets['team_dividend']['has_dividend_rate']) ? trim($widgets['team_dividend']['has_dividend_rate']) : 0;
        }
        //区域分红
        $exist_area_dividend = app('plugins')->isEnabled('area-dividend');
        if ($exist_area_dividend) {
            $widgets['area_dividend']['has_dividend_rate'] = trim($widgets['area_dividend']['has_dividend_rate']) ? trim($widgets['area_dividend']['has_dividend_rate']) : 0;
        }
        //招商分红
        $exist_merchant = app('plugins')->isEnabled('merchant');
        if ($exist_merchant) {
            $widgets['merchant']['staff_bonus'] = trim($widgets['merchant']['staff_bonus']) ? trim($widgets['merchant']['staff_bonus']) : 0;
            $widgets['merchant']['is_open_bonus_staff'] = 1;
            $widgets['merchant']['is_open_bonus_center'] = 1;
        }
        //消费返现
        $exist_single_return = app('plugins')->isEnabled('single-return');
        if ($exist_single_return) {
            $widgets['single_return']['return_rate'] = trim($widgets['single_return']['return_rate']) ? trim($widgets['single_return']['return_rate']) : 0;
        }
        return $widgets;
    }
}