<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/11
 * Time: 下午5:32
 */

namespace Yunshop\Mryt\store\models;


use app\common\models\BaseModel;

class GiveReward extends BaseModel
{
    public $table = 'yz_store_cashier_give_reward';
    public $timestamps = true;
    protected $guarded = [''];

    const REWARD_POINT = 0; // todo 积分
    const REWARD_LOVE = 1; // todo 爱心值

    const STORE = 1; // todo 奖励给门店
    const BUYER = 0; // todo 奖励给购买者

    public static function getStatisticByRewardTypeAndByBelongTo($reward_type, $belong_to)
    {
        return self::select()->byRewardType($reward_type)->byBelongTo($belong_to);
    }

    public function scopeByRewardType($query, $reward_type)
    {
        return $query->where('reward_model', $reward_type);
    }

    public function scopeByBelongTo($query, $belong_to)
    {
        return $query->where('is_store', $belong_to);
    }
}