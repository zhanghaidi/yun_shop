<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/24
 * Time: 6:10 PM
 */

namespace Yunshop\Love\Backend\Modules\Member\Models;


use app\common\scopes\UniacidScope;

class MemberLove extends \Yunshop\Love\Common\Models\MemberLove
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }

}
