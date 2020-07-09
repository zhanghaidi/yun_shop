<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019-10-29
 * Time: 14:15
 */

namespace Yunshop\Commission\models;


use app\common\models\BaseModel;

class Lose extends BaseModel
{
    public $table = 'yz_commission_lose';
    public $timestamps = true;
    protected $guarded = [''];
}