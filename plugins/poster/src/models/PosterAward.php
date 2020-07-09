<?php

namespace Yunshop\Poster\models;

use app\common\models\BaseModel;

class PosterAward extends BaseModel
{
    protected $table = 'yz_poster_award';
    protected $guarded = [''];

    /**
     * 多个奖励记录对应一个海报
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poster()
    {
        return $this->belongsTo('YunShop\Poster\models\Poster', 'id', 'poster_id');
    }

    //一个奖励记录对应一个推荐者
    public function recommender()
    {
        return $this->hasOne('app\common\models\Member', 'uid','recommender_memberid');
    }

    //一个奖励记录对应一个扫描者
    public function subscriber()
    {
        return $this->hasOne('app\common\models\Member', 'uid','subscriber_memberid');
    }

    //获取奖励记录
    public static function getPosterAwards($posterId = NULL)
    {
        $posterAwards = self::uniacid()->with(['recommender'=>function($query){
                    return $query->select(['uid', 'avatar', 'nickname', 'realname', 'mobile']);
                }])->with(['subscriber'=>function($query){
                    return $query->select(['uid', 'avatar', 'nickname', 'realname', 'mobile']);
                }])
                ->orderBy('created_at', 'desc');
        if ($posterId){
            $posterAwards = $posterAwards->where('poster_id', '=', $posterId);
        }
        return $posterAwards;
    }

    //多条件搜索
    public static function searchPosterAwards($params)
    {
        $posterAwards = self::uniacid()
            ->whereHas('recommender', function($query) use ($params){
                $query->where('nickname', 'like', '%'.$params['recommender'].'%')
                    ->orWhere('realname', 'like', '%'.$params['recommender'].'%')
                    ->orWhere('mobile', 'like', '%'.$params['recommender'].'%');
            })
            ->whereHas('subscriber', function($query) use ($params){
                $query->where('nickname', 'like', '%'.$params['subscriber'].'%')
                    ->orWhere('realname', 'like', '%'.$params['subscriber'].'%')
                    ->orWhere('mobile', 'like', '%'.$params['subscriber'].'%');
            });
        if ($params['poster_id']){
            $posterAwards = $posterAwards->where('poster_id', '=', $params['poster_id']);
        }
        if ($params['searchTime']){
            $posterAwards = $posterAwards->whereBetween('created_at',
                [
                    $params['timeStart'],
                    $params['timeEnd']
                ]);
        }
        return $posterAwards;
    }
}