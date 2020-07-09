<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\admin\fans\model;


class Fans extends \Yunshop\Wechat\common\model\Fans
{

    public function hasOneMember()
    {
        return $this->hasMany(Member::class,'uid','uid')->select('uid','mobile','avatar','nickname','email');
    }

    public function hasManyGroups()
    {
        return $this->hasMany(FansGroups::class,'uid','uid')->select('uid');
    }

    public function hasOneFansTagMapping()
    {
        return $this->hasOne(FansTagMapping::class,'fanid','fanid');
    }

    public function getGroupidAttribute($value)
    {
        return explode(',',$value);
    }

    public function setGroupidAttribute($value)
    {
        $this->attributes['groupid'] = implode(',', $value);
    }
}