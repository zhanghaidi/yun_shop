<?php

namespace Yunshop\MinappContent\models;

use app\common\models\BaseModel;

class HotSpotModel extends BaseModel
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    public $table = 'diagnostic_service_hot_spot';

    public function image()
    {
        return $this->hasMany('Yunshop\MinappContent\models\HotSpotImageModel', 'spot_id', 'id');
    }
}
