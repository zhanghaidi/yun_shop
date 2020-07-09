<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 10:46
 */

namespace Yunshop\JdSupply\frontend\order;


use app\common\exceptions\AppException;
use app\common\models\Goods;

class MemberCart extends \app\common\models\MemberCart
{
    public function getGroupId()
    {
        return $this->goods->plugin_id.'_'.$this->goods->hasOneJdGoods->source.'_'.$this->goods->hasOneJdGoods->shop_id;
    }

    public function goods()
    {
        return $this->belongsTo(\Yunshop\JdSupply\models\Goods::class,'goods_id');
    }

    /**
     * 购物车验证
     * @throws AppException
     */
    public function validate()
    {
        parent::validate();

    }
}