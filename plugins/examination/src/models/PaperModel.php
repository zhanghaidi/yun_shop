<?php

namespace Yunshop\Examination\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaperModel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_exam_paper';
}
