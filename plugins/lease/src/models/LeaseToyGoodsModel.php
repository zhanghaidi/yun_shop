<?php

namespace Yunshop\LeaseToy\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use app\common\traits\MessageTrait;


/**
* Author: 芸众商城 www.yunzshop.com
* Date: 2018/3/1
* Time: 10:05
*/
class LeaseToyGoodsModel extends BaseModel
{
    use SoftDeletes, MessageTrait;
   
   public $table = 'yz_lease_toy_goods';

   protected $guarded = [''];

   protected $hidden = [
      'uniacid',
      'deleted_at'
   ];

   protected $attributes = [
        'is_lease' => 0,
        'is_rights' => 1,
        'goods_deposit' => 0,
        'immed_goods_id' => 0
   ];


   public static function scopeOfGoodsId($query, $goodsId)
   {
       return $query->where('goods_id', $goodsId);
   }

    /**
     *  定义字段名
     * 可使
     * @return array
     */
    public function atributeNames()
    {
        return [
            'goods_deposit' => '押金',
            'immed_goods_id' => '购买商品ID'
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'goods_deposit' => 'required|numeric',
            'immed_goods_id' => 'required|numeric',
        ];
    }
}