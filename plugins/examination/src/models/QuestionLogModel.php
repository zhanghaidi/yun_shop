<?php

namespace Yunshop\Examination\models;

use app\common\models\BaseModel;
use Exception;
use Yunshop\Examination\models\QuestionModel;

class QuestionLogModel extends BaseModel
{
    public $table = 'yz_exam_question_log';

    const UPDATED_AT = null;

    public static function saveDataProcess($data)
    {
        $return = [];
        if (!isset($data['type'])) {
            throw new Exception('参数type缺失');
        }
        $typeStr = QuestionModel::getTypeDesc($data['type']);
        if ($typeStr == '') {
            throw new Exception('参数type错误');
        }
        $return['type'] = $data['type'];

        if (!isset($data['sort_id'])) {
            $data['sort_id'] = 0;
        }
        $return['sort_id'] = $data['sort_id'];

        if (!isset($data['problem'])) {
            throw new Exception('参数problem缺失');
        }
        $return['problem'] = html_entity_decode($data['problem']);

        if (isset($data['explain'])) {
            $data['explain'] = html_entity_decode($data['explain']);
        }

        $func = $typeStr . 'SaveDataProcess';
        $return = array_merge($return, self::$func($data));
        return $return;
    }

    private static function singleSaveDataProcess($data)
    {
        $optionOrder = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $return = [];
        for ($i = 0; $i < strlen($optionOrder); $i++) {
            if (!isset($data['option' . $optionOrder[$i]])) {
                break;
            }
            $return['option' . $optionOrder[$i]] = html_entity_decode($data['option' . $optionOrder[$i]]);
        }
        if (!isset($return['optionA']) || !isset($return['optionB'])) {
            throw new Exception('参数option错误，最少需要两个参数');
        }

        if (!isset($data['answer'])) {
            throw new Exception('参数answer缺失');
        }
        if (!isset($return['option' . $data['answer']])) {
            throw new Exception('参数answer错误');
        }
        $return['answer'] = $data['answer'];
        $return['explain'] = isset($data['explain']) ? $data['explain'] : '';

        return ['answer' => json_encode($return)];
    }

    private static function multipleSaveDataProcess($data)
    {
        $optionOrder = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $return = [];
        for ($i = 0; $i < strlen($optionOrder); $i++) {
            if (!isset($data['option' . $optionOrder[$i]])) {
                break;
            }
            $return['option' . $optionOrder[$i]] = html_entity_decode($data['option' . $optionOrder[$i]]);
        }
        if (!isset($return['optionA']) || !isset($return['optionB'])) {
            throw new Exception('参数option错误，最少需要两个参数');
        }

        if (!isset($data['answer']) || !is_array($data['answer']) ||
            !isset($data['answer'][0])
        ) {
            throw new Exception('参数answer缺失');
        }

        foreach ($data['answer'] as $v) {
            if (!isset($return['option' . $v])) {
                exit;
                throw new Exception('参数answer错误');
            }
        }
        $return['answer'] = implode(',', $data['answer']);
        $return['explain'] = isset($data['explain']) ? $data['explain'] : '';
        return ['answer' => json_encode($return)];

    }

    private static function judgmentSaveDataProcess($data)
    {
        $return = [];
        if (!isset($data['answer']) || !in_array($data['answer'], [0, 1])) {
            throw new Exception('参数answer缺失');
        }

        if ($data['answer'] == 1) {
            $return['answer'] = true;
        } else {
            $return['answer'] = false;
        }
        $return['explain'] = isset($data['explain']) ? $data['explain'] : '';
        return ['answer' => json_encode($return)];
    }

    public static function getAdminManageInfo($question, $log)
    {
        $return = [];
        if (!isset($question->id) || !isset($question->type)) {
            return $return;
        }
        isset($question->id) && $return['id'] = $question->id;
        isset($question->sort_id) && $return['sort_id'] = $question->sort_id;
        isset($question->problem) && $return['problem'] = $question->problem;

        $typeStr = QuestionModel::getTypeDesc($question->type);

        if (isset($log->answer)) {
            $answer = json_decode($log->answer, true);

            $func = $typeStr . 'GetAdminManageInfo';
            $return = array_merge($return, self::$func($answer));
        }
        return $return;
    }

    private static function singleGetAdminManageInfo($data)
    {
        $return = $option = [];
        $optionOrder = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < strlen($optionOrder); $i++) {
            if (!isset($data['option' . $optionOrder[$i]])) {
                break;
            }

            $option[$optionOrder[$i]] = [
                'name' => $optionOrder[$i],
                'content' => $data['option' . $optionOrder[$i]],
                'option' => false,
            ];
        }

        if (isset($data['answer']) && isset($option[$data['answer']])) {
            $option[$data['answer']]['option'] = true;
        }
        $return['answer'] = $option;
        isset($data['explain']) && $return['explain'] = $data['explain'];
        return $return;
    }

    private static function multipleGetAdminManageInfo($data)
    {
        $return = $option = [];
        $optionOrder = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < strlen($optionOrder); $i++) {
            if (!isset($data['option' . $optionOrder[$i]])) {
                break;
            }

            $option[$optionOrder[$i]] = [
                'name' => $optionOrder[$i],
                'content' => $data['option' . $optionOrder[$i]],
                'option' => false,
            ];
        }
        if (isset($data['answer'])) {
            $data['answer'] = explode(',', $data['answer']);
            foreach ($option as $k => $v) {
                if (in_array($v['name'], $data['answer'])) {
                    $option[$k]['option'] = true;
                }
            }
        }
        $return['answer'] = $option;
        isset($data['explain']) && $return['explain'] = $data['explain'];
        return $return;
    }

    private static function judgmentGetAdminManageInfo($data)
    {
        $return = ['answer' => false];
        if (isset($data['answer']) && is_bool($data['answer'])) {
            $return['answer'] = $data['answer'];
        }
        isset($data['explain']) && $return['explain'] = $data['explain'];
        return $return;
    }
}
