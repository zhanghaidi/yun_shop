<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-08-05
 * Time: 09:41
 */

namespace Yunshop\LuckyDraw\admin\models;


use app\common\models\Goods;

class GoodsModel extends Goods
{
    public static function getGoodsByName($kwd)
    {
        return self::uniacid()->select('id', 'title', 'thumb')
            ->where('title', 'like', '%' . $kwd . '%')
            ->get();
    }
}