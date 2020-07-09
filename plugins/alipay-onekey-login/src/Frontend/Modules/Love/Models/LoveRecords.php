<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/30 上午11:01
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Modules\Love\Models;


use Illuminate\Database\Eloquent\Builder;


class LoveRecords extends \Yunshop\Love\Common\Models\LoveRecords
{
    /**
     * 设置全局作用域 拼接 uniacid()
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(
            function(Builder $builder){
                return $builder->where('member_id',\YunShop::app()->getMemberId());
            }
        );
    }

    /**
     * 前端会员 爱心值 变动记录
     * @param $query
     * @return mixed
     */
    public function scopeRecords($query)
    {
        return $query->select('id','source','type','value_type','change_value','created_at');
    }






}
