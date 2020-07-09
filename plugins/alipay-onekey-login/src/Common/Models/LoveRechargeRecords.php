<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/24 上午9:21
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;
use app\common\traits\CreateOrderSnTrait;

/**
 * @property int $member_id
 * @property float $money
 *
 * Class LoveRechargeRecords
 * @package Yunshop\Love\Common\Models
 */
class LoveRechargeRecords extends BaseModel
{
    use CreateOrderSnTrait;


    protected $table = 'yz_love_recharge';

    protected $guarded = [''];

    /**
     * todo 应该 存在一个状态服务的常量集
     *
     * Recharge state error.
     */
    const STATUS_ERROR = -1;

    /**
     * todo 应该 存在一个状态服务的常量集
     *
     * Recharge state success.
     */
    const STATUS_SUCCESS = 1;

    public function member()
    {
        return $this->hasOne('Yunshop\Love\Common\Models\Member','uid','member_id');
    }


    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'uniacid'       => "公众号ID",
            'member_id'     => "会员ID",
            'change_value'  => '余额必须是有效的数字',
            'old_value'     => '充值金额',
            'new_value'     => '计算后金额',
            'type'          => '充值类型',
            'order_sn'      => '充值订单号',
            'status'        => '状态'
        ];
    }
}
