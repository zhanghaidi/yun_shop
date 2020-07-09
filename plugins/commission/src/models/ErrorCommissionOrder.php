<?php
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/5/19
 * Time: 下午3:23
 */

namespace Yunshop\Commission\models;

use app\backend\models\BackendModel;
use app\common\models\Member;
use Illuminate\Database\Eloquent\SoftDeletes;

class ErrorCommissionOrder extends BackendModel
{
    use SoftDeletes;

    public $table = 'yz_error_commission_order';
    protected $guarded = [''];

}