<?php

namespace Yunshop\XiaoeClock\models;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class XiaoeClock extends BaseModel
{

    use SoftDeletes;
    public $table = 'yz_xiaoe_clock';
    public $timestamps = true;
    protected $guarded = [''];


    //打卡关联多个主题(按天显示)
    public function hasManyTopic()
    {
        return $this->hasMany('Yunshop\XiaoeClock\models\XiaoeClockTopic', 'clock_id', 'id');
    }

    //打卡关联多个日记(按天显示)
    public function hasManyNote()
    {
        return $this->hasMany('Yunshop\XiaoeClock\models\XiaoeClockNote', 'clock_id', 'id');
    }

    //打卡关联所有用户
    public function hasManyUser()
    {
        return $this->hasMany('Yunshop\XiaoeClock\models\XiaoeClockUser', 'clock_id', 'id');
    }

    /**
     * 获取打卡用户信息
     */
    public function clockUser()
    {
        return $this->hasOneThrough('App\backend\modules\tracking\models\DiagnosticServiceUser', 'Yunshop\XiaoeClock\models\XiaoeClockUser',
            'clock_id', // 汽车表外键...
            'user_id', // 车主表外键...
            'id', // 修理工表本地键...
            'ajy_uid' // 汽车表本地键...
        );
    }

}