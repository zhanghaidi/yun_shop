<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/26
 * Time: 3:05 PM
 */

namespace Yunshop\Love\Backend\Modules\Love\Models;



use app\common\scopes\UniacidScope;
use Yunshop\Love\Backend\Modules\Love\Observers\TimingRechargeObserver;

class TimingLogModel extends \Yunshop\Love\Common\Models\TimingLogModel
{
    /**
     * @var array
     */
    public $timing_rule;

    public static function boot()
    {
        parent::boot();
        self::observe(new TimingRechargeObserver());
        self::addGlobalScope(new UniacidScope());
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'uniacid'       => "required|integer",
            'member_id'     => "required|integer",
            'amount'        => 'numeric|regex:/^[\-\+]?\d+(?:\.\d{1,2})?$/|max:9999999999',
            'total'         => 'required|integer',
            'recharge_sn'   => 'required',
        ];
    }
}
