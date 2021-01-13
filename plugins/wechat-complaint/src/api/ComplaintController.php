<?php

namespace Yunshop\WechatComplaint\api;

use app\common\components\ApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Yunshop\WechatComplaint\models\AnswerPaperModel;
use Yunshop\WechatComplaint\models\ComplaintItemModel;
use Yunshop\WechatComplaint\models\ComplaintProjectModel;

class ComplaintController extends ApiController
{
    public function options()
    {
        $memberId = (int) \YunShop::app()->getMemberId();
        if ($memberId <= 0) {
            return $this->errorJson('用户未授权登录', ['status' => 1]);
        }

        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->errorJson('参数错误', ['status' => 1]);
        }

        $projectRs = ComplaintProjectModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($projectRs->id)) {
            return $this->errorJson('投诉功能已失效', ['status' => 1]);
        }

        $listRs = (new ComplaintItemModel)->getOrderList(\YunShop::app()->uniacid);

        foreach ($listRs as &$v1) {
            unset($v1['uniacid'], $v1['type'], $v1['submit_mode'], $v1['order'], $v1['created_at'], $v1['updated_at'], $v1['deleted_at']);
            if (!isset($v1['children']) || !is_array($v1['children'])) {
                unset($v1['children']);
                continue;
            }

            foreach ($v1['children'] as &$v2) {
                unset($v2['uniacid'], $v2['type'], $v2['submit_mode'], $v2['order'], $v2['created_at'], $v2['updated_at'], $v2['deleted_at']);
                if (!isset($v2['children']) || !is_array($v2['children'])) {
                    unset($v2['children']);
                    continue;
                }

                foreach ($v2['children'] as &$v3) {
                    unset($v3['uniacid'], $v3['type'], $v3['submit_mode'], $v3['order'], $v3['created_at'], $v3['updated_at'], $v3['deleted_at']);
                    unset($v3['children']);
                }
                unset($v3);

                $v2['children'] = array_values($v2['children']);
            }
            unset($v2);
            $v1['children'] = array_values($v1['children']);
        }
        unset($v1);
        $listRs = array_values($listRs);
        return $this->successJson('成功', $listRs);
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
            AnswerPaperModel::completeToInvalid($paperRs->uniacid, $paperRs->id);
            return $this->errorJson('考试信息获取错误', ['status' => 1]);
        }
        if ($examinationRs->open_status != 1) {
            AnswerPaperModel::completeToInvalid($paperRs->uniacid, $paperRs->id);
            return $this->errorJson('考试已经结束了', ['status' => 4]);
        }
        if ($examinationRs->duration > 0) {
            $tempStart = strtotime($paperRs->created_at);
            $tempEnd = strtotime($paperRs->updated_at);
            if (($tempEnd - $tempStart) >= $examinationRs->duration * 60) {
                AnswerPaperModel::completeToInvalid($paperRs->uniacid, $paperRs->id);
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
            $answerPaperRs = AnswerPaperModel::select('id', 'examination_id')->where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (isset($answerPaperRs->id)) {
                $userAnserPaperRs = AnswerPaperModel::select('id')->where([
                    'member_id' => $memberId,
                    'uniacid' => \YunShop::app()->uniacid,
                    'status' => 2,
                ])->orderBy('id', 'desc')->first();
                if (isset($userAnserPaperRs->id)) {
                    return $this->errorJson('查看自己的答卷', [
                        'status' => 2,
                        'id' => $userAnserPaperRs->id,
                        'examination_id' => $answerPaperRs->examination_id,
                    ]);
                } else {
                    return $this->errorJson('没有答卷，请作答', [
                        'status' => 3,
                        'examination_id' => $answerPaperRs->examination_id,
                    ]);
                }
            } else {
                return $this->errorJson('答卷数据不存在', ['status' => 1]);
            }
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
            'examination_id' => $examinationRs->id,
            'share_title' => $examinationContentRs->share_title_after,
            'share_describe' => $examinationContentRs->share_describe_after,
            'share_image' => yz_tomedia($examinationContentRs->share_image_after),
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
