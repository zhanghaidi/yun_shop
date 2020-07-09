<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/12 下午7:27
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Frontend\Modules\Love\Models;


use Illuminate\Database\Eloquent\Builder;

class LoveActivationRecords extends \Yunshop\Love\Common\Models\LoveActivationRecords
{
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('member_id',function (Builder $builder) {
            return $builder->where('member_id',\YunShop::app()->getMemberId());
        });
    }

    public function scopeRecords($query)
    {
        return $query->select('id','created_at','actual_activation_love');
    }

}
