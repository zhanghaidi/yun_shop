<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/23
 * Time: 下午5:14
 */

namespace Yunshop\Printer\common\models;


use app\common\models\BaseModel;
use app\common\scopes\UniacidScope;

class PrintSet extends BaseModel
{
    public $table = 'yz_print_setting';
    public $timestamps = true;
    protected $guarded = [''];
    static protected $needLog = true;
    protected $casts = [
        'print_type' => 'json',
        'perms' => 'json'
    ];

    public static function fetchSetting()
    {
        return PrintSet::select()->ByOwner()->ByOwnerId()->first();
    }

    public function scopeByOwner($query)
    {
        $printer_owner = \app\common\modules\shop\ShopConfig::current()->get('printer_owner');
        return $query->whereOwner($printer_owner['owner']);
    }

    public function scopeByOwnerId($query)
    {
        $printer_owner = \app\common\modules\shop\ShopConfig::current()->get('printer_owner');
        return $query->whereOwnerId($printer_owner['owner_id']);
    }

    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope);
       // static::addGlobalScope(function (Builder $builder) {
       //     $builder->uniacid()->byOwner()->byOwnerId();
       // });
    }
}