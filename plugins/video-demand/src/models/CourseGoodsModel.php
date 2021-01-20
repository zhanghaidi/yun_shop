<?php

namespace Yunshop\VideoDemand\models;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/06
 * Time: 下午1:54
 */
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Yunshop\VideoDemand\services\CourseGoodsService;
use app\common\traits\MessageTrait;
use Illuminate\Support\Facades\DB;

class CourseGoodsModel extends BaseModel
{
    use SoftDeletes, MessageTrait;
    public $table = 'yz_video_course_goods';
    public $timestamps = true;
    protected $guarded = [''];

    public $attributes = [
        'is_course' => 0,
        'lecturer_id' => 0,
        'is_reward' => 0,
    ];

    public static function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }
        $chapter = $data['chapter'];

        $CourseModel = self::getModel($goodsId, $operate);
        $courseId = $CourseModel->id;

        if ($data['is_course'] != 0) {
            $lecturer = LecturerModel::find($data['lecturer_id']);
            
            $data['lecturer_name'] = $lecturer->real_name;
        }
        //判断deleted
        if ($operate == 'deleted') {
            return $CourseModel->delete();
        }
        $data['goods_id'] = $goodsId;
        $data['uniacid'] = \YunShop::app()->uniacid;
        $data['lecturer_id'] = $data['lecturer_id'] ? $data['lecturer_id'] : 0;
        $data['see_levels'] = !empty($data['see_levels']) ? implode(',', $data['see_levels']) : '';
        $data['goods_title'] = \YunShop::request()->goods['title'];

        unset($data['chapter']);
        $CourseModel->setRawAttributes($data);

        $request = $CourseModel->save();

        if ($request) {
            if (!$courseId) {
                $courseId = $CourseModel->id;
            }
            if ($chapter) {
                CourseGoodsService::saveCourseChapter($courseId, $chapter);
            }

        }
        return $request;
    }

    public static function getModel($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = static::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model = new static;
        return $model;
    }

    public static function getCourseGoods($goodsType = '', $search)
    {
        $model = self::where('yz_video_course_goods.uniacid', \Yunshop::app()->uniacid);

        // $model->select('id', 'goods_id', 'lecturer_id');

        /**
         * 查询商品与课程数据
         * update date 2017/12/28 17:32
         * Author: blank
         */
        $model->select('yz_video_course_goods.id', 'yz_video_course_goods.goods_id', 'yz_video_course_goods.lecturer_id' , 'yz_goods.title', 'yz_goods.thumb', 'yz_goods.price');

        /**
         * 关联商品
         */
        if ($goodsType) {
            $model->whereHas('hasOneGoods', function ($query) use ($goodsType) {
                switch ($goodsType) {
                    case 'is_recommand':
                        return $query->where('is_recommand', 1);
                        break;
                    case 'is_hot':
                        return $query->where('is_hot', 1);
                        break;
                    case 'is_new':
                        return $query->where('is_new', 1);
                        break;
                }
            });
        }

        $model->whereHas('hasOneGoods', function ($query) {
            return $query->where('status', 1);
        });

        /*$model->with(['hasOneGoods' => function ($query) {
            return $query->select('id', 'title', 'thumb', 'price');
        }]);*/


        /**
         * 关联商品表
         * Author: blank
         */
        $model->Join('yz_goods', 'yz_goods.id', '=', 'yz_video_course_goods.goods_id');
        


        /**
         * 关联讲师
         */
        $model->with(['hasOneLecturer' => function ($query) {
            return $query->select('id', 'real_name', 'mobile');
        }]);
        /**
         * 关联章节
         */
        $model->with(['hasManyCourseChapter' => function ($query) {

        }]);

        $model->where('is_course', 1);

        if ($search['goods']) {
            $model->where('goods_title', 'like', '%' . $search['goods'] . '%');
            $model->orWhere('lecturer_name', 'like', '%' . $search['goods'] . '%');
        }

        return $model;
    }


    public static function getCourseGoodsDetail($goodsId)
    {
        $model = self::uniacid();
        $model->select('id', 'goods_id', 'is_course', 'lecturer_id', 'is_reward', 'see_levels');

        $model->whereHas('hasOneGoods', function ($query) use ($goodsId) {
            return $query->where('id', $goodsId);
        });
        $model->with(['hasOneGoods' => function ($query) {
            return $query->select('id', 'title', 'thumb', 'price', 'content')->with('hasOneShare');
        }]);
        /**
         * 关联章节
         */
        $model->with(['hasManyCourseChapter' => function ($query) {
            return $query->select('id', 'chapter_name', 'course_id', 'video_address', 'is_audition');
        }]);

        /**
         * 关联讲师
         */
        $model->with(['hasOneLecturer' => function ($query) {
            return $query->select('id', 'member_id', 'real_name', 'mobile')
                ->with(['hasOneMember' => function ($query) {
                    return $query->select('uid', 'avatar');
                }]);
        }]);

        $model->where('is_course', 1);
        return $model;
    }
    /**
     * 获取课程商品id，判断显示跳转链接
     * @return [array] 课程商品id集合
     */
    public static function getCourseGoodsIdsData()
    {
        return self::uniacid()->select('goods_id')->where('is_course', 1)->get();
    }

    public function hasOneGoods()
    {
        return $this->hasOne('app\common\models\Goods', 'id', 'goods_id');
    }

    public function hasOneLecturer()
    {
        return $this->hasOne('Yunshop\VideoDemand\models\LecturerModel', 'id', 'lecturer_id');
    }

    public function hasManyCourseChapter()
    {
        return $this->hasMany('Yunshop\VideoDemand\models\CourseChapterModel', 'course_id', 'id');
    }


    public static function relationValidator($goodsId, $data, $operate)
    {
        $flag = false;
        $model = new static;
        $validator = $model->validator($data);

        if ($validator->fails()) {
            $model->error($validator->messages());
//            $model->error($validator->messages());
        } else {
            $flag = true;
        }
        return $flag;
    }

    /**
     *  定义字段名
     * 可使
     * @return array
     */
    public function atributeNames()
    {
        // return [
        //     'lecturer_id' => '课程讲师',
        //     'chapter' => '课程章节',
        // ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        // return [
        //     'lecturer_id' => 'required',
        //     'chapter' => 'required',
        // ];
    }

    /**
     * 商品是否开启课程
     *
     * @param $goods_id
     * @param $status
     * @return mixed
     */
    public static function checkCourse($goods_id, $status)
    {
        return self::uniacid()
            ->where('goods_id', $goods_id)
            ->where('is_course', $status);
    }
}