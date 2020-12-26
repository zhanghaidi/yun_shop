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

            $temp['answer'] = json_decode($temp['answer'], true);
            foreach ($temp['answer'] as $k => $v) {
                if (strpos($k, 'option') === 0) {
                    $tempKey = substr($k, 6);
                    $temp['answer'][$tempKey] = $v;
                    unset($temp['answer'][$k]);
                } elseif ($k == 'answer') {
                    $temp['solution'] = $v;
                    unset($temp['answer']['answer']);
                } elseif ($k == 'explain') {
                    $temp['explain'] = $v;
                    unset($temp['answer']['explain']);
                }
            }

            $type = QuestionModel::getTypeDesc($temp['type']);
            if ($type == '') {
                continue;
            }
            if ($type == QuestionModel::TYPE2) {
                $temp['option'] = json_decode($temp['option'], true);
                $temp['solution'] = explode(',', $temp['solution']);
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

    public static function clearGetQuestionCache(int $serviceId, int $paperId)
    {
        $cacheKey = 'AJYEXAM:PQ:GQ:' . $serviceId . ':' . $paperId;
        Redis::del($cacheKey);
        return true;
    }

    /**
     * 获取试卷中任一个题目的得分、是否正确
     * @param $questionRs 题目信息，包含答卷中content的全部信息
     * @param $answer 用户的回答
     */
    public static function getScore(array $questionRs, $answer)
    {
        if (!isset($questionRs['question_log_id']) || !isset($questionRs['score']) ||
            !isset($questionRs['type']) || !isset($questionRs['solution'])
        ) {
            return [];
        }

        $type = QuestionModel::getTypeDesc($questionRs['type']);

        $reply = '';
        $obtain = 0;
        $correct = false;
        if ($type == QuestionModel::TYPE1) {
            $reply = strtoupper($answer);
            if ($reply == $questionRs['solution']) {
                $obtain = $questionRs['score'];
                $correct = true;
            }
        } elseif ($type == QuestionModel::TYPE2) {
            if (!isset($questionRs['option']['option']) || !isset($questionRs['option']['score'])) {
                return [];
            }
            $reply = strtoupper($answer);
            $reply = explode(',', $reply);

            $obtain = $questionRs['score'];

            if ($questionRs['option']['option'] == 1) {
                // 漏选时则扣X分
                foreach ($questionRs['solution'] as $v) {
                    if (!in_array($v, $reply)) {
                        $obtain -= $questionRs['option']['score'];
                        break;
                    }
                }

                foreach ($reply as $v) {
                    if (!in_array($v, $questionRs['solution'])) {
                        $obtain -= $questionRs['option']['score'];
                        break;
                    }
                }
            } elseif ($questionRs['option']['option'] == 2) {
                // 漏选时每个选项扣X分
                foreach ($questionRs['solution'] as $v) {
                    if (!in_array($v, $reply)) {
                        $obtain -= $questionRs['option']['score'];
                    }
                }

                foreach ($reply as $v) {
                    if (!in_array($v, $questionRs['solution'])) {
                        $obtain -= $questionRs['option']['score'];
                    }
                }
            } else {
                $obtain = 0;
            }

            if ($obtain < 0) {
                $obtain = 0;
            }

            if ($obtain == $questionRs['score']) {
                $correct = true;
            }
        } elseif ($type == QuestionModel::TYPE3) {
            $reply = (bool) $answer;

            if ($reply === $questionRs['solution']) {
                $obtain = $questionRs['score'];
                $correct = true;
            }
        } else {
        }

        return [
            'reply' => $reply,
            'obtain' => $obtain,
            'correct' => $correct,
        ];
    }
}
