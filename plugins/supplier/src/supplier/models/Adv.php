<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/9
 * Time: 14:41
 */

namespace Yunshop\Supplier\supplier\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Yunshop\Supplier\common\models\Supplier;

class Adv extends BaseModel
{
    public $table = 'yz_supplier_adv';
    public $timestamps = true;
    static protected $needLog = true;
    protected $guarded = [''];
    protected $casts = [
        'advs' => 'json'
    ];

    public function hasOneSupplier()
    {
        return $this->hasOne(Supplier::class, 'uid', 'supplier_uid');
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}