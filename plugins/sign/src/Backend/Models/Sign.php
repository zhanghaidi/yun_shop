<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/6 下午3:19
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Backend\Models;


use app\common\scopes\UniacidScope;

class Sign extends \Yunshop\Sign\Common\Models\Sign
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }



}
