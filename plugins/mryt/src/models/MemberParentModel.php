<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/12/13
 * Time: 13:56
 */

namespace Yunshop\Mryt\models;


use app\common\models\Member;
use app\common\models\member\MemberParent;

class MemberParentModel extends MemberParent
{
    public static function getDirectMemberByMemberId($member_id)
    {
        return parent::uniacid()
            ->where('parent_id', $member_id)
            ->where('level',1)
            ->whereHas('hasOneMrytMember');
    }

    public static function getTeamMemberByMemberId($member_id)
    {
        return parent::uniacid()
            ->where('parent_id', $member_id)
            ->whereHas('hasOneMrytMember');
    }

    public static function getDirectLevelMember($member_id ,$level)
    {
        return parent::uniacid()
            ->where('parent_id', $member_id)
            ->where('level',1)
            ->whereHas('hasOneMrytMember', function ($q) use($level) {
                $q->where('level', $level);
            });
    }

    public static function getTeamLevelMember($member_id, $level)
    {
        return parent::uniacid()
            ->where('parent_id', $member_id)
            ->whereHas('hasOneMrytMember', function ($q) use($level) {
                $q->where('level', $level);
            });
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class,'uid', 'member_id');
    }


    public function hasOneMrytMember()
    {
        return $this->hasOne(MrytMemberModel::class, 'uid', 'member_id');
    }


}