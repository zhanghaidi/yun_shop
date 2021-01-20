<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/23
 * Time: 下午4:01
 */

namespace Yunshop\Printer\common\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Temp extends BaseModel
{
    public $table = 'yz_print_template';
    public $timestamps = true;
    protected $guarded = [''];
    static protected $needLog = true;
    protected $casts = [
        'print_data' => 'json'
    ];
    const SHOP = 1;
    const STORE = 2;
    const SUPPLIER = 3;

    public static function fetchTemps($kwd)
    {
        return Temp::select()->search($kwd);
    }

    public static function getTempById($id)
    {
        return Temp::select()->whereId($id);
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
        $printer_owner = \Config::get('printer_owner');
        return $query->whereOwner($printer_owner['owner']);
    }

    public function scopeByOwnerId($query)
    {
        $printer_owner = \Config::get('printer_owner');
        return $query->whereOwnerId($printer_owner['owner_id']);
    }

    public function atributeNames() {
        return [
            'title'  => '模板名称',
            'print_title'  => '打印头部',
            'print_style'  => '打印列格式'
        ];
    }

    public function rules()
    {
        return [
            'title'  => 'required',
            'print_title'  => 'required',
            'print_style'  => 'required'
        ];
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid()->byOwner()->byOwnerId();
        });
    }
}