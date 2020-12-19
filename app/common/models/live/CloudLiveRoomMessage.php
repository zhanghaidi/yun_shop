<?php

namespace app\common\models\live;

use Carbon\Carbon;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class CloudLiveRoomMessage extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_cloud_live_room_message';

    protected $guarded = [''];

    /*protected $casts = [
        'msg_content' => 'json',
    ];*/

    public function getMsgContentAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * 相关的用户信息。
     * return $this->hasOne('App\User', 'foreign_key', 'local_key');
     */
    public function user()
    {
        return $this->belongsTo('App\backend\modules\tracking\models\DiagnosticServiceUser','user_id','ajy_uid');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecords($query)
    {
        return $query->with(['user' => function ($user) {
            return $user->select('ajy_uid', 'nickname', 'avatarurl');
        }]);
    }

    //搜索条件
    public function scopeSearch($query, array $search)
    {

        //根据关键词筛选

        if ($search['keywords']) {
            $query = $query->where('group_id', $search['keywords'])->orWhere('user_id', $search['keywords'])->orWhere('client_ip', $search['keywords']);
        }
        //根据时间筛选
        if ($search['search_time'] == 1) {
            $query = $query->whereBetween('created_at', [strtotime($search['time']['start']),strtotime($search['time']['end'])]);
        }
        return $query;
    }

}
