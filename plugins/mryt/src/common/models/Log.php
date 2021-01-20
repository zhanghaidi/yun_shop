<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/3/6
 * Time: 11:32 PM
 */

namespace Yunshop\Mryt\common\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use app\common\models\Member;

class Log extends BaseModel
{
    public $table = 'yz_mryt_log';
    public $timestamps = true;
    protected $guarded = [''];

    public static function getList($search)
    {
        return self::build()->search($search);
    }

    public function scopeBuild($query)
    {
        return $query->with([
            'hasOneMember' => function ($member) {
                $member->select(['uid', 'mobile', 'nickname', 'realname', 'avatar']);
            }
        ]);
    }

    public function scopeSearch($query, $search)
    {
        if ($search['uid']) {
            $query->where('uid', $search['uid']);
        }
        if ($search['member']) {
            $query->whereHas('hasOneMember', function ($member) use ($search) {
                $member->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $search['member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $search['member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $search['member'] . '%');
            });
        }

        return $query;
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}