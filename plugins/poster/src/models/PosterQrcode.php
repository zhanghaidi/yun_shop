<?php

namespace Yunshop\Poster\models;

use app\common\models\BaseModel;

class PosterQrcode extends BaseModel
{
    protected $table = 'yz_poster_qrcode';
    public $timestamps = true;
    protected $guarded = [''];

    //获取用户在指定海报下生成的二维码
    public static function getUserExistedPoster($memberId, $posterId)
    {
        $poster = self::uniacid()
            ->where('memberid', '=', $memberId)
            ->where('poster_id', '=', $posterId)
            ->first();
        return $poster;
    }

    //根据二维码ID获取海报ID
    public static function getPosterIdByQrcodeId($qrcodeId)
    {
        $posterId = self::uniacid()
                    ->where('qrcode_id', '=', $qrcodeId)
                    ->value('poster_id');
        return $posterId;
    }

    //根据qrcodeId获取推荐者member_id
    public static function getRecommenderIdByQrcodeId($id)
    {
        $uid = self::uniacid()
            ->where('qrcode_id', '=', $id)
            ->value('memberid');
        return $uid;
    }



}