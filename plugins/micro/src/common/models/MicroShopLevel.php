<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/9
 * Time: 下午4:33
 */

namespace Yunshop\Micro\common\models;

use app\backend\modules\goods\models\Goods;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class MicroShopLevel extends BaseModel
{
    protected $table = 'yz_micro_shop_level';
    protected $guarded = [''];
    //设置主键
    protected $primaryKey = 'id';

    /**
     * @name 获取微店等级列表
     * @author 杨洋
     * @return $this
     */
    public static function getLevelList()
    {
        return self::with([
            'hasOneGoods'
        ])->orderBy('level_weight', 'asc');
    }

    /**
     * @name 通过微店等级id查询微店等级
     * @author 杨洋
     * @param $level_id
     * @return mixed
     */
    public static function getLevelById($level_id)
    {
        return self::with([
            'hasOneGoods'
        ])->byId($level_id)->first();
    }

    public static function getLevelByGoodsId($goods_id)
    {
        return self::with([
            'hasOneGoods'
        ])->byGoodsId($goods_id)->first();
    }

    public static function updateLevelById($id, $data)
    {
        return self::where('id', $id)->update($data);
    }

    public function hasOneGoods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }

    public function scopeById($query, $id)
    {
        return $query->where('id', $id);
    }

    public function scopeByGoodsId($query, $goods_id)
    {
        return $query->where('goods_id', $goods_id);
    }

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'level_weight'  => '等级权重',
            'level_name'  => '等级名称',
            'bonus_ratio'  => '分红比例',
            'goods_id'  => '商品id'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'level_weight'  => [
                'required',
                'integer',
                Rule::unique($this->table)->where('uniacid', $this->uniacid)->ignore($this->id)
            ],
            'level_name'  => 'required',
            'bonus_ratio'  => ['required', 'min:1', 'max:100'],
            'goods_id'  => [
                'required',
                Rule::unique($this->table)->ignore($this->id)
            ]
        ];
    }
}