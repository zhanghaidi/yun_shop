<?php

namespace Yunshop\Examination\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionModel extends BaseModel
{
    // 单选
    const TYPE1 = 'single';
    // 多选
    const TYPE2 = 'multiple';
    // 判断
    const TYPE3 = 'judgment';
    // 填空
    const TYPE4 = 'blank';
    // 问答
    const TYPE5 = 'qa';

    use SoftDeletes;
    public $table = 'yz_exam_question';

    public static function getList()
    {
        $model = self::uniacid();

        return $model;
    }

    public static function getTypeDesc(int $type)
    {
        $ref = new \ReflectionClass(new self);
        $constants = $ref->getConstants();

        $typeDesc = '';
        foreach ($constants as $k => $v) {
            if ('TYPE' . $type == $k) {
                $typeDesc = $v;
                break;
            }
        }
        return $typeDesc;
    }
}
