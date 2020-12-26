<?php

namespace Yunshop\XiaoeClock\models;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class XiaoeClockTopic extends BaseModel
{

    use SoftDeletes;
    public $table = 'yz_xiaoe_clock_task';
    public $timestamps = true;
    protected $guarded = [''];


    //关联所属打卡
    public function belongsToClock()
    {
        return $this->belongsTo('Yunshop\XiaoeClock\models\XiaoeClock','clock_id','id');
    }

    //该主题下参与用户
    public function hasManyUser()
    {
        return $this->hasMany('Yunshop\XiaoeClock\models\XiaoeClockUser', 'clock_task_id', 'id');
    }

    //该主题下日记数
    public function hasManyNote()
    {
        return $this->hasMany('Yunshop\XiaoeClock\models\XiaoeClockNote', 'clock_id', 'id');
    }


}