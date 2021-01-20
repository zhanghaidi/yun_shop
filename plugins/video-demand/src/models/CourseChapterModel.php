<?php
namespace Yunshop\VideoDemand\models;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/06
 * Time: 下午1:54
 */
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseChapterModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_video_course_chapter';
    public $timestamps = true;
    protected $guarded = [''];

    public static function getCourseChapterByCourseId($courseId)
    {
        return self::uniacid()
            ->where('course_id',$courseId);
    }

    public static function relationSave($chapterId, $data)
    {
        $ChapterModel = self::getModel($chapterId);
        $ChapterModel->setRawAttributes($data);
        $ChapterModel->save();
        return $chapterId ? $chapterId : $ChapterModel->id;


    }


    public static function getModel($chapterId)
    {
        $model = false;
        if ($chapterId) {
            $model = static::find($chapterId);
        }
        !$model && $model = new static;
        return $model;
    }

}