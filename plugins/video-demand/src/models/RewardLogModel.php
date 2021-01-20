<?php

namespace Yunshop\VideoDemand\models;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/06
 * Time: 下午1:54
 */
use app\common\models\BaseModel;
use app\common\traits\CreateOrderSnTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class RewardLogModel extends BaseModel
{
    use SoftDeletes, CreateOrderSnTrait;
    public $table = 'yz_video_reward_log';
    public $timestamps = true;
    protected $guarded = [''];

    protected $hidden = [];

    const PAY_YES_MONEY = 1;
    const PAY_NOT_MONEY = 0;

    /**
     * 我的打赏
     * @param [int] $uid [id]
     * @return  [array] $data
     */
    public function meReward($uid)
    {
        $data = self::select(['goods_id', 'lecturer_id', 'amount', 'order_sn', 'created_at'])->AddedWhere($uid)
            ->with(['rewardGoods' => function ($query) {
                return $query->select(['id', 'title', 'thumb']);
            }, 'rewardLecturer' => function ($query2) {
                return $query2->select(['id', 'real_name']);
            }])->get()->toArray();

        return $data;
    }

    public function scopeAddedWhere($query, $uid)
    {
        return $query->uniacid()->where('pay_status', self::PAY_YES_MONEY)->where('member_id', $uid)->orderBy('created_at', 'desc');
    }


    //一对多(反向)
    public function rewardGoods()
    {
        return $this->belongsTo('app\common\models\Goods', 'goods_id', 'id');
    }

    //一对多(反向)
    public function rewardLecturer()
    {
        return $this->belongsTo('Yunshop\VideoDemand\models\LecturerModel', 'lecturer_id', 'id');
    }

    public static function getRewardLogByOrderSn($orderSn)
    {
        return self::uniacid()
            ->where('order_sn', $orderSn);
    }

}