<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/18
 * Time: 5:29 PM
 */

namespace Yunshop\Nominate\models;


class MemberParent extends \app\common\models\member\MemberParent
{
    public function shopMember()
    {
        return $this->hasOne(ShopMember::class, 'member_id', 'parent_id');
    }
}