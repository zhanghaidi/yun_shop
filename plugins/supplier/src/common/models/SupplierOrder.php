<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/24
 * Time: 上午10:41
 */

namespace Yunshop\Supplier\common\models;

use app\common\models\BaseModel;
use app\common\models\Member;
use app\common\models\Order;
use Yunshop\Supplier\common\Observer\SupplierOrderObserver;

/**
 * Class SupplierOrder
 * @package Yunshop\Supplier\common\models
 * @property Supplier supplier
 */
class SupplierOrder extends BaseModel
{
    public $table = 'yz_supplier_order';
    protected $guarded = [''];
    protected $attributes = [
        'apply_status' => 0
    ];

    protected $appends = [
        'status_name'
    ];

    public static function boot()
    {
        parent::boot();
        static::observe(new SupplierOrderObserver());
    }

    public function getStatusNameAttribute()
    {
        $statusName = '待提现';
        if ($this->apply_status == 1) {
            $statusName = '已提现';
        }

        return $statusName;
    }

    public function scopeSearch($query, $search)
    {
        if ($search['id']) {
            $query->where('id', $search['id']);
        }
        if ($search['member']) {
            $query->whereHas('member', function ($member) use ($search) {
                $member->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $search['member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $search['member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $search['member'] . '%');
            });
        }
        if ($search['uid']) {
            $query->where('member_id', $search['uid']);
        }

        if ($search['order_sn']) {
            $query->whereHas('order', function ($order) use ($search) {
                $order->select('id', 'order_sn', 'price')
                    ->where('order_sn', 'like', '%' . $search['order_sn'] . '%');
            });
        }

        return $query;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'uid', 'member_id');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}