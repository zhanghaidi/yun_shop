<?php

namespace Yunshop\XiaoeClock\models;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class XiaoeClockNote extends BaseModel
{

    use SoftDeletes;
    public $table = 'yz_xiaoe_users_clock';
    public $timestamps = true;
    protected $guarded = [''];

    //打卡日记关联点赞
    public function hasManyLike()
    {

        return $this->hasMany('Yunshop\XiaoeClock\models\XiaoeClockNoteLike', 'clock_users_id', 'id');
    }

    //打卡日记关联评论
    public function hasManyComment()
    {
        return $this->hasMany('Yunshop\XiaoeClock\models\XiaoeClockNoteComment', 'clock_users_id', 'id');
    }


    //关联用户
    public function user()
    {
        return $this->belongsTo('App\backend\modules\tracking\models\DiagnosticServiceUser','user_id','ajy_uid');
    }

    //关联打卡
    public function belongsToClock()
    {
        return $this->belongsTo('Yunshop\XiaoeClock\models\XiaoeClock','clock_id','id');
    }

    //关联主题
    public function topic()
    {
        return $this->belongsTo('Yunshop\XiaoeClock\models\XiaoeClockTopic','clock_task_id','id');
    }








}