<?php

namespace Yunshop\LeaseToy\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use  Yunshop\LeaseToy\models\retreat\OrderReturnAddress;
use  Yunshop\LeaseToy\models\retreat\OrderReturnExpress;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/3/6
* Time: 15:12
*/
class LeaseOrderModel extends BaseModel
{
    use SoftDeletes;

    const PLUGIN_ID = 40; //待退还

    const RETURN_APPLY = 1; //前端：审核中..., 后端：同意审核
    const APPLY_ADOPT = 2; //前端： 退还中。。。 后端：待买家退还
    const STAY_CONFIRM = 4; //前端： 待确认 后端：确认退还
    const RETURNED = 3; //退还完成

    public $table = 'yz_plugin_lease_order';

    protected $guarded = [''];

    protected $appends = ['return_name', 'return_mode'];



    protected $hidden = [
        'uniacid',
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    protected $attributes = [
        'return_deposit' => 0,
    ];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

      /**
     * 时间类型字段
     * @return array
     */
    public function getDates()
    {
        return ['start_time','end_time', 'return_time'] + parent::getDates();
    }

    public function getReturnNameAttribute()
    {
        // $result = [];
        if (empty($this->return_status)) {
            return '待退还';
        }

        if ($this->return_status == self::RETURN_APPLY) {
            return '待审核';
        }

        if ($this->return_status == self::APPLY_ADOPT) {
            return '退还中';
        }
        if ($this->return_status == self::STAY_CONFIRM) {
            return '待确认';
        }

        if ($this->return_status == self::RETURNED) {
            return '已退还';
        }
    }

    public function getReturnModeAttribute()
    {
        if ($this->return_pay_type_id == 3) {
            return '余额';
        }
        if ($this->return_pay_type_id == -1) {
            return '手动退款';
        }

        return '其他退款';
    }

    //是否退还
    public function isReturned()
    {
        if ($this->return_status >= self::RETURNED) {
            return true;
        }
        return false;
    }
    //是否审核
    public function isApply()
    {
        if ($this->return_status == self::RETURN_APPLY) {
            return true;
        }
        return false;
    }
    //是否确认
    public function isConfirm()
    {
        if ($this->return_status == self::STAY_CONFIRM) {
            return true;
        }
        return false;
    }


    public function LeaseAddress()
    {
        return $this->hasOne(OrderReturnAddress::class, 'lease_id', 'id');
    }

    public function LeaseExpress()
    {
        return $this->hasOne(OrderReturnExpress::class, 'lease_id', 'id');
    }

    public function scopeReturnBuilder($query)
    {
        return $query->with('LeaseAddress')->with('LeaseExpress');
    }


    static public function getLeaseMemberOrder($uid, $search)
    {

        $model = self::ReturnTime()->where('member_id', $uid);

        // if (!empty($search['realname']) || !empty($search['level'])) {
        //     $model->whereHas('belongsToMember', function ($query) use($search) {
        //         if (!empty($search['level'])) {
        //             $member = $query->whereHas('yzMember', function ($query2) use ($search) {
        //                 return $query2->where('level_id', $search['level']);
        //             });
        //         }
        //         if (!empty($search['realname'])) {
        //             $member = $query->where('nickname', 'like', '%' . $search['realname'] . '%')
        //             ->orWhere('mobile', 'like', $search['realname'] . '%')
        //             ->orWhere('realname', 'like', '%' . $search['realname'] . '%');
        //         }
        //         return $member;
        //     });
        // }

        if (!empty($search['status'] || $search['status'] === '0')) {
            $model->where('return_status', $search['status']);
        }

        if ($search['searchtime'] == '1') {
            $range = [strtotime($search['times']['start']), strtotime($search['times']['end'])];
            $model->whereBetween('start_time', $range);
        }


        $model->with(['belongsToMember' => function ($query) use ($search) {
            $member = $query->select('uid', 'nickname', 'avatar', 'realname', 'mobile')
            ->with(['yzMember' => function ($query3) {
                return $query3->select('member_id','level_id');
            }]);
        }]);

        return $model;
    }

    public static function toTal($id)
    {
        $data['frozens'] = self::ReturnTime()->where('member_id', $id)->where('return_status', 0)->sum('deposit_total');
        $data['returns'] = self::ReturnTime()->where('member_id', $id)->where('return_status', self::RETURNED)->sum('return_deposit');

        $data['and'] = $data['frozens'] + $data['returns'];

        return $data;

    }

    public function scopeReturnTime($query)
    {
        return $query->where('start_time', '>', 0);
    }

    public function belongsToMember()
    {
        return $this->belongsTo('app\backend\modules\member\models\Member', 'member_id', 'uid');
    }


    //生成订单
    public function setOrder(PreOrder $order)
    {
        $order->hasOneLeaseOrder->push($this);

    }
}