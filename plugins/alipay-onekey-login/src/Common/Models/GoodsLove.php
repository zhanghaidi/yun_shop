<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 下午5:04
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;
use app\common\traits\MessageTrait;
use Illuminate\Database\Eloquent\Builder;
use Yunshop\Love\Common\Services\SetService;


/**
 * Class GoodsLove
 * @package Yunshop\Love\Common\Models
 *
 */
class GoodsLove extends BaseModel
{
    use MessageTrait;

    protected $table = 'yz_love_goods';

    protected $guarded = [''];

    /**
     * 设置全局作用域 拼接 uniacid()
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(
            function (Builder $builder) {
                return $builder->uniacid();
            }
        );
    }

    /**
     * 商品ID 检索
     * @param $query
     * @param $goodsId
     * @return mixed
     */
    public function scopeOfGoodsId($query, $goodsId)
    {
        return $query->where('goods_id', $goodsId);
    }


    /**
     * 商品ID 检索
     * @param $query
     * @param $goodsId
     * @return mixed
     */
    public function GetLoveGoodsSet($goodsId)
    {
        return self::select('love_accelerate','activation_state')->where('goods_id',$goodsId)->first()->toArray();
    }

    /**
     * 字段验证规则
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid'                   => 'numeric|integer',
            //'goods_id'                => 'numeric|integer',
            'award'                     => 'regex:/^[01]$/',
            'parent_award'              => 'regex:/^[012]$/',
            'deduction'                 => 'regex:/^[01]$/',
            'award_proportion'          => 'numeric|min:0',
            'parent_award_proportion'   => 'numeric|min:0|max:100',
            'second_award_proportion'   => 'numeric|min:0|max:100',
            'third_award_proportion'    => 'numeric|min:0|max:100',
            'deduction_proportion'      => 'numeric|min:0|max:100',
        ];
    }


    /**
     * 字段名称
     * @return array
     */
    public function atributeNames()
    {
        $love = SetService::getLoveName();
        return [
            'uniacid'                   => '公众号ID',
            //'goods_id'              => '商品ID',
            'award'                     => $love . '购物奖励开关',
            'parent_award'              => $love . '购物上级赠送开关',
            'deduction'                 => $love . '购物抵扣开关',
            'award_proportion'          => $love . '购物奖励比例',
            'parent_award_proportion'   => $love . '购物上一级赠送比例',
            'second_award_proportion'   => $love . '购物上二级赠送比例',
            'third_award_proportion'    => $love . '购物上三级赠送比例',
            'deduction_proportion'      => $love . '购物抵扣比例',
        ];
    }

}