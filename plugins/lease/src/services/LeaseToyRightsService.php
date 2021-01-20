<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/29
 */

namespace Yunshop\LeaseToy\services;

use Yunshop\LeaseToy\models\MemberModel;
use Yunshop\LeaseToy\models\RightsLogModel;
use Yunshop\LeaseToy\models\LevelRightsModel;

class LeaseToyRightsService
{
    
    static public function getMemberRights($memberId)
    {
        //用户等级
        $member_level = MemberModel::getLevel($memberId);
        //等级权益
        $level = LevelRightsModel::getRights($member_level);

        $data = [
            'rent_free' => !empty($level) ? $level->rent_free : 0,
            'deposit_free' => !empty($level) ? $level->deposit_free : 0,
        ];
        //使用过权益的件数
        $rightsLog = RightsLogModel::getRightsLog($memberId);

        //剩余件数
        if ($rightsLog) {
            $data['rent_free'] = max($level->rent_free - $rightsLog['sue_rent_free'], 0);
            $data['deposit_free'] =  max($level->deposit_free - $rightsLog['sue_deposit_free'], 0);
        }
        
        return $data;
    }


}