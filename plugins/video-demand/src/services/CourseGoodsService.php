<?php

namespace Yunshop\VideoDemand\services;


use app\common\models\MemberShopInfo;
use app\common\models\OrderGoods;
use app\common\models\MemberLevel;
use Yunshop\VideoDemand\models\CourseChapterModel;
use Yunshop\VideoDemand\models\MemberCourseModel;

class CourseGoodsService
{
    public static function saveCourseChapter($courseId, $chapter)
    {
        foreach ($chapter['chapter_id'] as $key => $item) {
            $data = [
                'uniacid' => \YunShop::app()->uniacid,
                'course_id' => $courseId,
                'chapter_name' => $chapter['chapter_name'][$key],
                'video_address' => $chapter['video_address'][$key],
                'is_audition' => $chapter['is_audition'][$key],
            ];
            $chapter_ids[] = CourseChapterModel::relationSave($item, $data);
        }
        CourseChapterModel::uniacid()->where('course_id', $courseId)->whereNotIn('id', $chapter_ids)->delete();
    }

    /**
     * @param $videoGoods
     * @param string $chapterId
     * @return int 0:未获得观看权限 1:免费试看、2:等级权限、3:购买权限
     *
     */
    public static function validateIsWatch($videoGoods, $chapterId = '')
    {
        /**
         * 试看章节
         */
        $auditionWatch = static::getAuditionWatch($videoGoods, $chapterId);
        if ($auditionWatch) {
            return $auditionWatch;
        }

        /**
         *  等级权限
         */
        $memberLevelWatch = static::getMemberLevelWatch($videoGoods->see_levels);
        if ($memberLevelWatch) {
            return $memberLevelWatch;
        }
        /**
         *  购买权限
         */
        $buyWatch = static::getBuyWatch($videoGoods->goods_id);
        if ($buyWatch) {
            return $buyWatch;
        }

        return 0;
    }

    /**
     * @param $videoGoods
     * @param $chapterId
     * @return int
     */
    public static function getAuditionWatch($videoGoods, $chapterId)
    {
        $audition = 0;
        foreach ($videoGoods->hasManyCourseChapter as $chapter) {
            if ($chapterId) {
                if ($chapterId == $chapter->id) {
                    $audition = $chapter->is_audition;
                    break;
                }
            } else {
                $audition = $chapter->is_audition;
                break;
            }
        }
        return $audition;
    }

    /**
     * @param $seeLevels
     * @return int
     */
    public static function getMemberLevelWatch($seeLevels)
    {
        if (empty($seeLevels) && $seeLevels !== "0") {
            return 2;
        }
        $level_id = MemberShopInfo::whereMemberId(\YunShop::app()->getMemberId())->value('level_id');

        $see_levels = explode(',', $seeLevels);
        if (in_array($level_id, $see_levels)) {
            return 2;
        }

        /**
         * update 2018/1/2
         * 等级高以设置条件等级的都可看
         */
        if ($level_id) {
            $memberLevel = MemberLevel::find($level_id)->value('level');
            $levels = MemberLevel::whereIn('id', $see_levels)->max('level');
            if ($memberLevel > $levels) {
                return 2;
            }
        }

        return 0;
    }

    public static function getLevelNames($seeLevels)
    {
        if (empty($seeLevels) && $seeLevels !== "0") {
            return array();
        }

        $see_levels = explode(',', $seeLevels);

        $levelNames = MemberLevel::select('level_name')->whereIn('id', $see_levels)->get();
        
        return $levelNames->toArray();
    }


    /**
     * @param $goodsId
     * @return int
     */
    public static function getBuyWatch($goodsId)
    {
        // $orderGoods = OrderGoods::uniacid()
        // ->where('uid',\YunShop::app()->getMemberId())
        // ->where('goods_id',$goodsId)
        // ->first();

        $memberCourse = MemberCourseModel::uniacid()
        ->where('member_id',\YunShop::app()->getMemberId())
        ->where('goods_id',$goodsId)
        ->first();

        if ($memberCourse) {
            return 3;
        }
        return 0;
    }


    /**
     * @param $goodsRewardStatus
     * @return int
     * 打赏权限
     */
    public static function validateIsReward($goodsRewardStatus)
    {
        $set = \Setting::get('plugin.video_demand');
        if ($set['is_reward']) {
            return $goodsRewardStatus;
        }
        return 0;
    }


}