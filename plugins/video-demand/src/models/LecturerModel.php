<?php

namespace Yunshop\VideoDemand\models;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/06
 * Time: 下午1:54
 */
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class LecturerModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_video_lecturer';
    public $timestamps = true;
    protected $guarded = [''];

    protected $search_fields = ['mobile', 'real_name'];


    public static function getLecturerList($search)
    {
        $model = self::uniacid();
        if ($search['lecturer_id']) {
            $model->where('id', $search['lecturer_id']);
        }
        if (!empty($search['member'])) {
            $model->whereHas('hasOneMember', function ($query) use ($search) {
                return $query->searchLike($search['member']);
            });
        }
        $model->with('hasOneMember');
        $model->with('hasManyCourseGoods');
//        $model->with('hasManyLecturerReward');

        return $model;
    }

    /**
     * 讲师的信息
     * @return [type] [description]
     */
    public function getLecturer()
    {

    }


    public static function getLecturerByMemberId($memberId)
    {
        return self::uniacid()
            ->where('member_id', $memberId);
    }


    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }

    public function hasManyCourseGoods()
    {
        return $this->hasMany('Yunshop\VideoDemand\models\CourseGoodsModel', 'lecturer_id', 'id');
    }
//
//    public function hasManyLecturerReward()
//    {
//        return $this->hasMany('Yunshop\VideoDemand\models\LecturerRewardLogModel', 'lecturer_id', 'id');
//    }

    /**
     *  定义字段名
     * 可使
     * @return array
     */
    public function atributeNames()
    {
        return [
            'member_id' => '会员',
            'real_name' => '真实姓名',
            'mobile' => '手机',
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'member_id' => 'required',
            'real_name' => 'required',
            'mobile' => 'required',
        ];
    }

}