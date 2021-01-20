<?php
/**
 * Create Date 2017/12/18 17:11
 */

namespace Yunshop\VideoDemand\models;


class MemberModel extends \app\common\models\Member
{

    /**
     * 获取用户基本信息
     * @param  int $uid 用户id
     * @return array   $data
     */
    public function getData($uid)
    {
        $manyArr = self::uniacid()->select(['uid', 'nickname', 'avatar'])
            ->with(['yzMember' => function ($query) use ($uid) {
                return $query->select('member_id', 'level_id')
                    ->with(['level' => function ($query2) {
                        return $query2->select(['id', 'level_name']);
                    }]);
            }])->find($uid)->toArray();


        return $manyArr;
    }

    /**
     * 讲师头像
     */
    public static function LecturerInfo($uid)
    {
        return self::uniacid()->select(['avatar'])->where('uid', $uid)->first();
    }

}


