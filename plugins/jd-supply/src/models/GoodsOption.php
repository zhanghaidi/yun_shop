<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/11
 * Time: 10:45
 */

namespace Yunshop\JdSupply\models;


use Illuminate\Support\Facades\DB;
use Yunshop\JdSupply\services\JdGoodsService;

class GoodsOption extends \app\common\models\GoodsOption
{
    public static function saveOption($source,$goods_model, $options, $spec_items, $uniacid)
    {
        if (empty($options)) {
            return false;
        }

        $goods_id = $goods_model->id;
        $stock = $goods_model->stock;
        $optionids = [];
        $jd_option_ids = [];
        $flag = true;
        foreach ($options as $option_item) {

            $option_ids = explode("_", $option_item['spec_value_ids']);
            $new_ids = [];
            foreach ($option_ids as $ida) {
                foreach ($spec_items as $jd_spec_value) {
                    if ($jd_spec_value['jd_specs_value_id'] == $ida) {
                        $new_ids[] = $jd_spec_value['id'];
                        break;
                    }
                }
            }
//            if ($option_item['id'] == 3383605) {
//                dd($new_ids,$options,$spec_items,$option_ids);
//            }
            $new_ids = implode("_", $new_ids);
            $price_data = [
                'source'            =>  $source,
                'guide_price'       =>  $option_item['guide_price']/100,
                'agreement_price'   =>  $option_item['agreement_price']/100,
                'activity_price'    =>  $option_item['activity_price']/100
            ];

            $goodsOption = [
                "uniacid" => $uniacid,
                "title" => $option_item['spec_value_names'],
                "product_price" => JdGoodsService::getGuidePrice($price_data), // 第三方指导价格 == 销售价格
                "cost_price" => JdGoodsService::getCostPrice($price_data), // 第三方协议价格 == 成本价格
                "market_price" => $option_item['market_price']/100, // 第三方市场价格 == 市场价格 == 原价
                "stock" => $option_item['stock'],
                "weight" => $option_item['weight'],
                "goods_sn" => '',
                "product_sn" => '',
                "goods_id" => $goods_id,
                "specs" => $new_ids,
                'virtual' => 0,
                'thumb' =>  $option_item['image']
            ];
            //风控策略
            if ($flag) {
                $flag = JdGoodsService::controlMethod($goodsOption['product_price'], $goodsOption['cost_price'], $goods_model);
            }

            $goodsOptionModel = static::create($goodsOption);
            $option_id = $goodsOptionModel->id;
            if ($goodsOptionModel) {
                $jd_option = [
                    'goods_id' => $goods_id,
                    'option_id' => $option_id,
                    'shop_goods_specs' => $new_ids,
                    'jd_goods_id' => $option_item['goods_id'],
                    'jd_option_id' => $option_item['id'],
                    'spec_value_ids' => $option_item['spec_value_ids'],
                    'spec_value_names' => $option_item['spec_value_names'],
                ];

                $jd_option_ids[] = self::createJdOption($jd_option);
                $optionids[] = $option_id;
            }
        }

        if (count($optionids) > 0) {
            JdGoodsOption::where('goods_id', $goods_id)->withoutGlobalScopes()->whereNotIn('id', $jd_option_ids)->delete();
            static::where('goods_id', '=', $goods_id)->whereNotIn('id', $optionids)->delete();
        } else {
            JdGoodsOption::where('goods_id', $goods_id)->withoutGlobalScopes()->delete();
            static::where('goods_id', '=', $goods_id)->delete();
        }

        return true;
    }

    public static function saveCloseOption($source,$goods_model, $options, $spec_items, $uniacid)
    {
        if (empty($options)) {
            return false;
        }

        $goods_id = $goods_model->id;
        $stock = $goods_model->stock;
        $optionids = [];
        $jd_option_ids = [];
        $flag = true;
        foreach ($options as $option_item) {

            $option_ids = explode("_", $option_item['spec_value_ids']);
            $new_ids = [];
            foreach ($option_ids as $ida) {
                foreach ($spec_items as $jd_spec_value) {
                    if ($jd_spec_value['jd_specs_value_id'] == $ida) {
                        $new_ids[] = $jd_spec_value['id'];
                        break;
                    }
                }
            }
            //新的option
            $new_ids = implode("_", $new_ids);
            $price_data = [
                'source'            =>  $source,
                'guide_price'       =>  $option_item['guide_price']/100,
                'agreement_price'   =>  $option_item['agreement_price']/100,
                'activity_price'    =>  $option_item['activity_price']/100
            ];
            $goodsOption = [
                "uniacid" => $uniacid,
                "title" => $option_item['spec_value_names'],
                "product_price" => JdGoodsService::getGuidePrice($price_data), // 第三方指导价格 == 销售价格
                "cost_price" => JdGoodsService::getCostPrice($price_data), // 第三方协议价格 == 成本价格
                "market_price" => $option_item['market_price']/100, // 第三方市场价格 == 市场价格 == 原价
                "stock" => $option_item['stock'],
                "weight" => $option_item['weight'],
                "goods_sn" => '',
                "product_sn" => '',
                "goods_id" => $goods_id,
                "specs" => $new_ids,
                'virtual' => 0,
                'thumb' =>  $option_item['image']
            ];
            //查出售价
            $old_option = JdGoodsOption::GoodsId($goods_id)->JdGoodsId($option_item['goods_id'])->JdOptionId($option_item['id'])->with('hasOneOption')->first();
            \Log::debug('聚合商品旧售价',$old_option->hasOneOption->product_price);
            if ($old_option->hasOneOption) {
                $goodsOption['product_price'] = $old_option->hasOneOption->product_price;
            }
            //风控策略
            if ($flag) {
               $flag = JdGoodsService::controlMethod($goodsOption['product_price'], $goodsOption['cost_price'], $goods_model);
            }
            $goodsOptionModel = static::create($goodsOption);
            $option_id = $goodsOptionModel->id;
            if ($goodsOptionModel) {
                $jd_option = [
                    'goods_id' => $goods_id,
                    'option_id' => $option_id,
                    'shop_goods_specs' => $new_ids,
                    'jd_goods_id' => $option_item['goods_id'],
                    'jd_option_id' => $option_item['id'],
                    'spec_value_ids' => $option_item['spec_value_ids'],
                    'spec_value_names' => $option_item['spec_value_names'],
                ];

                $jd_option_ids[] = self::createJdOption($jd_option);
                $optionids[] = $option_id;
            }
        }

        if (count($optionids) > 0) {
            JdGoodsOption::where('goods_id', $goods_id)->withoutGlobalScopes()->whereNotIn('id', $jd_option_ids)->delete();
            static::where('goods_id', '=', $goods_id)->whereNotIn('id', $optionids)->delete();
        } else {
            JdGoodsOption::where('goods_id', $goods_id)->withoutGlobalScopes()->delete();
            static::where('goods_id', '=', $goods_id)->delete();
        }

        return true;
    }

    public static function createJdOption($jd_option)
    {
        $jd_option_model = JdGoodsOption::create($jd_option);

        return $jd_option_model->id;
    }
}