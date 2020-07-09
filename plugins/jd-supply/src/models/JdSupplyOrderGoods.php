<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/5
 * Time: 15:41
 */

namespace Yunshop\JdSupply\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class JdSupplyOrderGoods extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_jd_supply_order_goods';

    protected $guarded = [''];


}