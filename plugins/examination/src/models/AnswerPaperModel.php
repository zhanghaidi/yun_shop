<?php

namespace Yunshop\Examination\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnswerPaperModel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_exam_answer_paper';
}
