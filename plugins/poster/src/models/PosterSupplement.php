<?php

namespace Yunshop\Poster\models;

use app\common\models\BaseModel;

class PosterSupplement extends BaseModel
{
    protected $table = 'yz_poster_supplement';
    public $timestamps = false;
    protected $guarded = [''];

    /**
     *  定义字段名
     * 可使用
     * @return array */
    public function atributeNames() { //todo typo
        return [
            'recommender_credit' => '推荐者的积分奖励',
            'recommender_bonus' => '推荐者的现金奖励',
            'recommender_coupon_id' => '奖励推荐者的优惠券的 ID',
            'recommender_coupon_num' => '奖励推荐者的优惠券的张数',
            'subscriber_credit' => '关注者的积分奖励',
            'subscriber_bonus' => '关注者的现金奖励',
            'subscriber_coupon_id' => '奖励关注者的优惠券的 ID',
            'subscriber_coupon_num' => '奖励关注者的优惠券的张数',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public function rules() {
        return [
            'recommender_credit' => 'integer',
            'recommender_bonus' => 'numeric|min:0',//数值型
            'recommender_coupon_id' => 'integer',
            'recommender_coupon_num' => 'integer',
            'subscriber_credit' => 'integer',
            'subscriber_bonus' => 'numeric|min:0',//数值型
            'subscriber_coupon_id' => 'integer',
            'subscriber_coupon_num' => 'integer',
            'bonus_method' => 'integer|between:1,2',
        ];
    }

    /**
     * 和主表一对一的关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function poster()
    {
        return $this->belongsTo('YunShop\Poster\models\Poster', 'poster_id', 'poster_id');
    }

    //根据海报ID获取奖励通知
    public static function getAwardInfoByPosterId($posterId)
    {
        $award = self::where('poster_id', '=', $posterId)
                            ->select([
                                'subscriber_credit',
                                'subscriber_bonus',
                                'bonus_method',
                                'subscriber_coupon_id',
                                'subscriber_coupon_num',
                                'recommender_credit',
                                'recommender_bonus',
                                'recommender_coupon_id',
                                'recommender_coupon_num',
                            ])
                            ->first();
        return $award;
    }

    //根据海报ID获取实例
    public static function getPosterSupplementByPosterId($posterId)
    {
        return self::where('poster_id', '=', $posterId)->first();
    }
}