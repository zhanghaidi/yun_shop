<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/5/22
 * Time: 下午3:34
 */

namespace Yunshop\Micro\widgets;

use app\common\components\Widget;
use Yunshop\Micro\common\models\GoodsMicro;
use Yunshop\Micro\common\models\MicroShopLevel;

class MicroGoodsWidget extends Widget
{
    public function run()
    {
        $micro_levels = MicroShopLevel::getLevelList()->get();
        $goods_model = GoodsMicro::getGoodsMicro($this->goods_id)->first();
        if ($goods_model) {
            $goods_model = $goods_model->toArray();
            $goods_model['set'] = unserialize($goods_model['set']);
        }
        return view('Yunshop\Micro::backend.Goods.micro_goods', [
            'micro_levels'  => $micro_levels,
            'micro_goods'   => $goods_model
        ])->render();
    }

    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }
        $microModel = self::getModel($goodsId, $operate);
        //判断deleted
        if ($operate == 'deleted') {
            return $microModel->delete();
        }
        if ($data['independent_bonus'] == 1) {
            foreach ($data['level'] as $row) {
                if (empty($row)) {
                    return false;
                }
                if (!is_float(floatval($row)) || !is_numeric($row)) {
                    return false;
                }
            }
        }
        $goods_micro_data = [
            'goods_id'  => $goodsId,
            'is_open_bonus' => $data['is_open_bonus'],
            'independent_bonus' => $data['independent_bonus'],
            'set'   => serialize($data['level'])
        ];
        $microModel->fill($goods_micro_data);
        return $microModel->save();
    }
    public static function getModel($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = GoodsMicro::getGoodsMicro($goodsId)->first();
        }
        !$model && $model = new GoodsMicro();
        return $model;
    }
}