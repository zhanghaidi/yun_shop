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

    //关联加入学员
    public function joinUser()
    {
        return $this->belongsTo('Yunshop\XiaoeClock\models\XiaoeClockUser','user_id','user_id');
    }

    //关联打卡
    public function clock()
    {
        return $this->belongsTo('Yunshop\XiaoeClock\models\XiaoeClock','clock_id','id');
    }

    //关联主题
    public function topic()
    {
        return $this->belongsTo('Yunshop\XiaoeClock\models\XiaoeClockTopic','clock_task_id','id');
    }

    //是否点赞
    public function isLike()
    {
        return $this->hasOne('Yunshop\XiaoeClock\models\XiaoeClockNoteLike', 'clock_users_id', 'id');
    }


    //获取器转为数组
    public function getImageDescAttribute($value){
        return json_decode($value, true);
    }









}