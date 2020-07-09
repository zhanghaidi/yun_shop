<?php

namespace Yunshop\ClockIn\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Date: 2018/1/10 13:51
 */

class ClockRuleModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yz_clock_rule';


    public function getRule()
    {
        return $this->uniacid()->first();
    }
}