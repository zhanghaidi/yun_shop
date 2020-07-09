<?php

namespace Yunshop\ClockIn\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/08
 * Time: 下午3:24
 */
class ClockQueueModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_clock_queue';

    public static function getList()
    {
        $model = self::uniacid();


        return $model;
    }


    public static function getStatistic($start, $end)
    {
        return self::uniacid()
            ->whereBetween('created_at', [$start, $end]);
    }

}