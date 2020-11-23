<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/10
 * Time: 下午2:24
 */

namespace app\common\models;


class MemberQyWechatModel extends BaseModel
{
    public $table = 'yz_member_qywechat';

    /**
     * 删除会员信息
     *
     * @param $id
     */
    public static function  deleteMemberInfoById($id)
    {
        return self::uniacid()
            ->where('member_id', $id)
            ->delete();
    }
}