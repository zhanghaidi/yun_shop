<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/19 0019
 * Time: 上午 11:16
 */

namespace Yunshop\Love\Backend\Modules\Love\Models;


use app\common\scopes\UniacidScope;

class MemberLove extends \Yunshop\Love\Common\Models\MemberLove
{
    public static function boot()
    {
        return parent::boot();
        self::addGlobalScope(new UniacidScope());
    }
}
