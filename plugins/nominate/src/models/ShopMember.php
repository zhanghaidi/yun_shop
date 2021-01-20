<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/15
 * Time: 4:16 PM
 */

namespace Yunshop\Nominate\models;


use app\common\models\Member;
use app\common\models\MemberShopInfo;

class ShopMember extends MemberShopInfo
{
    public static function getList($search)
    {
        return self::select()
            ->with(['shopMemberLevel'])
            ->with(['parent', function ($parent) {
                $parent->select('uid', 'avatar', 'nickname');
            }])
            ->with(['hasOneMember', function ($member) {
                $member->select('uid', 'avatar', 'nickname');
            }])
            ->byAgents()
            ->byLevelIdNotNull()
            ->search($search);
    }

    public function scopeSearch($query, $search)
    {
        if ($search['member']) {
            $query->whereHas('hasOneMember', function ($member) use ($search) {
                $member->where('nickname', 'like', "%{$search['member']}%")
                    ->orWhere('realname', 'like', "%{$search['member']}%")
                    ->orWhere('mobile', 'like', "%{$search['member']}%");
            });
        }
        if ($search['uid']) {
            $query->where('member_id', $search['uid']);
        }
        if ($search['level_id'] > 0) {
            $query->where('level_id', $search['level_id']);
        }
        return $query;
    }

    public function scopeByLevelIdNotNull($query)
    {
        return $query->where('level_id', '>', 0);
    }

    public function scopeByAgents($query)
    {
        return $query->where('is_agent', 1)->where('status', 2);
    }

    public function shopMemberLevel()
    {
        return $this->hasOne(ShopMemberLevel::class, 'id', 'level_id');
    }

    public function parent()
    {
        return $this->hasOne(Member::class, 'uid', 'parent_id');
    }
}