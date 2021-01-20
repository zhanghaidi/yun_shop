<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/11/23
 * Time: 10:36 AM
 */

namespace Yunshop\Mryt\common\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use app\common\models\Member;
use Yunshop\Mryt\models\MrytLevelModel;

class TierAward extends BaseModel
{
    public $table = 'yz_mryt_tier_award';
    public $timestamps = true;
    protected $guarded = [''];
    protected $appends = [
        'status_name'
    ];

    public static function getListByApi($status)
    {
        return self::build()->byStatus($status);
    }

    public static function getList($search)
    {
        return self::build()->search($search);
    }

    public function scopeByStatus($query, $status)
    {
        if ($status != null && in_array($status, [0,1])) {
            $query->where('status', $status);
        }
        return $query;
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
        if ($search['source_uid']) {
            $query->where('source_uid', $search['source_uid']);
        }
        if ($search['source_member']) {
            $query->whereHas('hasOneSourceMember', function ($source_member) use ($search) {
                $source_member->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $search['source_member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $search['source_member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $search['source_member'] . '%');
            });
        }
        if ($search['search_time']) {
            $query->whereBetween('created_at', [strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }
    }

    public function getStatusNameAttribute()
    {
        $name = '待发放';
        if ($this->status == 1) {
            $name = '已发放';
        }
        return $name;
    }

    public function scopeBuild($query)
    {
        return $query->with([
            'hasOneMember' => function ($member) {
                $member->select(['uid', 'mobile', 'nickname', 'realname', 'avatar']);
            },
            'hasOneSourceMember' => function ($source_member) {
                $source_member->select(['uid', 'mobile', 'nickname', 'realname', 'avatar']);
            },
            'hasOneLevel' => function ($level) {
                $level->select(['id', 'level_name']);
            }
        ]);
    }

    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public function hasOneSourceMember()
    {
        return $this->hasOne(Member::class, 'uid', 'source_uid');
    }

    public function hasOneLevel()
    {
        return $this->hasOne(MrytLevelModel::class,'id', 'level_id');
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}