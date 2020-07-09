<?php
/**
 * Created by PhpStorm.
 * Time: 下午5:30
 */

namespace Yunshop\Commission\admin\model;

class Agents extends \Yunshop\Commission\models\Agents
{

    // 数据同步，将会员表中已是代理的会员导入到yz_agents表
    public static function dataIdentical($members)
    {
        foreach ($members as $member) {
            $arrMember = $member->toArray();
            $arrAgent['uniacid'] = $arrMember['uniacid'];
            $arrAgent['parent_id'] = $arrMember['parent_id'];
            $arrAgent['member_id'] = $arrMember['member_id'];
            $arrAgent['parent'] = $arrMember['relation'];
            $arrAgent['created_at'] = $arrMember['agent_time'];
            $arrAgent['updated_at'] = $arrMember['agent_time'];
            $agent = new Agents();
            $agent->fill($arrAgent);
            $agent->save();
        }
    }
}