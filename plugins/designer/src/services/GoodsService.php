<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/8
 * Time: 下午2:54
 */

namespace Yunshop\Designer\services;


use app\common\models\Goods;

class GoodsService extends Goods
{

    public static function getGoodsByIds($ids)
    {
        return self::uniacid()->whereIn('id', $ids)->get()->toArray();
    }

    public static function getLimitBuyByIds($ids)
    {
        return self::uniacid()->whereIn('id', $ids)->with(['hasOneGoodsLimitBuy' => function ($query) {
            return $query->select('goods_id', 'start_time', 'end_time');
        }]);
    }

    public static function isExist($id)
    {
        return self::uniacid()->where('id',$id)->where("status", 1)->first();
    }

}