<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/05/20
 * Time: 下午4:00
 */

namespace Yunshop\Commission\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CommissionManage extends BaseModel
{

    use SoftDeletes;
    public $table = 'yz_commission_manage';
    public $timestamps = true;
    protected $guarded = [''];

    public $StatusService;
    protected $appends = ['status_name'];

    public static function getManages($search)
    {
        $Model = self::uniacid();
        if ($search['member']) {
            $Model->whereHas('hasOneMember', function ($query) use ($search) {
                return $query->searchLike($search['member']);
            });
        }
        $Model->with(['hasOneMember' => function ($query) {
            return $query->select('uid', 'mobile', 'nickname', 'avatar');
        }]);
        $Model->with(['hasOneSubordinate' => function ($query) {
            return $query->select('uid', 'mobile', 'nickname', 'avatar');
        }]);
        if($search['status'] != ''){
            $Model->where('status',$search['status']);
        }
        if($search['hierarchy'] != ''){
            $Model->where('hierarchy',$search['hierarchy']);
        }
        return $Model;
    }

    public static function getManageByMemberId()
    {
        return self::uniacid()
            ->with(['hasOneMember' => function ($query) {
                return $query->select('uid', 'mobile', 'nickname', 'avatar');
            }])
            ->with(['hasOneSubordinate' => function ($query) {
                return $query->select('uid', 'mobile', 'nickname', 'avatar');
            }])
            ->where('member_id', \YunShop::app()->getMemberId());
    }


    public function getStatusNameAttribute()
    {
        if (!isset($this->StatusService)) {

            switch ($this->status) {
                case 0:
                    $this->StatusService = '未提现';
                    break;
                case 1:
                    $this->StatusService = '已提现';
                    break;
            }
        }
        return $this->StatusService;
    }

    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }

    public function hasOneSubordinate()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'subordinate_id');
    }

    public static function updatedWithdraw($data, $where)
    {
        return self::uniacid()
            ->where($where)
            ->update($data);
    }

}