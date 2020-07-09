<?php

namespace Yunshop\Poster\models;

use app\common\models\BaseModel;

class PosterScan extends BaseModel
{
    protected $table = 'yz_poster_scan';
    protected $guarded = [''];
    protected $search_fields = ['created_at'];

    //获取所有扫码记录
    public static function getDetailedPosterScan($posterId = NULL)
    {
        $detailedPosterScans = self::uniacid()->with(['recommender'=>function($query){
                    return $query->select(['uid', 'avatar', 'nickname', 'realname', 'mobile']);
                }])->with(['subscriber'=>function($query){
                    return $query->select(['uid', 'avatar', 'nickname', 'realname', 'mobile']);
                }])->select(['subscriber_memberid', 'recommender_memberid', 'created_at', 'event_type','is_register'])
                ->orderBy('created_at', 'desc');
        if ($posterId){
            $detailedPosterScans = $detailedPosterScans->where('poster_id', '=', $posterId);
        }
        return $detailedPosterScans;
    }

    //多条件搜索
    public static function searchPosterScan($params)
    {
        $detailedPosterScans = self::uniacid()
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
            $detailedPosterScans = $detailedPosterScans->where('poster_id', '=', $params['poster_id']);
        }
        if ($params['searchTime']){
            $detailedPosterScans = $detailedPosterScans->whereBetween('created_at',
                                    [
                                        $params['timeStart'],
                                        $params['timeEnd'],
                                    ]);
        }

        return $detailedPosterScans;
    }

    /*
     * 多个扫码记录对应一个海报
     */
    public function poster()
    {
        return $this->belongsTo('Yunshop\Poster\models\Poster', 'id', 'poster_id');
    }

    //一个扫码记录对应一个推荐者
    public function recommender()
    {
        return $this->hasOne('app\common\models\Member', 'uid','recommender_memberid');
    }

    //一个扫码记录对应一个扫描者
    public function subscriber()
    {
        return $this->hasOne('app\common\models\Member', 'uid','subscriber_memberid');
    }


}