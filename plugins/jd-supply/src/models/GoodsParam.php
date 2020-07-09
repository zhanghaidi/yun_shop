<?php


namespace Yunshop\JdSupply\models;


class GoodsParam extends \app\backend\modules\goods\models\GoodsParam
{
    public static function saveParam($data, $goods_id = 2)
    {
        $param_titles = $data['param_title'];
        $param_values = $data['param_value'];
        $paramids = [];
        foreach ($param_titles as $k=>$v) {
            $param_id = "";
            $param = [
                "uniacid" => \YunShop::app()->uniacid,
                "title" => $param_titles[$k],
                "value" => $param_values[$k],
                "displayorder" => $k,
                "goods_id" => $goods_id
            ];
            $model = GoodsParam::create($param);
            $paramids[] = $model->id;
        }
        //删除本商品其它规格
        GoodsParam::where('goods_id', '=', $goods_id)->whereNotIn('id', $paramids )->delete();
    }
}