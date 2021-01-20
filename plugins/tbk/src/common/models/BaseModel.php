<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2019/1/10
 * Time: 16:38
 */

namespace Yunshop\Tbk\common\models;


use app\common\scopes\UniacidScope;

class BaseModel extends \app\common\models\BaseModel
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope);
    }
}