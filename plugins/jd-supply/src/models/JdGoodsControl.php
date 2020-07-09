<?php


namespace Yunshop\JdSupply\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class JdGoodsControl extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_jd_supply_goods_control';
    protected $guarded = [''];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }

}