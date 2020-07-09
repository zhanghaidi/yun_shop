<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/14
 * Time: 下午4:00
 */

namespace Yunshop\Exhelper\common\models;


use app\common\models\BaseModel;

class Short extends BaseModel
{
    public $table = 'yz_exhelper_goods';
    protected $guarded = [''];
    public $timestamps = false;

    public static function getShortByGoodsId($goods_id)
    {
        return self::select()->where('goods_id', $goods_id);
    }
}