<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/30 上午11:21
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Backend\Modules\Love\Models;


use Yunshop\Love\Common\Models\Member;

class LoveRecords extends \Yunshop\Love\Common\Models\LoveRecords
{
    /**
     * 获取该公众号所有爱心值变动明细（附带会员信息）
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeRecords($query)
    {
        /**
         * @var $query LoveRecords
         */
        return $query->with(['member' => function($query) {
            /**
             * @var $query Member
             */
            $query->records()->withLove();
        }]);
    }

}
