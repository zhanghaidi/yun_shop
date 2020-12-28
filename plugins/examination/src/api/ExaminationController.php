<?php

namespace Yunshop\Examination\api;

use app\common\components\ApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Yunshop\Examination\models\AnswerPaperContentModel;
use Yunshop\Examination\models\AnswerPaperModel;
use Yunshop\Examination\models\ExaminationModel;
use Yunshop\Examination\models\PaperModel;
use Yunshop\Examination\models\PaperQuestionModel;

class ExaminationController extends ApiController
{
    public function detail()
    {
        $memberId = (int) \YunShop::app()->getMemberId();
        if ($memberId <= 0) {
            return $this->errorJson('用户未授权登录', ['status' => 1]);
        }

        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->errorJson('参数错误', ['status' => 1]);
        }

        $examinationRs = ExaminationModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($examinationRs->id)) {
            return $this->errorJson('考试数据不存在', ['status' => 1]);
        }
        $nowTime = time();
        if (isset($examinationRs->start) && strtotime($examinationRs->start) > $nowTime) {
            return $this->errorJson('考试未开始', ['status' => 2]);
        }
        if (isset($examinationRs->end) && strtotime($examinationRs->end) < $nowTime) {
            return $this->errorJson('考试已结束', ['status' => 3]);
        }
        if ($examinationRs->open_status != 1) {
            return $this->errorJson('考试已关闭', ['status' => 4]);
        }
        $examinationContentRs = $examinationRs->content;

        // 获取分享配置中的 {成绩得分}
        if (strpos($examinationContentRs->share_title, '{成绩得分}') !== false ||
            strpos($examinationContentRs->share_describe, '{成绩得分}') !== false
        ) {
            $lastAnswerScore = AnswerPaperModel::select('id', 'score_obtain')->where([
                'member_id' => $memberId,
                'examination_id' => $examinationRs->id,
                'uniacid' => $examinationRs->uniacid,
                'status' => 2,
            ])->orderBy('id', 'desc')->first();
            if (isset($lastAnswerScore->id)) {
                $lastAnswerScore = $lastAnswerScore->score_obtain;
            } else {
                $lastAnswerScore = PaperModel::select('id', 'score')
                    ->where('id', $examinationRs->paper_id)->first();
                if (isset($lastAnswerScore->id)) {
                    $lastAnswerScore = $lastAnswerScore->score;
                } else {
                    $lastAnswerScore = 100;
                }
                $lastAnswerScore = intval(lastAnswerScore * mt_rand(1, 6) / 10);
            }
        }

        // 如果有一天内未完成答卷，则继续进行
        $answerPaperRs = AnswerPaperModel::select('id', 'status', 'created_at')->where([
            'member_id' => $memberId,
            'examination_id' => $examinationRs->id,
            'uniacid' => $examinationRs->uniacid,
        ])->orderBy('id', 'desc')->first();
        if (isset($answerPaperRs->id) && $answerPaperRs->status == 1) {
            $lastTime = strtotime($answerPaperRs->created_at);

            // 判断未完成试卷是否有效
            $isEffective = true;
            if ($examinationRs->duration > 0) {
                if (($nowTime - $lastTime) >= $examinationRs->duration * 60) {
                    $isEffective = false;
                }
            } else {
                if (($nowTime - $lastTime) >= 86400) {
                    $isEffective = false;
                }
            }

            if ($isEffective) {
                $answerContentRs = AnswerPaperContentModel::select('id', 'content')
                    ->where('answer_paper_id', $answerPaperRs->id)->first();
                if (isset($answerPaperRs->id)) {
                    $answerPaperContent = json_decode($answerContentRs->content, true);

                    $return = [
                        'id' => $answerPaperRs->id,
                        'name' => $examinationRs->name,
                        'url' => yz_tomedia($examinationRs->url),
                        'content' => html_entity_decode($examinationContentRs->content),
                        'duration' => $examinationRs->duration,
                        'is_question_score' => $examinationRs->is_question_score,
                        'question' => [],
                        'now_time' => time(),
                        'remained' => 0,
                        'share_title' => $examinationContentRs->share_title,
                        'share_describe' => $examinationContentRs->share_describe,
                        'share_image' => yz_tomedia($examinationContentRs->share_image),
                    ];
                    if ($return['duration'] > 0) {
                        $return['remained'] = $return['duration'] * 60 - ($nowTime - $lastTime);
                    }
                    $return['share_title'] = str_replace('{考试名称}', $examinationRs->name, $return['share_title']);
                    if (isset($lastAnswerScore)) {
                        $return['share_title'] = str_replace('{成绩得分}', $lastAnswerScore, $return['share_title']);
                    }
                    $return['share_describe'] = str_replace('{考试名称}', $examinationRs->name, $return['share_describe']);
                    if (isset($lastAnswerScore)) {
                        $return['share_describe'] = str_replace('{成绩得分}', $lastAnswerScore, $return['share_describe']);
                    }

                    foreach ($answerPaperContent as $k => $v) {
                        $tempAnswer = [];
                        foreach ($v['answer'] as $k2 => $v2) {
                            $tempAnswer[] = [
                                'key' => $k2,
                                'value' => $v2,
                            ];
                        }
                        $answerPaperContent[$k]['answer'] = $tempAnswer;
                        unset($answerPaperContent[$k]['question_id']);
                        unset($answerPaperContent[$k]['obtain']);
                        unset($answerPaperContent[$k]['correct']);
                    }
                    $return['question'] = $answerPaperContent;
                    return $this->successJson('成功', $return);
                }
            }
        }

        // 考试次数限制
        if ($examinationRs->frequency > 0) {
            $countRs = AnswerPaperModel::where([
                'member_id' => $memberId,
                'examination_id' => $examinationRs->id,
                'uniacid' => $examinationRs->uniacid,
            ])->count();
            if ($countRs >= $examinationRs->frequency) {
                return $this->errorJson('本次考试，每人仅能参与' . $examinationRs->frequency . '次', [
                    'status' => 5,
                    'id' => $answerPaperRs->id,
                ]);
            }
        }

        // 重考间隔限制
        if ($examinationRs->interval > 0) {
            $lastRs = AnswerPaperModel::select('id', 'created_at')->where([
                'member_id' => $memberId,
                'examination_id' => $examinationRs->id,
                'uniacid' => $examinationRs->uniacid,
                'status' => 2,
            ])->orderBy('id', 'desc')->first();
            if (isset($lastRs->id)) {
                $lastTime = strtotime($lastRs->created_at);
                if (($nowTime - $lastTime) <= ($examinationRs->interval * 3600)) {
                    $lastTime = $nowTime - $lastTime;
                    $lastTime = $examinationRs->interval * 3600 - $lastTime;
                    $lastTime = ceil($lastTime / 60);
                    if ($lastTime > 60 * 24) {
                        $lastTime = ceil($lastTime / 60 / 24) . '天';
                    } elseif ($lastTime > 60) {
                        $lastTime = ceil($lastTime / 60) . '小时';
                    } else {
                        $lastTime .= '分钟';
                    }
                    return $this->errorJson('考试过于频繁，请' . $lastTime . '后重试', [
                        'status' => 6,
                        'id' => $lastRs->id,
                    ]);
                }
            }
        }

        $paperRs = PaperModel::select('id', 'random_question')->where([
            'id' => $examinationRs->paper_id,
            'uniacid' => $examinationRs->uniacid,
        ])->first();
        if (!isset($paperRs->id)) {
            return $this->errorJson('考试的试卷还没有准备好，请稍后再试', ['status' => 1]);
        }

        $paperQuestionRs = PaperQuestionModel::getQuestion($examinationRs->uniacid, $paperRs->id);
        if (!isset($paperQuestionRs['code']) || $paperQuestionRs['code'] != 0 ||
            !isset($paperQuestionRs['data'])
        ) {
            return $this->errorJson(isset($paperQuestionRs['msg']) ? $paperQuestionRs['msg'] : '试卷准备出错了，请稍后再试', ['status' => 1]);
        }
        $paperQuestionRs = $paperQuestionRs['data'];

        if ($paperRs->random_question == 1) {
            shuffle($paperQuestionRs);
        }

        DB::beginTransaction();
        try {
            $answer = new AnswerPaperModel;
            $answer->uniacid = $examinationRs->uniacid;
            $answer->examination_id = $examinationRs->id;
            $answer->member_id = $memberId;
            $answer->score_total = array_sum(array_column($paperQuestionRs, 'score'));
            $answer->score_obtain = 0;
            $answer->question_total = count($paperQuestionRs);
            $answer->question_correct = 0;
            $answer->status = 1;
            $answer->save();
            if (!isset($answer->id) || $answer->id <= 0) {
                throw new Exception('生成答卷错误，请稍后重试');
            }

            $answerContent = new AnswerPaperContentModel;
            $answerContent->answer_paper_id = $answer->id;
            $answerContent->content = json_encode(AnswerPaperContentModel::generateAnswerContent($paperQuestionRs));
            $answerContent->save();
            if (!isset($answerContent->id) || $answerContent->id <= 0) {
                throw new Exception('答卷生成错误，请稍后重试');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorJson($e->getMessage(), ['status' => 1]);
        }

        $return = [
            'id' => $answer->id,
            'name' => $examinationRs->name,
            'url' => yz_tomedia($examinationRs->url),
            'content' => html_entity_decode($examinationContentRs->content),
            'duration' => $examinationRs->duration,
            'is_question_score' => $examinationRs->is_question_score,
            'question' => [],
            'now_time' => time(),
            'remained' => 0,
            'share_title' => $examinationContentRs->share_title,
            'share_describe' => $examinationContentRs->share_describe,
            'share_image' => yz_tomedia($examinationContentRs->share_image),
        ];
        if ($return['duration'] > 0) {
            $reutrn['remained'] = $return['duration'] * 60;
        }
        $return['share_title'] = str_replace('{考试名称}', $examinationRs->name, $return['share_title']);
        if (isset($lastAnswerScore)) {
            $return['share_title'] = str_replace('{成绩得分}', $lastAnswerScore, $return['share_title']);
        }
        $return['share_describe'] = str_replace('{考试名称}', $examinationRs->name, $return['share_describe']);
        if (isset($lastAnswerScore)) {
            $return['share_describe'] = str_replace('{成绩得分}', $lastAnswerScore, $return['share_describe']);
        }

        foreach ($paperQuestionRs as $k => $v) {
            $tempAnswer = [];
            foreach ($v['answer'] as $k2 => $v2) {
                $tempAnswer[] = [
                    'key' => $k2,
                    'value' => $v2,
                ];
            }
            $paperQuestionRs[$k]['answer'] = $tempAnswer;
            unset($paperQuestionRs[$k]['question_id']);
            unset($paperQuestionRs[$k]['reply']);
            unset($paperQuestionRs[$k]['obtain']);
            unset($paperQuestionRs[$k]['correct']);
        }
        $return['question'] = $paperQuestionRs;
        return $this->successJson('成功', $return);
    }

    public function submit()
    {
        $memberId = (int) \YunShop::app()->getMemberId();
        if ($memberId <= 0) {
            return $this->errorJson('用户未授权登录', ['status' => 1]);
        }

        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->errorJson('参数错误', ['status' => 1]);
        }

        $answer = \YunShop::request()->question;
        $answer = json_decode($answer, true);
        if ($answer === null) {
            return $this->errorJson('答案提交错误', ['status' => 1]);
        }

        // 延时互斥锁
        $lockKey = 'AJYEXAM:SUBMIT:' . $memberId . ':' . $id . ':' . date('ymdHi');
        $lockRs = $this->DelayMutex($lockKey);
        if ($lockRs === false) {
            return $this->errorJson('答题太快了', ['status' => 2]);
        }

        $paperRs = AnswerPaperModel::where([
            'id' => $id,
            'member_id' => $memberId,
        ])->first();
        if (!isset($paperRs->id)) {
            return $this->errorJson('答卷未找到', ['status' => 1]);
        }
        if ($paperRs->status != 1) {
            return $this->errorJson('答卷状态错误', ['status' => 1]);
        }
        $nowTime = time();
        $lastTime = strtotime($paperRs->created_at);
        if (($nowTime - $lastTime) >= 86400 * 2) {
            return $this->errorJson('答卷作答时间过长', ['status' => 3]);
        }

        $contentRs = AnswerPaperContentModel::where('answer_paper_id', $paperRs->id)->first();
        if (!isset($contentRs->id)) {
            return $this->errorJson('试卷找不到了', ['status' => 1]);
        }
        $questionContent = json_decode($contentRs->content, true);

        // 系统阅卷 - 批改
        foreach ($questionContent as $k1 => $v1) {
            if (!isset($v1['question_log_id']) || !isset($v1['reply'])
            ) {
                continue;
            }
            if (!is_string($v1['reply']) || $v1['reply'] !== '') {
                continue;
            }
            $tempAnswer = null;
            foreach ($answer as $v2) {
                if (!isset($v2['question_log_id']) || !isset($v2['answer'])) {
                    continue;
                }
                if ($v2['question_log_id'] != $v1['question_log_id']) {
                    continue;
                }
                $tempAnswer = $v2['answer'];
                break;
            }
            if (is_null($tempAnswer)) {
                continue;
            }

            $scoreAndCorrect = PaperQuestionModel::getScore($v1, $tempAnswer);
            if (isset($scoreAndCorrect['reply'])) {
                $v1 = array_merge($v1, $scoreAndCorrect);
                $questionContent[$k1] = $v1;
            }
        }

        // 保存阅卷结果
        DB::beginTransaction();
        try {
            $contentRs->content = json_encode($questionContent);
            $contentRs->save();

            $paperRs->score_obtain = array_sum(array_column($questionContent, 'obtain'));
            $paperRs->question_correct = count(array_filter(array_column($questionContent, 'correct')));
            $totalAnswer = array_column($questionContent, 'reply');
            foreach ($totalAnswer as $k => $v) {
                if ($v === '') {
                    unset($totalAnswer[$k]);
                }
            }
            if (count($totalAnswer) == $paperRs->question_total) {
                $paperRs->status = 2;
            }
            $paperRs->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->errorJson($e->getMessage(), ['status' => 1]);
        }

        // 考试超时等处理
        $examinationRs = ExaminationModel::select(
            'id', 'start', 'end', 'duration', 'is_score', 'status'
        )->where('id', $paperRs->examination_id)->first();
        if (!isset($examinationRs->id)) {
            return $this->errorJson('考试信息获取错误', ['status' => 1]);
        }
        if ($examinationRs->open_status != 1) {
            return $this->errorJson('考试已经结束了', ['status' => 4]);
        }
        if ($examinationRs->duration > 0) {
            $tempStart = strtotime($paperRs->created_at);
            $tempEnd = strtotime($paperRs->updated_at);
            if (($tempEnd - $tempStart) >= $examinationRs->duration * 60) {
                return $this->errorJson('你已经过了交卷时间', ['status' => 5]);
            }
        }

        $return = [
            'score_total' => $paperRs->score_total,
            'score_obtain' => $paperRs->score_obtain,
            'question_total' => $paperRs->question_total,
            'question_correct' => $paperRs->question_correct,
        ];
        if ($examinationRs->is_score != 1) {
            $return['score_obtain'] = -1;
        }
        return $this->successJson('成功', $return);
    }

    private function DelayMutex(string $cacheKey, int $maxDelay = 10)
    {
        if ($maxDelay <= 0) {
            $maxDelay = 10;
        }

        $isLock = false;
        for ($i == 0; $i < $maxDelay; $i++) {
            $cacheRs = Redis::setnx($cacheKey, 1);
            if ($cacheRs != 1) {
                sleep(1);
                continue;
            } else {
                Redis::expire($cacheKey, intval(ceil($maxDelay / 3)));
                $isLock = true;
                break;
            }
        }
        return $isLock;
    }

    public function answer()
    {
        $memberId = (int) \YunShop::app()->getMemberId();
        if ($memberId <= 0) {
            return $this->errorJson('用户未授权登录', ['status' => 1]);
        }

        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->errorJson('参数错误', ['status' => 1]);
        }

        $answerRs = AnswerPaperModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $memberId,
            'status' => 2,
        ])->first();
        if (!isset($answerRs->id)) {
            return $this->errorJson('答卷数据不存在', ['status' => 1]);
        }

        $examinationRs = ExaminationModel::select('id', 'name', 'url', 'end', 'is_score')->where([
            'id' => $answerRs->examination_id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($examinationRs->id)) {
            return $this->errorJson('考试数据不存在', ['status' => 1]);
        }
        $examinationContentRs = $examinationRs->content;

        $return = [
            'id' => $answerRs->id,
            'name' => $examinationRs->name,
            'url' => yz_tomedia($examinationRs->url),
            'content' => html_entity_decode($examinationContentRs->content),
            'question' => [],
            'score_total' => $answerRs->score_total,
            'score_obtain' => $answerRs->score_obtain,
            'question_total' => $answerRs->question_total,
            'question_correct' => $answerRs->question_correct,
            'complete_at' => strtotime($answerRs->updated_at),
            'end_at' => isset($examinationRs->end) ? strtotime($examinationRs->end) : 0,
            'share_title' => $examinationContentRs->share_title,
            'share_describe' => $examinationContentRs->share_describe,
            'share_image' => yz_tomedia($examinationContentRs->share_image),
        ];
        if ($examinationRs->is_score != 1) {
            $return['score_obtain'] = -1;
        }

        $return['share_title'] = str_replace('{考试名称}', $examinationRs->name, $return['share_title']);
        $return['share_title'] = str_replace('{成绩得分}', $answerRs->score_obtain, $return['share_title']);
        $return['share_describe'] = str_replace('{考试名称}', $examinationRs->name, $return['share_describe']);
        $return['share_describe'] = str_replace('{成绩得分}', $answerRs->score_obtain, $return['share_describe']);

        $contentRs = json_decode($answerRs->content->content, true);
        foreach ($contentRs as $k => $v) {
            $tempAnswer = [];
            foreach ($v['answer'] as $k2 => $v2) {
                $tempAnswer[] = [
                    'key' => $k2,
                    'value' => $v2,
                ];
            }
            $contentRs[$k]['answer'] = $tempAnswer;
            unset($contentRs[$k]['question_id']);
        }
        $return['question'] = $contentRs;
        return $this->successJson('成功', $return);
    }
}
