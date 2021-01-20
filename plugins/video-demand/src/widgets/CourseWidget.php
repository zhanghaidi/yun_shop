<?php
namespace Yunshop\VideoDemand\widgets;
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/06
 * Time: 下午1:45
 */

use app\backend\modules\member\models\MemberLevel;
use app\common\components\Widget;
use Yunshop\VideoDemand\models\CourseChapterModel;
use Yunshop\VideoDemand\models\CourseGoodsModel;
use Yunshop\VideoDemand\models\LecturerModel;

class CourseWidget extends Widget
{
    public function run()
    {
        $item = CourseGoodsModel::getModel($this->goods_id,'');
        $item->see_levels = $item->see_levels != '' ? explode(',', $item->see_levels) : '';

        $lecturers = LecturerModel::getLecturerList()->get();
        $levels = MemberLevel::getMemberLevelList();
        $course_chapter = CourseChapterModel::getCourseChapterByCourseId($item->id)->get();
        return view('Yunshop\VideoDemand::admin.goods', [
            'item' => $item,
            'lecturers' => $lecturers,
            'levels' => $levels,
            'course_chapter' => json_encode($course_chapter),
        ])->render();
    }
}