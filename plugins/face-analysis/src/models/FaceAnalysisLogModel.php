<?php

namespace Yunshop\FaceAnalysis\models;


use app\common\models\BaseModel;

class FaceAnalysisLogModel extends BaseModel
{
    public $table = 'yz_face_analysis_log';

    public static function getList()
    {
        $model = self::uniacid();

        return $model;
    }
}