<?php
namespace Yunshop\VideoDemand\models;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/06
 * Time: 下午1:54
 */
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoricalModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_video_historical';
    public $timestamps = true;
    protected $guarded = [''];


    protected $attributes = [];


    protected $hidden = ['course_id', 'course_chapter_id'];

    /**
     * 查询用户是否有观看过
     * @param  [array] $where 观看信息
     * @return [objtec]       [description]
     */
    public function inquireNotes($where)
    {
        $model = self::uniacid()->where(function($query) use($where) {
            $query->where('member_id', '=', $where['member_id']);
        })->where(function($query) use($where) {
            $query->where('course_id', '=', $where['course_id']);
        })->first();

        return $model;

    }

    public function memberScan($uid, $pageSize = 15)
    {
        $model = self::AddedWhere($uid);
        
        $model->select(['course_id', 'course_chapter_id']);

        $model->with(['historyCourseGoods' => function ($query) {

            return $query->select(['id', 'goods_id'])->with(['hasOneGoods' => function ($query3) {
                    return $query3->select(['id', 'title','thumb']);
                }]);
            }]);

        $model->with(['historyCourseChapter' => function ($query2) {
            return $query2->select(['id', 'chapter_name']);
        }]);

        return $model->paginate($pageSize)->toArray();

        /*return self::select(['course_id', 'course_chapter_id'])->AddedWhere($uid)
        ->with(['historyCourseGoods' => function ($query) {
            return $query->select(['id', 'goods_id'])->with(['hasOneGoods' => function ($query3) {
                return $query3->select(['id', 'title','thumb']);
            }]);
        }, 'historyCourseChapter' => function ($query2) {
            return $query2->select(['id', 'chapter_name']);
        }])
        ->get()->toArray();*/
    }

    
    public function scopeAddedWhere($query, $uid)
    {
        return $query->where('member_id', $uid)->uniacid()->orderBy('created_at', 'desc');
    }

    //一对多(反向)
    public function historyCourseGoods()
    {
        return $this->belongsTo('Yunshop\VideoDemand\models\CourseGoodsModel', 'course_id', 'id');
    }

    //一对多(反向)
    public function historyCourseChapter()
    {
        return $this->belongsTo('Yunshop\VideoDemand\models\CourseChapterModel', 'course_chapter_id', 'id');
    }
}