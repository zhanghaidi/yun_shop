<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/17 上午11:03
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Modules\Love\Models;


class LoveActivationRecords extends \Yunshop\Love\Common\Models\LoveActivationRecords
{
    public function scopeRecords($query)
    {
        return $query->with(['member' => function($query) {
            $query->records();
        }]);
    }



}
