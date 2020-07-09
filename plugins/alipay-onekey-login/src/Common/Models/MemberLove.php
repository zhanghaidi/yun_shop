<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/6/27 上午10:56
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace Yunshop\Love\Common\Models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


/**
 * @property float $usable
 * @property int $member_id
 *
 * @method records()
 * @method search(array $search)
 *
 * Class MemberLove
 * @package Yunshop\Love\Common\Models
 */
class MemberLove extends BaseModel
{
    protected $table = 'yz_love_member';

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
     * 会员记录检索
     * @param $query
     * @return mixed
     */
    public function scopeRecords($query)
    {
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search['min_love']) {
            $query->where('usable', '>=', $search['min_love']);
        }
        if ($search['max_love']) {
            $query->where('usable', '<=', $search['max_love']);
        }
        return $query;
    }

    public function scopeOfMemberId($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    /**
     * @param $rate
     * @return self
     */
    public static function getReturnLove($rate)
    {
        /**
         * @var static $model
         */
        $model = self::uniacid();

        $model->where(DB::raw('`usable` / 100 * ' . $rate), '>=', 0.01);

        $model->where('usable', '>', 0);

        return $model;
    }

    public static function updatedReturnLove($rate)
    {
        return self::uniacid()
            ->where(DB::raw('`usable` / 100 * ' . $rate), '>=', 0.01)
            ->where('usable', '>', 0)
            ->update(['usable' => DB::raw('`usable` - `usable` / 100 * ' . $rate)]);
    }

    /*
     *获取爱心值冻结值
     * */
    public function getMemberLoveFroze($memberId)
    {
        return self::where('member_id', $memberId)
            ->first(['froze']);
    }

    public function Member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'uid');
    }

}