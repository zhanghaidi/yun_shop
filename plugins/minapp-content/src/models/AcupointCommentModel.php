<?php

namespace Yunshop\MinappContent\models;

use app\common\models\BaseModel;

class AcupointCommentModel extends BaseModel
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    public $table = 'diagnostic_service_acupoint_comment';
}
