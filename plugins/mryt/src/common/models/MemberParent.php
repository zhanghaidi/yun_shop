<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/26
 * Time: 5:15 PM
 */

namespace Yunshop\Mryt\common\models;


use app\common\models\member\MemberParent as ShopMemberParent;
use Yunshop\Mryt\models\MrytMemberModel;

class MemberParent extends ShopMemberParent
{
    public function hasOneMrytMember()
    {
        return $this->hasOne(MrytMemberModel::class, 'uid', 'parent_id');
    }
}