<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/2
 * Time: 下午12:49
 */

namespace Yunshop\RechargeCode\common\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class RechaergeCodeBindLog extends BaseModel
{
    public $table = 'yz_recharge_code_bind_log';
    public $timestamps = true;
    protected $guarded = [''];
    protected $casts = [
        'code_information' => 'json'
    ];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}