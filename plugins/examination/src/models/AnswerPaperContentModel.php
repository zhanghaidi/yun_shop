<?php

namespace Yunshop\Examination\models;

use app\common\models\BaseModel;

class AnswerPaperContentModel extends BaseModel
{
    public $table = 'yz_exam_answer_paper_content';

    public static function generateAnswerContent(array $question)
    {
        foreach ($question as $k => $v) {
            $question[$k]['reply'] = '';
            $question[$k]['obtain'] = 0;
            $question[$k]['correct'] = 0;
        }
        return $question;
    }
}
