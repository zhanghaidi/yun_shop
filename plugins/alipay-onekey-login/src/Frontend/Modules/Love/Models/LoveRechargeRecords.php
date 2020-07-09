<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2018/11/28
 * Time: 6:09 PM
 */

namespace Yunshop\Love\Frontend\Modules\Love\Models;


class LoveRechargeRecords extends \Yunshop\Love\Common\Models\LoveRechargeRecords
{
    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'uniacid'       => "required",
            'member_id'     => "required",
            'change_value'  => 'numeric|min:0',
            'type'          => [
                'required',
                'regex:/^(1|2|28|29)$/'
            ],
            'order_sn'      => 'required',
            'status'        => 'required',
            'value_type'    => 'required',
            'remark'        => 'max:50'
        ];
    }
}
