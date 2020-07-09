<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/10
 * Time: 9:25
 */

namespace Yunshop\Designer\Backend\Models;

use app\common\scopes\UniacidScope;

class MemberPageModel extends \Yunshop\Designer\Common\Models\MemberPageModel
{
    protected $appends = ['page_type_cast'];


    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }


    public function getPageTypeCastAttribute()
    {
        return explode(',', $this->attributes['page_type']);
    }


    public function scopeSearch($query, array $search)
    {
        if ($search['name']) {
            $query->where('page_name', 'like', $search['name'] . "%");
        }
        return $query;
    }
}