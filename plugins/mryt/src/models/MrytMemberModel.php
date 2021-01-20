<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/26
 * Time: 下午5:42
 */

namespace Yunshop\Mryt\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Yunshop\Mryt\common\models\MemberReferralAward;
use Yunshop\Mryt\common\models\MemberTeamAward;
use Yunshop\Mryt\common\models\OrderParentingAward;
use Yunshop\Mryt\common\models\OrderTeamAward;
use Yunshop\Mryt\common\models\TierAward;

class MrytMemberModel extends BaseModel
{
    public $table = 'yz_mryt_member';

    protected $appends = [
        'order_team_total', 'team_total', 'thankful_total', 'parenting_total', 'referral_total', 'tier_total'
    ];

    public function getTierTotalAttribute()
    {
        return TierAward::select(['uid', 'amount'])
            ->where('uid', $this->uid)
            ->sum('amount');
    }

    public function getOrderTeamTotalAttribute()
    {
        return OrderTeamAward::select(['uid', 'amount'])
            ->where('uid', $this->uid)
            ->sum('amount');
    }

    public function getTeamTotalAttribute()
    {
        return MemberTeamAward::select(['uid', 'amount'])
            ->where('uid', $this->uid)
            ->where('award_type', 1)
            ->sum('amount');
    }

    public function getThankfulTotalAttribute()
    {
        return MemberTeamAward::select(['uid', 'amount'])
            ->where('uid', $this->uid)
            ->where('award_type', 2)
            ->sum('amount');
    }

    public function getParentingTotalAttribute()
    {
        return OrderParentingAward::select(['uid', 'amount'])
            ->where('uid', $this->uid)
            ->sum('amount');
    }

    public function getReferralTotalAttribute()
    {
        return MemberReferralAward::select(['uid', 'amount'])
            ->where('uid', $this->uid)
            ->sum('amount');
    }

    /**
     * 等级1:1关系
     *
     * @return mixed
     */
    public function hasOneLevel()
    {
        return $this->hasOne('Yunshop\Mryt\models\MrytLevelModel', 'id', 'level');
    }

    public function hasOneUpgradeSet()
    {
        return $this->hasOne('Yunshop\Mryt\models\MrytLevelUpgradeModel', 'level_id', 'level');
    }

    /**
     * 会员1:1关系
     *
     * @return mixed
     */
    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'uid');
    }

    public function hasManyChildren()
    {
        return $this->hasMany('app\common\models\member\ChildrenOfMember', 'member_id', 'uid');
    }


    public static function searchAgency($parame)
    {
        $result = self::uniacid();

        if (!empty($parame['realname'])) {
            $result = $result->whereHas('hasOneMember', function ($query) use ($parame) {
                $query->where('nickname', 'like', "%{$parame['realname']}%")
                    ->orWhere('realname', 'like', "%{$parame['realname']}%")
                    ->orWhere('mobile', 'like', "%{$parame['realname']}%");
            });
        }

        if (!empty($parame['level_id']) && $parame['level_id'] != -1) {
            $result = $result->where('level', $parame['level_id']);
        }

        if (isset($parame['searchtime']) && $parame['searchtime'] == 2) {
            if ($parame['times']['start'] != '请选择' && $parame['times']['end'] != '请选择') {
                $range = [strtotime($parame['times']['start']), strtotime($parame['times']['end'])];
                $result = $result->whereBetween('created_at', $range);
            }
        }

        $result = $result->with(['hasOneLevel' => function($query){
            return $query->select(['*']);
        },'hasOneMember' => function($query){
            return $query->select(['*']);
        }])->orderBy('id', 'desc');


        return $result;
    }

    public static function getMemberInfoByUid($uid)
    {
        return self::uniacid()
            ->with('hasOneLevel')
            ->with('hasOneUpgradeSet')
            ->where('uid', $uid)
            ->first();
    }

    public static function getMemberAutoWithdrawByUid($uid)
    {
        return self::uniacid()
            ->with('hasOneLevel')
            ->whereHas('hasOneLevel', function ($query) {
                $query->where('auto_withdraw',1)->where('withdraw_time','>','0');
            })
            ->where('uid', $uid)
            ->first();
    }

    public static function getMemberAutoWithdraw()
    {
        return self::uniacid()
            ->with('hasOneLevel')
            ->whereHas('hasOneLevel', function ($query) {
                $query->where('auto_withdraw',1)->where('withdraw_time','>','0');
            })
            ->get();
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });

    }

    public static function updatedLevelByMemberId($level_id, $member_id)
    {
        return self::uniacid()->where('uid', $member_id)->update(['level' => $level_id]);
    }

    public static function daletedAgency($id)
    {
        return self::where('id', $id)
            ->delete();
    }

    public static function getMemberInfosWithLevel(array $uids, $unaicid = 0)
    {
        \Log::debug('---------getMemberInfosWithLevel--------');
        return self::where('uniacid', $unaicid)
            ->where('level', '>', 0)
            ->whereIn('uid', $uids)
            ->get();
    }

    public static function updateContractOfMember(array $uid)
    {
        return self::uniacid()
                   ->whereIn('uid', $uid)
                   ->update(['status' => 1]);
    }

    public static function verify($uid) {
        return self::uniacid()->whereUserUid($uid)->first();
    }
}