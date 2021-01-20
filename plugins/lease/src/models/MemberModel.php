<?php
/**
 * Create 2018/3/3 
 * Date 9:54
 */

namespace Yunshop\LeaseToy\models;


class MemberModel extends \app\common\models\Member
{

    /**
     * 获取用户基本信息
     * @param  int $uid 用户id
     * @return array   $data
     */
    public static function getLevel($uid)
    {
        $manyArr = self::uniacid()->select(['uid', 'nickname', 'avatar'])
            ->with(['yzMember' => function ($query) use ($uid) {
                return $query->select('member_id', 'level_id');
            }])->find($uid);

        return $manyArr->yzMember->level_id;
    }
}


