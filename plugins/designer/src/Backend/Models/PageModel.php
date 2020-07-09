<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-05-28
 * Time: 14:03
 */

namespace Yunshop\Designer\Backend\Models;


use app\common\scopes\UniacidScope;

class PageModel extends \Yunshop\Designer\Common\Models\PageModel
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
