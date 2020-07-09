<?php

namespace Yunshop\ClockIn\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/08
 * Time: 下午3:24
 */
class ClockContinuityModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_clock_continuity';


    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }


    public static function getClockNum($start, $end)
    {
        return self::uniacid()
            ->whereBetween('updated_at', [$start, $end]);
    }

    public static function getClockByMemberId($memberId)
    {
        return self::uniacid()
            ->where('member_id', $memberId);
    }

}