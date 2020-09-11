<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/8 上午11:35
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Frontend\Models;


use app\common\scopes\MemberIdScope;
use app\common\scopes\UniacidScope;

class Sign extends \Yunshop\Sign\Common\Models\Sign
{
    protected $appends = ['sign_status', 'cumulative', 'cumulative_name', 'cumulative_award'];


    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
        self::addGlobalScope(new MemberIdScope());
    }


    public function signLog()
    {
        return $this->hasMany('Yunshop\Sign\Frontend\Models\SignLog', 'member_id', 'member_id');
    }


    public function getCumulativeAwardAttribute()
    {
        if (app('plugins')->isEnabled('love')) {
            $love = \Yunshop\Love\Common\Services\SetService::getLoveName();
            $award_content = '积分：+' . $this->attributes['cumulative_point'] . "；";
            $award_content = $award_content . "优惠券：（" . $this->attributes['cumulative_coupon'] . "）张；";
            $award_content = $award_content . $love . "：（" . $this->attributes['cumulative_love'] . "）";
        }else{
            $award_content = '积分：+' . $this->attributes['cumulative_point'] . "；";
            $award_content = $award_content . "优惠券：（" . $this->attributes['cumulative_coupon'] . "）张；";
        }

        return $award_content;
    }

}
