<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/23
 * Time: 下午6:39
 */

namespace Yunshop\Community\models;

use app\common\models\BaseModel;

class AgrGroup extends BaseModel
{
    public $table = 'agr_group';
    public $timestamps = false;
    protected $guarded = [''];
    protected $attributes = [
        'status' => 0,
        'ft_time' => 0,
        'is_yk' => 0,
        'pay' => 0,
        'money' => 0,
        'pl_time' => 0
    ];

    public static function getGroupByShopLevelId($id, $uniacid)
    {
        return self::select()->byShopLevelId($id)->byAcid($uniacid);
    }

    public function scopeByShopLevelId($query, $id)
    {
        return $query->whereShopLevelId($id);
    }

    public function scopeByAcid($query, $uniacid)
    {
        return $query->whereAcid($uniacid);
    }
}