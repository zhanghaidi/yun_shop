<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/9/21
 * Time: 上午11:50
 */

namespace Yunshop\Love\Common\Models;


use app\common\models\MemberGroup;
use app\common\models\MemberLevel;
use app\common\models\MemberShopInfo;

class YzMemberModel extends MemberShopInfo
{
    public $levelName;
    public $groupName;
    protected $appends = ['level_name','group_name'];

    public function getLevelNameAttribute()
    {
        if (!isset($this->levelName)) {
            $this->levelName = MemberLevel::where('id',$this->level_id)->pluck('level_name')->first();
        }
        return $this->levelName;
    }
    public function getGroupNameAttribute()
    {
        if (!isset($this->groupName)) {
            $this->groupName = MemberGroup::where('id',$this->group_id)->pluck('group_name')->first();
        }
        return $this->groupName;
    }
}