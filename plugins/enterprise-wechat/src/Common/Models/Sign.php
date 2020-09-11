<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/6 上午11:32
 * Email: livsyitian@163.com
 */

namespace Yunshop\Sign\Common\Models;


use app\common\models\BaseModel;

class Sign extends BaseModel
{
    protected $table = 'yz_sign';


    protected $guarded = [];


    protected $appends = ['sign_status', 'cumulative', 'cumulative_name'];


    public function member()
    {
        return $this->belongsTo('Yunshop\Sign\Common\Models\Member', 'member_id', 'uid');
    }

    public function signLog()
    {
        return $this->hasMany('Yunshop\Sign\Common\Models\SignLog', 'member_id', 'member_id');
    }


    public function scopeWithMember($query)
    {
        return $query->with(['member' => function($query) {
            return $query->records();
        }]);
    }


    public function scopeWithSignLog($query)
    {
        return $query->with(['signLog' => function($query) {
            return $query->records();
        }]);
    }


    public function scopeRecords($query)
    {
        return $query;
    }


    public function scopeSearch($query, $search)
    {
        if ($search['search_time']) {
            $query->whereBetween('updated_at', [strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }
        return $query;
    }


    public function scopeSearchMember($query,$search)
    {
        return $query->whereHas('member',function($query)use($search) {
            return $query->search($search);
        });
    }


    public function scopeOfUid($query, $member_id)
    {
        return $query->where('member_id', $member_id);
    }


    public function getSignStatusAttribute()
    {
        /*$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));

        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        return ($beginToday <= $this->attributes['updated_at'] && $this->attributes['updated_at'] <= $endToday);*/

        return static::timeDifferenceResult();
    }

    public function getCumulativeAttribute()
    {
        /*$beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));

        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        return ($beginYesterday <= $this->attributes['updated_at'] && $this->attributes['updated_at'] <= $endToday);*/
        return static::timeDifferenceResult(1);
    }


    public function getCumulativeNameAttribute()
    {
        $result = static::getCumulativeAttribute();

        return $result ? $this->attributes['cumulative_number'] .  trans('Yunshop\Sign::sign.sign_unit') : trans('Yunshop\Sign::sign.sign_unit_hint');
    }


    private function timeDifferenceResult($size = 0)
    {
        $begin = mktime(0,0,0,date('m'),date('d')-$size,date('Y'));

        $end = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        return ($begin <= $this->attributes['updated_at'] && $this->attributes['updated_at'] <= $end);
    }





}
