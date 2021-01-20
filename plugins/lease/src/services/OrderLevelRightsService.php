<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/12
 * Time: 下午1:48
 */
namespace Yunshop\LeaseToy\services;

use Yunshop\LeaseToy\models\Goods;
use Yunshop\LeaseToy\models\LeaseToyGoodsModel;

class OrderLevelRightsService
{

    /**
     * 筛选可使用权益的商品
     * @param  [type] $goodsArr [description]
     * @return [type]           [description]
     */
    static public function getSupportRightsGoods($goodsArr)
    {
       
        $goodsIds = array_pluck($goodsArr, 'goods_id');

        //获取有权益的商品
       $goodsModel = LeaseToyGoodsModel::select('goods_id')->whereIn('goods_id', $goodsIds)->where('is_rights', 1)->get()->toArray();
        $ids = array_pluck($goodsModel, 'goods_id');
       foreach ($goodsArr as $key => $value) {
           if (in_array($value['goods_id'], $ids)) {
                $rightsGoods[] = $value;
           }
       }
       return $rightsGoods;
    }

    /**
     * 免租金
     * @param  [type] $goodsArr      [description]
     * @param  [type] $rentFreeTotal [description]
     * @return [type]                [description]
     */
    static public function getGoodsPriceSort($goodsArr, $rentFreeTotal)
    {
        // $rentFreeTotal = 6;
        // $goodsArr = [
        //     ['goods_id' => 100, 'total' => 2],
        //     ['goods_id' => 109, 'total' => 1],
        //     ['goods_id' => 99, 'total' => 1],
        // ];
        $goodsIds = array_pluck($goodsArr, 'goods_id');

        //根据价格排序
        $goodsModel = Goods::select('id')->whereIn('id', $goodsIds)->orderBy('price')->get()->toArray();
        $ids = array_pluck($goodsModel, 'id');

        return self::freeAdmission($rentFreeTotal, $goodsArr, $ids);
    }

    /**
     * 免押金
     * @param  [type] $goodsArr         [description]
     * @param  [type] $depositFreeTotal [description]
     * @return [type]                   [description]
     */
    static public function getGoodsDepositSort($goodsArr, $depositFreeTotal)
    {

        $goodsIds = array_pluck($goodsArr, 'goods_id');

        //根据商品押金排序
        $goodsModel = LeaseToyGoodsModel::select('goods_id')->whereIn('goods_id', $goodsIds)->orderBy('goods_deposit')->get()->toArray();
        $ids = array_pluck($goodsModel, 'goods_id');

        return self::freeAdmission($depositFreeTotal, $goodsArr, $ids);
    }


    /**
     * [freeAdmission description]
     * @param  [int] $FreeTotal 免权益数量
     * @param  [array] $goodsArr  [免权益的商品]
     * @param  [array] $goods_ids [免权益商品id]
     * @return [array]            [最终免权益的商品]
     */
    static public function freeAdmission($FreeTotal, $goodsArr, $goods_ids)
    {
        $id = array_shift($goods_ids);
        if (empty($id)) return;
        // dd($FreeTotal, $goodsArr, $goods_ids);
        foreach ($goodsArr as $value) {

            if ($value['goods_id'] == $id) {
                $total = max(($FreeTotal - $value['total']), 0);
                if ($total > 0) {

                    $final_free = self::freeAdmission($total, $goodsArr, $goods_ids);
                    $final_free[] = [
                        'goods_id' => $id,
                        'free_num' => $value['total'],
                    ];
                } else {
                    $final_free[] = [
                        'goods_id' => $id,
                        'free_num' => $FreeTotal,
                    ];
                }

                break;
            }

        }
        return $final_free;
    }
}

