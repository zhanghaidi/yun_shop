<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/8 下午1:13
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Modules\Love\Models;


use Illuminate\Database\Eloquent\Builder;

class LoveTransferRecords extends \Yunshop\Love\Common\Models\LoveTransferRecords
{
    /**
     * 设置全局作用域
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(
            function(Builder $builder){
                return $builder->where('transfer',\YunShop::app()->getMemberId());
            }
        );
    }



}
