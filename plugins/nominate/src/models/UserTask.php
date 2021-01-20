<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/17
 * Time: 11:01 AM
 */

namespace Yunshop\Nominate\models;


use app\common\models\BaseModel;

class UserTask extends BaseModel
{
    public $table = 'yz_nominate_user_task';
    public $timestamps = true;
    protected $guarded = [''];
}