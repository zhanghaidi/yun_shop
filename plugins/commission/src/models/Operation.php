<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/11/13
 * Time: 10:30 AM
 */

namespace Yunshop\Commission\models;


use app\common\models\BaseModel;
use app\common\models\Order;
use Illuminate\Database\Eloquent\Builder;
use app\backend\modules\member\models\Member;

class Operation extends BaseModel
{
    public $table = 'yz_commission_operation';
    public $timestamps = true;
    protected $guarded = [''];

    public function hasOneOrder()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public function hasOneBuyMember()
    {
        return $this->hasOne(Member::class, 'uid', 'buy_uid');
    }

    public function scopeSearch($query, $search)
    {
        if ($search['order_sn']) {
            $query->whereHas('hasOneOrder', function ($order) use ($search) {
                $order->where('order_sn', 'like', '%' . $search['order_sn'] . '%');
            });
        }
        return $query;
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}