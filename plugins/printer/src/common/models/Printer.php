<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/23
 * Time: 下午3:59
 */

namespace Yunshop\Printer\common\models;

use app\common\models\BaseModel;
use app\common\scopes\UniacidScope;

class Printer extends BaseModel
{
    public $table = 'yz_printer';
    public $timestamps = true;
    protected $guarded = [''];
    protected $appends = ['status_obj'];
    static protected $needLog = true;

    const SHOP = 1;
    const STORE = 2;
    const SUPPLIER = 3;

    public static function fetchPrints($kwd)
    {
        return Printer::select()->ByOwner()->ByOwnerId()->search($kwd);
    }

    public static function getPrinterById($id)
    {
        return Printer::select()->whereId($id);
    }

    public function scopeSearch($query, $kwd)
    {
        if (!$kwd) {
            return $query;
        }
        return $query->where('title', 'like', '%' . $kwd . '%');
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

    public function getStatusObjAttribute()
    {
        $status_obj = [];
        if ($this->status == 0) {
            $status_obj = [
                'style' => 'btn btn-default',
                'name'  => '关闭'
            ];
        }
        if ($this->status == 1) {
            $status_obj = [
                'style' => 'btn btn-info',
                'name'  => '开启'
            ];
        }
        return $status_obj;
    }

    public static function add($printer_data)
    {
        $model = new Printer();
        $model->fill($printer_data);
        $validator = $model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        $model->save();
    }

    public static function edit($printer_data, $model)
    {
        $model->fill($printer_data);
        $validator = $model->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        $model->save();
    }

    public function atributeNames() {
        return [
            'title'  => '打印机名称',
            'user'  => 'USER',
            'ukey'  => 'UKEY',
            'printer_sn'  => '打印机编号',
            'times' => '打印联数'
        ];
    }

    public function rules()
    {
        return [
            'title'  => 'required',
            'user'  => 'required',
            'ukey'  => 'required',
            'printer_sn'  => 'required',
            'times' => 'numeric|min:1'
        ];
    }

    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope);
//        static::addGlobalScope(function (Builder $builder) {
//            $builder->uniacid()->byOwner()->byOwnerId();
//        });
    }
}