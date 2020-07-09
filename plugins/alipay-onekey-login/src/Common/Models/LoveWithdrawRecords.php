<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/29 下午5:02
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;

/**
 * @property $love_value
 * @property $member_id
 *
 * Class LoveWithdrawRecords
 * @package Yunshop\Love\Common\Models
 */
class LoveWithdrawRecords extends BaseModel
{
    protected $table ='yz_love_withdrawal_record';

    protected $guarded = [''];

}
