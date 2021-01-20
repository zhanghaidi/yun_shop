<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/17
 * Time: 11:08 AM
 */

namespace Yunshop\Nominate\models;


use app\common\models\member\ChildrenOfMember;

class MemberChild extends ChildrenOfMember
{
    public function shopMember()
    {
        return $this->hasOne(ShopMember::class, 'member_id', 'child_id');
    }
}