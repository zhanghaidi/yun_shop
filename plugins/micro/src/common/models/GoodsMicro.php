<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/5/22
 * Time: 下午4:09
 */

namespace Yunshop\Micro\common\models;


use app\common\models\BaseModel;

class GoodsMicro extends BaseModel
{
    protected $table = 'yz_goods_micro';
    protected $guarded = [''];

    public static function getGoodsMicro($goods_id)
    {
        return self::select()->where('goods_id', $goods_id);
    }
}