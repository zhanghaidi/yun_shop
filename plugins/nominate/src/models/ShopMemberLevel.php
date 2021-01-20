<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/15
 * Time: 4:14 PM
 */

namespace Yunshop\Nominate\models;


use app\common\models\MemberLevel;

class ShopMemberLevel extends MemberLevel
{
    public function nominateLevel()
    {
        return $this->hasOne(NominateLevel::class, 'level_id', 'id');
    }
}