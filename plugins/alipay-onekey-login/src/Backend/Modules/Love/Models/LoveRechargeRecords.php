<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/24 上午11:19
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Modules\Love\Models;


use app\common\scopes\UniacidScope;
use Yunshop\Love\Common\Observers\Love\RechargeObserver;


class LoveRechargeRecords extends \Yunshop\Love\Common\Models\LoveRechargeRecords
{
    protected $appends = ['type_name'];

    public function getDates()
    {
        return ['created_at'];
    }
    
    /**
     * Payment translation set.
     *
     * @var array
     */
    private static $typeComment = [
        0 => "后台充值",
        1 => "微信支付",
        2 => "支付宝支付"
    ];


    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
        self::observe(new RechargeObserver());
    }

    /**
     * Gets the value of the additional field type_name.
     *
     * @return string
     */
    public function getTypeNameAttribute()
    {
        return static::getTypeNameComment($this->attributes['type']);
    }

    /**
     * Gets the value of the additional field type_name.
     *
     * @param $attributes
     * @return string
     */
    public function getTypeNameComment($attributes)
    {
        return isset(static::$typeComment[$attributes]) ? static::$typeComment[$attributes] : "其他支付";
    }

    public function scopeSearch($query, $search)
    {
        if ($search['order_sn']) {
            $query->where('order_sn', 'like', $search['order_sn'] . '%');
        }
        
        if ($search['is_time'] == 1) {
            $query->where('created_at', '>', strtotime($search['time']['start']))->where('created_at', '<', strtotime($search['time']['end']));
        }

        return $query;
    }

    public function scopeSearchMember($query, $search)
    {
        if ($search['realname']) {
            $query->whereHas('member', function($query)use($search) {
                return $query->searchLike($search['realname']);
            });
        }
        return $query;
    }

    public function scopeWithMember($query)
    {
        return $query->with(['member' => function($query) {
            return $query->select('uid', 'nickname','realname','mobile','avatar');
        }]);
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'uniacid'       => "required",
            'member_id'     => "required",
            'change_value'  => 'numeric|regex:/^[\-\+]?\d+(?:\.\d{1,2})?$/|max:9999999999',
            'type'          => 'required',
            'order_sn'      => 'required',
            'status'        => 'required',
            'value_type'    => 'required',
            'remark'        => 'max:50'
        ];
    }
}
