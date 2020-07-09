<?php
/**
 * Created by PhpStorm.
 * Date: 2017/3/17
 * Time: 下午12:04
 */

namespace Yunshop\Commission\admin\model;
use Illuminate\Support\Facades\DB;

class MemberShopInfo extends \app\common\models\MemberShopInfo
{
    // 查出该公众号下所有成为推广员的会员，并且这些会员不在yz_agents表里的会员
    public static function getAgentMembers()
    {
        return self::select(['yz_member.uniacid','yz_member.parent_id','yz_member.member_id','yz_member.relation','yz_member.agent_time'])
            ->leftJoin('yz_agents', function ($join) {$join->on('yz_member.member_id', '=', 'yz_agents.member_id')
                ->on('yz_member.uniacid', '=', 'yz_agents.uniacid');})
            ->where('yz_member.status','=',2)
            ->where('yz_member.is_agent','=',1)
            ->where('yz_member.uniacid','=',\YunShop::app()->uniacid)
            ->whereNull('yz_agents.member_id')
            ->get();
    }

}