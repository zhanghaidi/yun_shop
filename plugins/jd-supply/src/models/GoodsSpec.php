<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/11
 * Time: 10:44
 */

namespace Yunshop\JdSupply\models;


class GoodsSpec extends \app\common\models\GoodsSpec
{
    public static $spec_items = [];

    public static function saveSpec($goods_id, $specs, $uniacid)
    {

        $spec_names = $specs['names'];
        $spec_values = $specs['values'];
        $data = [];
        if (empty($spec_names) || empty($spec_values)) {
            return false;
        }
        $specids = [];
        $spec_items = [];
        foreach ($spec_names as $name_key => $name_item) {
            if (!in_array())

            $spec = [
                "uniacid" => $uniacid,
                "goods_id" => $goods_id,
                "display_order" => $name_key,
                "title" => $name_item['name'],
            ];

            $goods_spec = static::create($spec);
            $spec_id = $goods_spec->id;

            $name_values = array_filter($spec_values, function ($a) use ($name_item) {
                return $a['spec_name_id'] == $name_item['id'] ;
            });
            $data[] = array_column($name_values,'id');
            $valueIndex = 0;
            $itemids = [];
            foreach ($name_values as $value_item) {
                $specItem = [
                    "uniacid" => $uniacid,
                    "specid" => $spec_id,
                    "display_order" => $valueIndex,
                    "title" => $value_item['name'],
                    "show" => 1,
                    "virtual" => 0,
                ];

                $goods_spec_item = GoodsSpecItem::create($specItem);

                $itemids[] = $goods_spec_item->id;
                $specItem['get_id'] = $goods_spec_item->id;
                $specItem['id'] = $goods_spec_item->id;
                $specItem['jd_specs_value_id'] =  $value_item['id'];
                $spec_items[] = $specItem;
                self::$spec_items = $spec_items;

                $valueIndex += 1;
            }
            if (count($itemids) > 0) {
                GoodsSpecItem::where('specid', '=', $spec_id)->whereNotIn('id', $itemids)->delete();
            } else {
                GoodsSpecItem::where('specid', '=', $spec_id)->delete();
            }
            static::updateOrCreate(['id' => $spec_id], ['content' => serialize($itemids)]);
            $specids[] = $spec_id;
        }
        if (count($specids) > 0) {
            static::where('goods_id', '=', $goods_id)->whereNotIn('id', $specids)->delete();
        } else {
            static::where('goods_id', '=', $goods_id)->delete();
        }
        return $data;
    }

    public static function saveCloseSpec($goods_id, $specs, $uniacid)
    {

        $spec_names = $specs['names'];
        $spec_values = $specs['values'];
        $data = [];
        if (empty($spec_names) || empty($spec_values)) {
            return false;
        }

        $specids = [];
        $spec_items = [];
        foreach ($spec_names as $name_key => $name_item) {
            $spec = [
                "uniacid" => $uniacid,
                "goods_id" => $goods_id,
                "display_order" => $name_key,
                "title" => $name_item['name'],
            ];
            $goods_spec = static::create($spec);
            $spec_id = $goods_spec->id;

            $name_values = array_filter($spec_values, function ($a) use ($name_item) {
                return $a['spec_name_id'] == $name_item['id'];
            });
            $data[] = array_column($name_values,'id');
            $valueIndex = 0;
            $itemids = [];
            foreach ($name_values as $value_item) {
                $specItem = [
                    "uniacid" => $uniacid,
                    "specid" => $spec_id,
                    "display_order" => $valueIndex,
                    "title" => $value_item['name'],
                    "show" => 1,
                    "virtual" => 0,
                ];

                $goods_spec_item = GoodsSpecItem::create($specItem);

                $itemids[] = $goods_spec_item->id;
                $specItem['get_id'] = $goods_spec_item->id;
                $specItem['id'] = $goods_spec_item->id;
                $specItem['jd_specs_value_id'] =  $value_item['id'];
                $spec_items[] = $specItem;
                self::$spec_items = $spec_items;

                $valueIndex += 1;
            }
            if (count($itemids) > 0) {
                GoodsSpecItem::where('specid', '=', $spec_id)->whereNotIn('id', $itemids)->delete();
            } else {
                GoodsSpecItem::where('specid', '=', $spec_id)->delete();
            }
            static::updateOrCreate(['id' => $spec_id], ['content' => serialize($itemids)]);
            $specids[] = $spec_id;
        }
        if (count($specids) > 0) {
            static::where('goods_id', '=', $goods_id)->whereNotIn('id', $specids)->delete();
        } else {
            static::where('goods_id', '=', $goods_id)->delete();
        }

        return $data;
    }
}