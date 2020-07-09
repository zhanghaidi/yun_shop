<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/11
 * Time: 16:18
 */

namespace Yunshop\JdSupply\models;


use app\common\models\BaseModel;
use app\common\models\Option;
use app\framework\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class JdGoodsOption extends  BaseModel
{
    public $table = 'yz_jd_supply_goods_option';

    protected $guarded = [''];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->whereNull('deleted_at');
        });
    }

    public function scopeGoodsId($query, $goods_id)
    {
        return $query->where('goods_id', $goods_id);
    }
    public function scopeJdGoodsId($query, $jd_goods_id)
    {
        return $query->where('jd_goods_id', $jd_goods_id);
    }
    public function scopeJdOptionId($query, $jd_option_id)
    {
        return $query->where('jd_option_id',$jd_option_id);
    }

    public static function getJdGoods($goods_id, $option_id)
    {
        return self::select('jd_goods_id', 'jd_option_id')->goodsId($goods_id)->where('option_id', $option_id);
    }

    public function hasOneOption()
    {
        return $this->hasOne(\app\common\models\GoodsOption::class,'id','option_id');
    }

    public static function getJdOptionData($goods_id, $jd_goods_id)
    {
        return self::goodsId($goods_id)->jdGoodsId('option_id', $jd_goods_id);
    }
}