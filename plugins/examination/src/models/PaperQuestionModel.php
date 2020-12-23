<?php

namespace Yunshop\Examination\models;

use app\common\models\BaseModel;
use Illuminate\Support\Facades\Redis;
use Yunshop\Examination\models\PaperQuestionModel;
use Yunshop\Examination\models\QuestionLogModel;
use Yunshop\Examination\models\QuestionModel;

class PaperQuestionModel extends BaseModel
{
    public $table = 'yz_exam_paper_question';

    const UPDATED_AT = null;

    /**
     * 获取试卷的问题内容
     * @param $serviceId 小程序ID
     * @param $paperId 试卷ID
     * @desc 使用了5-10分钟的缓存
     */
    public static function getQuestion(int $serviceId, int $paperId)
    {
        $cacheKey = 'AJYEXAM:PQ:GQ:' . $serviceId . ':' . $paperId;
        $result = Redis::get($cacheKey);
        if ($result !== false && $result !== null) {
            return json_decode($result, true);
        }

        $paperQuestionRs = PaperQuestionModel::select('id', 'question_id', 'score', 'option')
            ->where('paper_id', $paperId)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get()->toArray();
        $questionIds = array_column($paperQuestionRs, 'question_id');
        if (!isset($questionIds[0])) {
            return ['code' => 1, 'msg' => '试卷的题目还没有准备好，请稍后再试'];
        }

        $questionRs = QuestionModel::select('id', 'type', 'problem', 'log_id')
            ->whereIn('id', $questionIds)
            ->where('uniacid', $serviceId)
            ->get()->toArray();
        $questionLogIds = array_column($questionRs, 'log_id');
        if (!isset($questionLogIds[0])) {
            return ['code' => 1, 'msg' => '试卷题目还没有设置好，请稍后再试'];
        }

        $questionLogRs = QuestionLogModel::select('id', 'question_id', 'answer')
            ->whereIn('id', $questionLogIds)->get()->toArray();

        $return = [];
        foreach ($paperQuestionRs as $v1) {
            if (!isset($v1['id'])) {
                continue;
            }
            $temp = [];
            $temp['question_id'] = $v1['question_id'];
            $temp['score'] = $v1['score'];
            $temp['option'] = $v1['option'];
            foreach ($questionRs as $v2) {
                if (!isset($v2['id'])) {
                    continue;
                }
                if ($temp['question_id'] != $v2['id']) {
                    continue;
                }
                $temp['type'] = $v2['type'];
                $temp['problem'] = $v2['problem'];

                foreach ($questionLogRs as $v3) {
                    if (!isset($v3['id']) || !isset($v3['question_id'])) {
                        continue;
                    }
                    if ($v2['log_id'] != $v3['id']) {
                        continue;
                    }
                    if ($temp['question_id'] != $v3['question_id']) {
                        continue;
                    }

                    $temp['question_log_id'] = $v3['id'];
                    $temp['answer'] = $v3['answer'];
                    break;
                }

                break;
            }

            if (!isset($temp['question_log_id'])) {
                continue;
            }

            $type = QuestionModel::getTypeDesc($temp['type']);
            if ($type == '') {
                continue;
            }
            if ($type == 'multiple') {
                $temp['option'] = json_decode($temp['option'], true);
            }

            $temp['answer'] = json_decode($temp['answer'], true);
            foreach ($temp['answer'] as $k => $v) {
                if (strpos($k, 'option') === 0) {
                    $tempKey = substr($k, 6);
                    $temp['answer'][$tempKey] = $v;
                    unset($temp['answer'][$k]);
                }
            }

            $return[] = $temp;
        }
        if (!isset($return[0])) {
            return ['code' => 1, 'msg' => '试卷题目内容空白，请稍后再试'];
        }
        $return = ['code' => 0, 'data' => $return];
        Redis::setex($cacheKey, mt_rand(300, 600), json_encode($return));
        return $return;
    }
}
