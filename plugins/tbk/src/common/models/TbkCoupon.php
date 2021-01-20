<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com yangyu
 * Date: 2018/12/20
 * Time: 17:45
 */

namespace Yunshop\Tbk\common\models;


class TbkCoupon extends \Yunshop\Tbk\common\models\BaseModel
{
    public $table = 'yz_tbk_coupon';
    public $timestamps = true;
    protected $guarded = [''];

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }

}