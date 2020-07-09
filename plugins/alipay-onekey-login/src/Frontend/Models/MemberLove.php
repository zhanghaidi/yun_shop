<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/14
 * Time: 下午2:09
 */

namespace Yunshop\Love\Frontend\Models;


class MemberLove extends \Yunshop\Love\Common\Models\MemberLove
{

    /**
     * 获取最多可用爱心值
     * @return mixed
     */
    public function getMaxUsablePoint()
    {
        return $this->usable;
    }

}