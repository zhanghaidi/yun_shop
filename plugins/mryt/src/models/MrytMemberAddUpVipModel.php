<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/11/4
 * Time: 下午4:29
 */

namespace Yunshop\Mryt\models;


use app\common\models\BaseModel;
use app\common\models\Member;
use app\framework\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class MrytMemberAddUpVipModel extends BaseModel
{
    public $table = 'yz_mryt_vip_addup';

    private $uniacid = 0;

    public function __construct($uniacid = 0, array $attributes = [])
    {
        $this->uniacid = $uniacid;

        parent::__construct($attributes);
    }

    public function scopeUniacid(Builder $query)
    {
        if (0 == $this->uniacid && \YunShop::app()->uniacid !== null) {
            $this->uniacid = \YunShop::app()->uniacid;
        }

        return $query->where($this->getTable() . '.uniacid', $this->uniacid);
    }

    public function hasOneMember()
    {
        return $this->hasOne('Yunshop\Mryt\models\MrytMemberModel', 'uid', 'uid');
    }

    public function hasOneMcMember()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public function CreateData($data)
    {
        $rs = DB::table($this->getTable())->insert($data);

        return $rs;
    }

    public function UpdateIncrementNums($uid, $curr_date)
    {
        $rs = DB::table($this->getTable())
            ->where('curr_date', $curr_date)
            ->whereIn('uid', $uid)
            ->increment('nums');

        return $rs;
    }

    public function QueryCurrentMonthRecord(array $ids, $curr_month)
    {
        return self::uniacid()
            ->where('curr_date', $curr_month)
            ->whereIn('uid', $ids)
            ->get();
    }

    public static function getList($params)
    {
        $result = self::whereHas('hasOneMember', function ($query) use ($params) {
            if (isset($params['level']) && $params['level'] != -1) {
                $query->where('level', $params['level']);
            }
        });

        if (isset($params['name']) && !empty($params['name'])) {
        $result = $result->whereHas('hasOneMcMember', function ($query) use ($params) {
                $query->where('nickname', 'like', $params['name'].'%')
                    ->orWhere('realname', 'like', $params['name'].'%')
                    ->orWhere('mobile', 'like', $params['name'].'%');
        });
        }

        $result = $result->with(['hasOneMember' => function ($query) {
            $query->with('hasOneLevel');
        }, 'hasOneMcMember' => function ($query) use ($params) {
            $query->select('uid', 'nickname', 'avatar');
        }]);

        if (!empty($params['member'])) {
           $result->where('uid', $params['member']);
        }

        $result->whereIn('curr_date', $params['search_month']);

        return $result;
    }

    /**
     * 获取前3月达标的会员
     * @param array $uid
     * @param array $months
     * @return mixed
     */
    public static function getMemberForReachTheStandard(array $level, array $months, $nums)
    {
        return self::uniacid()
                  ->select(DB::raw('count(1) as total, uid'))
                  ->where('nums', $nums)
                  ->whereIn('curr_date', $months)
                  ->with(['hasOneMember' => function ($query) use ($level) {
                          $query->select(['uid', 'status'])
                                ->with(['hasOneLevel' => function ($query) use ($level) {
                              $query->whereIn('id', $level);
                          }])
                          ->where('status', 0);
                  }])
                  ->groupBy('uid')
                  ->get();
    }
}