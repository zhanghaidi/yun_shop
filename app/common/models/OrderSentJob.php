<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSentJob extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_order_sent_job';

    protected $guarded = ['id'];

}
