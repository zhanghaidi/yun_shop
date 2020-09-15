<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/8 上午11:36
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Frontend\Models;

use app\common\scopes\UniacidScope;
use app\common\scopes\MemberIdScope;

class SignLog extends \Yunshop\Sign\Common\Models\SignLog
{
    protected $appends = ['award_content'];

    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
        self::addGlobalScope(new MemberIdScope());
    }

    public function getAwardContentAttribute()
    {
        if (app('plugins')->isEnabled('love')) {
            $awardContent = '积分：+' . $this->attributes['award_point'] . "；";
            $awardContent = $awardContent . "优惠券：（" . $this->attributes['award_coupon'] . "）张；";
            $awardContent = $awardContent . LOVE_NAME . "：(" . $this->attributes['award_love'] . ")";
        } else {
            $awardContent = '积分：+' . $this->attributes['award_point'] . "；";
            $awardContent = $awardContent . "优惠券：（" . $this->attributes['award_coupon'] . "）张；";
        }
        return $awardContent;
    }

}
