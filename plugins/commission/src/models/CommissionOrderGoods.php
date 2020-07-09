<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/3/14
 * Time: 下午5:30
 */

namespace Yunshop\Commission\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionOrderGoods extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_commission_order_goods';
    public $timestamps = true;
    protected $guarded = [''];
    
    
}