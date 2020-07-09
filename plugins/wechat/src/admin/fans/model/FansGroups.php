<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace Yunshop\Wechat\admin\fans\model;



class FansGroups extends \Yunshop\Wechat\common\model\FansGroups
{
    public function getGroupsAttribute($value)
    {
        return unserialize($value);
    }

    public function setGroupsAttribute($value)
    {
        $this->attributes['groups'] = serialize($value);
    }

}