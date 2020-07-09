<?php

namespace Yunshop\ClockIn\models;

use app\common\models\BaseModel;
use app\common\traits\CreateOrderSnTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Yunshop\ClockIn\services\ClockInService;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/08
 * Time: 下午3:24
 */
class ClockPayLogModel extends BaseModel
{
    use SoftDeletes, CreateOrderSnTrait;

    public $table = 'yz_clock_pay_log';

    public $payStatusName;
    public $clockInStatusName;
    protected $appends = ['pay_status_name', 'clock_in_status_name'];

    /**
     * @param $search
     * @return mixed
     */
    public static function getList($search)
    {
        $model = self::uniacid();
        if ($search['log_id']) {
            $model->where('id', $search['log_id']);
        }

        if (!empty($search['member'])) {
            $model->whereHas('hasOneMember', function ($query) use ($search) {
                return $query->searchLike($search['member']);
            });
        }

        $model->with('hasOneMember');
        $model->with('hasOnePayType');

        if ($search['pay_method']) {
            $model->where('pay_method', $search['pay_method']);
        }

        if ($search['queue_id']) {
            $model->where('queue_id', $search['queue_id']);
        }

        if ($search['is_time']) {
            if ($search['time']) {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $model->whereBetween('created_at', $range);
            }
        }
        return $model;
    }

    /**
     * @return string
     */
    public function getPayStatusNameAttribute()
    {
        if (!isset($this->payStatusName)) {
            $this->payStatusName = ClockInService::getPayStatusName($this->pay_status);
        }
        return $this->payStatusName;
    }

    /**
     * @return string
     */
    public function getClockInStatusNameAttribute()
    {
        if (!isset($this->clockInStatusName)) {
            $this->clockInStatusName = ClockInService::getClockInStatusName($this->clock_in_status);
        }
        return $this->clockInStatusName;
    }



    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOnePayType()
    {
        return $this->hasOne('app\common\models\PayType', 'id', 'pay_method');
    }

    /**
     * @param $start
     * @param $end
     * @return mixed
     */
    public static function getStatistic($start, $end)
    {
        return self::uniacid()
            ->where('pay_status', 1)
            ->whereBetween('created_at', [$start, $end]);
    }

    /**
     * @param $start
     * @param $end
     * @param $queueId
     * @return mixed
     */
    public static function updatedPayLog($start, $end, $queueId)
    {
        return self::uniacid()
            ->whereBetween('created_at', [$start, $end])
            ->where('pay_status', 1)
            ->update(['queue_id' => $queueId]);
    }

    /**
     * @param $start
     * @param $end
     * @return mixed
     */
    public static function getClockInLog($start, $end)
    {
        return self::uniacid()
            ->whereBetween('clock_in_at', [$start, $end])
            ->with('hasOneMember');
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public static function getPayLogByMemberId($memberId)
    {
        $model = self::uniacid();

        $model->with(['hasOneReward' => function ($query) {
            return $query->select('pay_id', 'amount');
        }]);

        $model->where('member_id', $memberId);

        return $model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneReward()
    {
        return $this->hasOne('Yunshop\ClockIn\models\ClockRewardLogModel', 'pay_id', 'id');
    }

}