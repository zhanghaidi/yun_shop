<?php

namespace Yunshop\Examination\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnswerPaperModel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_exam_answer_paper';

    public function content()
    {
        return $this->hasOne('Yunshop\Examination\models\AnswerPaperContentModel', 'answer_paper_id');
    }

    public static function completeToInvalid(int $serviceId, int $id)
    {
        return self::where([
            'id' => $id,
            'uniacid' => $serviceId,
            'status' => 2,
        ])->limit(1)->update(['status' => 3]);
    }
}
