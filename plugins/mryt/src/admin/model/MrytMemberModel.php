<?php
/**
 * Created by PhpStorm.
 * Date: 2018/10/26
 * Time: 下午5:42
 */

namespace Yunshop\Mryt\admin\model;


class MrytMemberModel extends \Yunshop\Mryt\models\MrytMemberModel
{
    // 设置模型不可注入为空
    protected $guarded=[];
    // 数据同步，将会员表中已是代理的会员导入到yz_mryt_member表
    public static function dataIdentical($members)
    {
        foreach ($members as $member) {
            $arrMember = $member->toArray();
            $arrMrytMember['uniacid'] = $arrMember['uniacid'];
            $arrMrytMember['uid'] = $arrMember['member_id'];
            $arrMrytMember['realname'] = $arrMember['realname'];
            $arrMrytMember['mobile'] = $arrMember['mobile'];
            $arrMrytMember['created_at'] = $arrMember['agent_time'];
            $arrMrytMember['updated_at'] = $arrMember['agent_time'];
            $mrytMember = new MrytMemberModel();
            $mrytMember->fill($arrMrytMember);
            $mrytMember->save();
        }
    }
}