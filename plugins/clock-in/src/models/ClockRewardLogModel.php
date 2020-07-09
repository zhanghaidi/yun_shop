<?php

namespace Yunshop\ClockIn\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/08
 * Time: 下午3:24
 */
class ClockRewardLogModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_clock_reward_log';

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

        if ($search['is_time']) {
            if ($search['time']) {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $model->whereBetween('created_at', $range);
            }
        }
        return $model;
    }

    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }


    public static function getRewardByTime($start, $end)
    {
        return self::uniacid()
            ->where('status', 1)
            ->whereBetween('created_at', [$start, $end]);
    }

    public static function getRewardNum()
    {
        return self::uniacid();
    }

    public static function getRewardByMemberId($memberId)
    {
        $model = self::uniacid();

        $model->where('member_id', $memberId);

        return $model;
    }

}