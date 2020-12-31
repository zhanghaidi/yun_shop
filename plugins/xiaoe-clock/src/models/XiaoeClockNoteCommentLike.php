<?php

namespace Yunshop\XiaoeClock\models;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class XiaoeClockNoteCommentLike extends BaseModel
{

    use SoftDeletes;
    public $table = 'yz_xiaoe_users_clock_comment';
    public $timestamps = true;
    protected $guarded = [''];

    //关联用户
    public function user()
    {
        return $this->belongsTo('App\backend\modules\tracking\models\DiagnosticServiceUser','user_id','ajy_uid');
    }

}