<?php

namespace Yunshop\VideoDemand\models;

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/06
 * Time: 下午1:54
 */
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Yunshop\VideoDemand\services\LecturerRewardLogService;

class LecturerRewardLogModel extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_video_lecturer_reward_log';
    public $timestamps = true;
    protected $guarded = [''];

    public $RewardTypeName;
    public $StatusName;
    protected $appends = ['reward_type_name', 'status_name'];

    public static function getLecturerRewardList($search)
    {
        $model = self::uniacid();

        if (!empty($search['lecturer'])) {
            $model->whereHas('hasOneLecturer', function ($query) use ($search) {
                $query->searchLike($search['lecturer']);
                return $query;
            });
        }
        $model->with(['hasOneLecturer' => function ($query) {
            $query->with(['hasOneMember' => function ($memberQuery) {
                return $memberQuery->select('uid', 'mobile', 'realname', 'nickname', 'avatar');
            }]);
            return $query;
        }]);

        if (!empty($search['course_goods'])) {
            $model->whereHas('hasOneCourse', function ($query) use ($search) {
                $query->whereHas('hasOneGoods', function ($query) use ($search) {
                    return $query->searchLike($search['course_goods']);
                });
                return $query;
            });
        }
        $model->with(['hasOneCourse' => function ($query) {
            $query->with(['hasOneGoods' => function ($GoodsQuery) {
                return $GoodsQuery->select('id', 'title', 'thumb');
            }]);
            return $query;
        }]);

        $model->with(['hasOneRewardMember' => function ($memberQuery) {
            return $memberQuery->select('uid', 'realname', 'nickname', 'avatar');
        }]);

        if ($search['order_sn']) {
            $model->where('order_sn', 'like', '%' . $search['order_sn'] . '%');
        }

        if ($search['reward_type'] != '') {
            $model->where('reward_type', $search['reward_type']);
        }
        if ($search['status'] != '') {
            $model->where('status', $search['status']);
        }
        if ($search['is_time']) {
            if ($search['time']) {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $model->whereBetween('created_at', $range);
            }
        }
        return $model;
    }


    public static function getRewardLogByLecturerId($lecturerId)
    {
        return self::uniacid()
            ->where('lecturer_id', $lecturerId);
    }

    public function scopeAccountInfo($query, $account_id = unll)
    {
        $query = $query->select(['order_sn', 'amount', 'reward_type', 'status', 'created_at'])->orderBy('created_at', 'desc');

        $query = $query->where(function($query1) use($account_id) {

            if (!empty($account_id) || $account_id === "0") {
                return $query1->where('status', $account_id);
            }
        });

        return $query;
    }


    public function getRewardTypeNameAttribute()
    {
        if (!isset($this->RewardTypeName)) {
            $this->RewardTypeName = LecturerRewardLogService::getRewardTypeName($this);
        }
        return $this->RewardTypeName;
    }

    public function getStatusNameAttribute()
    {
        if (!isset($this->StatusName)) {
            $this->StatusName = LecturerRewardLogService::getStatusName($this);
        }
        return $this->StatusName;
    }


    public function hasOneLecturer()
    {
        return $this->hasOne('Yunshop\VideoDemand\models\LecturerModel', 'id', 'lecturer_id');
    }

    public function hasOneRewardMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }


    public function hasOneCourse()
    {
        return $this->hasOne('Yunshop\VideoDemand\models\CourseGoodsModel', 'id', 'course_id');
    }

    public static function getStatement()
    {
        return self::uniacid()
            ->where(function ($query) {
                return $query->where(DB::raw('`created_at` + (`settle_days` * 86400)'), '<=', time())
                    ->orWhere('settle_days', '=', '0');
            })
            ->where('status', '0');
    }

    public static function hasLecturerRewardLog($ordersn)
    {
        return self::uniacid()
            ->where('order_sn', $ordersn);
    }

}