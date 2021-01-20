<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/19
 * Time: 上午11:42
 */

namespace Yunshop\VideoDemand\api;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\services\goods\SaleGoods;
use Yunshop\VideoDemand\models\CourseChapterModel;
use Yunshop\VideoDemand\models\CourseGoodsModel;
use Yunshop\VideoDemand\services\CourseGoodsService;

class VideoCourseGoodsController extends ApiController
{
    public $set;
    public $uid;

    protected $pageSize = 20;

    public function __construct()
    {
        parent::__construct();
        $this->set = Setting::get('plugin.video_demand');
        $this->uid = \YunShop::app()->getMemberId();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 课程商品列表
     */
    public function getCourseGoods()
    {
        // plugin.video-demand.api.video-course-goods.get-course-goods
        /**
         * goods_type (is_recommand is_hot is_new)
         * in_home_page (1:聚合页)
         * search 搜索内容
         */
        $goods_type = \YunShop::request()->goods_type;
        $in_home_page = \YunShop::request()->in_home_page;

        $search = \YunShop::request()->search;


        $courseGoodsModel = CourseGoodsModel::getCourseGoods($goods_type, ['goods' => $search]);

        /**
         * 在聚合页显示
         */
        if ($in_home_page) {
            $this->pageSize = 4;
        }
        // $courseGoodsData = $courseGoodsModel->paginate($this->pageSize)->toArray();

        // foreach ($courseGoodsData['data'] as &$course) {
        //     $course['has_one_goods']['thumb'] = replace_yunshop(yz_tomedia($course['has_one_goods']['thumb']));
        //     $course['course_chapter_num'] = count($course['has_many_course_chapter']);
        //     unset($course['has_many_course_chapter']);
        // }

        /**
         * 添加排序
         * update date 2017/12/28 17:28
         * Author: blank
         */
        $courseGoodsData = $courseGoodsModel->orderBy('yz_goods.display_order', 'desc')->orderBy('yz_video_course_goods.created_at', 'desc')->paginate($this->pageSize)->toArray();

        foreach ($courseGoodsData['data'] as &$course) {
            $course['thumb'] = replace_yunshop(yz_tomedia($course['thumb']));
            $course['course_chapter_num'] = count($course['has_many_course_chapter']);
            unset($course['has_many_course_chapter']);
        }
        //
        
        if ($courseGoodsData) {
            return $this->successJson('成功', $courseGoodsData);
        }
        return $this->errorJson('未检测到数据!', $courseGoodsData);
    }

    public function getCourseGoodsDetail()
    {
        // plugin.video-demand.api.video-course-goods.get-course-goods-detail
        /**
         * goods_id 商品ID
         * chapter_id 章节ID (非必填 默认第一个章节)
         */

        $goods_id = \YunShop::request()->goods_id;
        $chapter_id = \YunShop::request()->chapter_id;

        $detail = CourseGoodsModel::getCourseGoodsDetail($goods_id)->first();

        if (!$detail->is_course) {
            return $this->errorJson('该商品未开启课程点播功能!', []);
        }

        // 课程图片
        $detail->hasOneGoods->thumb = replace_yunshop(yz_tomedia($detail->hasOneGoods->thumb));
        // 课程详情
        $detail->hasOneGoods->content = html_entity_decode($detail->hasOneGoods->content);

        //分享关注
        if ($detail->hasOneGoods->hasOneShare) {
            $detail->hasOneGoods->hasOneShare->share_thumb = yz_tomedia($detail->hasOneGoods->hasOneShare->share_thumb);

        }
        // 课程章节数量
        $detail->course_chapter_num = count($detail->hasManyCourseChapter);
        // 讲师会员头像图片
        $detail->hasOneLecturer->hasOneMember->avatar = replace_yunshop(yz_tomedia($detail->hasOneLecturer->hasOneMember->avatar));

        /**
         *  验证观看权限
         * 1、章节免费试看
         * 2、等级权限
         * 3、购买权限
         */
        $detail->watch = CourseGoodsService::validateIsWatch($detail, $chapter_id);

        /**
         * 可看会员等级名称
         */
        $detail->level_names = CourseGoodsService::getLevelNames($detail->see_levels);

        /**
         *  验证是否打赏
         * 1、插件设置
         * 2、课程商品设置
         */
        $detail->is_reward = CourseGoodsService::validateIsReward($detail->is_reward);
        
        $detail->pushGoods = SaleGoods::getPushGoods($detail->goods_id);

        if (count($detail->pushGoods) > 4) {
            $detail->pushGoods = array_slice($this->shuffle_assoc($detail->pushGoods), 0, 4);
        }
        if ($detail) {
            return $this->successJson('成功', $detail);
        }
        return $this->errorJson('未检测到数据!', $detail);
    }

    public function getVideoAddress()
    {
        // plugin.video-demand.api.video-course-goods.get-video-address
        /**
         * chapter_id 章节ID
         */
        $chapter_id = \YunShop::request()->chapter_id;
        $data = CourseChapterModel::getModel($chapter_id);

        if ($data) {
            return $this->successJson('成功', $data->video_address);
        }
        return $this->errorJson('未检测到数据!', $data);

    }

    /**
     * 打乱二维数组
     * @param  [type] $list [description]
     * @return [type]       [description]
     */
    function shuffle_assoc($list) { 
        if (!is_array($list)) return $list; 
        $keys = array_keys($list); 
        shuffle($keys); 

        $random = array(); 

        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        } 
        return $random; 
    } 


}