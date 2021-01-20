<?php

namespace Yunshop\LeaseToy\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/**
* 
*/
class AreaLeaseReturnLogModel extends BaseModel
{
	public $table = 'yz_area_lease_log';
    public $timestamps = true;
    static protected $needLog = true;
    protected $guarded = [''];


    public static function getModel($order_id)
    {
    	$model = self::where('order_id', $order_id)->first();

    	return $model ? $model : new AreaLeaseReturnLogModel();

    }

	public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}