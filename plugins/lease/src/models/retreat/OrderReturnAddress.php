<?php

namespace Yunshop\LeaseToy\models\retreat;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/3/21
* Time: 15:12
*/
class OrderReturnAddress extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_lease_toy_order_return_address';
    
    protected $guarded = [''];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}