<?php

namespace Yunshop\VideoDemand\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
* 
*/
class MemberCourseModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yz_video_member_course';

    public $timestamps = true;

    protected $guarded = [''];

    /**
     * 我的课程
     * @param  [int] $uid [description]
     * @return [array]      [description]
     */
    public function memberCourse($uid)
    {
        $data = self::select(['goods_id','order_sn','created_at'])
                ->AddedWhere($uid)
                ->with(['courseGoods' => function ($query) {
                    return $query->select(['id','title','thumb']);
                }])->get()->toArray();

        return $data;
    }

    public function scopeAddedWhere($query, $uid)
    {
        return $query->where('member_id', $uid)->uniacid()->orderBy('created_at', 'desc');
    }


    //一对多(反向)
    public function courseGoods()
    {
        return $this->belongsTo('app\common\models\Goods', 'goods_id', 'id');
    }

}