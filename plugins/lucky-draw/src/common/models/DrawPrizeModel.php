<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-06-26
 * Time: 14:18
 */

namespace Yunshop\LuckyDraw\common\models;


use app\common\models\BaseModel;
use app\common\models\Coupon;

class DrawPrizeModel extends BaseModel
{
    protected $table = 'yz_draw_prize';
    protected $guarded = [''];

    public function hasOneCoupon()
    {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id');
    }
}