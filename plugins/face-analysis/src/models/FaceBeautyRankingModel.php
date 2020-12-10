<?php

namespace Yunshop\FaceAnalysis\models;

use app\common\models\BaseModel;

class FaceBeautyRankingModel extends BaseModel
{
    public $table = 'yz_face_beauty_ranking';

    public static function getList()
    {
        $model = self::uniacid();

        return $model;
    }
}