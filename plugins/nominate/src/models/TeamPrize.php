<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/22
 * Time: 4:18 PM
 */

namespace Yunshop\Nominate\models;


use app\common\models\BaseModel;
use app\common\models\Order;
use Illuminate\Database\Eloquent\Builder;
use Yunshop\Nominate\services\MessageService;
use app\common\models\Member;

class TeamPrize extends BaseModel
{
    public $table = 'yz_nominate_team_prize';
    public $timestamps = true;
    protected $guarded = [''];

    protected $appends = [
        'status_name'
    ];

    const SUCCESS = 1;
    const WARTING = 0;
    const LOSE = 2;

    public static function getList($search)
    {
        return self::with([
            'member' => function ($member) {
                $member->select(['uid', 'nickname', 'realname', 'avatar', 'mobile']);
            },
            'order' => function ($order) {
                $order->select('id', 'order_sn', 'price');
            },
            'memberLevel' => function ($memberLevel) {
                $memberLevel->select('id', 'level_name');
            }
        ])->search($search);
    }

    public function getStatusNameAttribute()
    {
        $statusName = '未发放';
        if ($this->status == self::SUCCESS) {
            $statusName = '已发放';
        }elseif ($this->status == self::LOSE) {
            $statusName = '已失效';
        }
        return $statusName;
    }

    public function scopeSearch($query, $search)
    {
        if ($search['order_sn']) {
            $query->whereHas('order', function ($order) use ($search) {
                $order->where('order_sn', 'like', '%' . $search['order_sn'] . '%');
            });
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
            $query->where('uid', $search['uid']);
        }

        if ($search['search_time']) {
            $query->whereBetween('created_at', [strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }

        return $query;
    }

    /**
     * @name 用于前端获取列表
     * @author
     * @param $query
     * @param $status
     * @return mixed
     */
    public function scopeByStatusToApiList($query, $status)
    {
        if ($status == -1) {
            return $query;
        }
        return $query->where('status', $status);
    }

    public function scopeByOrderId($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public static function store($data)
    {
        $model = self::create($data);
        $typeAndAmount = '团队业绩奖' . '-' . $model->amount;
        MessageService::awardMessage($model->uniacid, $model->uid, $typeAndAmount);
        return $model;
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function memberLevel()
    {
        return $this->hasOne(ShopMemberLevel::class, 'id', 'level_id');
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}