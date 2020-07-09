<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-05
 * Time: 14:37
 */

namespace Yunshop\Designer\Backend\Models;


use app\common\scopes\UniacidScope;

class MenuModel extends \Yunshop\Designer\Common\Models\MenuModel
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }

}
