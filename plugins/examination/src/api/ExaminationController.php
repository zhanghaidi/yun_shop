<?php

namespace Yunshop\Examination\api;

use app\common\components\ApiController;
use Exception;
use Illuminate\Support\Facades\DB;
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
            return $this->errorJson('用户未授权登录');
        }

        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->errorJson('参数错误');
        }

        $examinationRs = ExaminationModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($examinationRs->id) || $examinationRs->open_status != 1) {
            return $this->errorJson('考试已结束');
        }

        // 考试次数限制
        if ($examinationRs->frequency > 0) {
            $countRs = AnswerPaperModel::where([
                'member_id' => $memberId,
                'examination_id' => $examinationRs->id,
                'uniacid' => $examinationRs->uniacid,
            ])->count();
            if ($countRs >= $examinationRs->frequency) {
                return $this->errorJson('本次考试，每人仅能参与' . $examinationRs->frequency . '次');
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
                $nowTime = time();
                $lastTime = strtotime($lastRs->created_at);
                if (($nowTime - $lastTime) <= ($examinationRs->interval * 3600)) {
                    $lastTime = $nowTime - $lastTime;
                    $lastTime = ceil($lastTime / 60);
                    return $this->errorJson('考试次数过于频繁，请' . $lastTime . '分钟后重试');
                }
            }
        }

        $paperRs = PaperModel::select('id', 'random_question')->where([
            'id' => $examinationRs->paper_id,
            'uniacid' => $examinationRs->uniacid,
        ])->first();
        if (!isset($paperRs->id)) {
            return $this->errorJson('考试的试卷还没有准备好，请稍后再试');
        }

        $paperQuestionRs = PaperQuestionModel::getQuestion($examinationRs->uniacid, $paperRs->id);
        if (!isset($paperQuestionRs['code']) || $paperQuestionRs['code'] != 0 ||
            !isset($paperQuestionRs['data'])
        ) {
            return $this->errorJson(isset($paperQuestionRs['msg']) ? $paperQuestionRs['msg'] : '试卷准备出错了，请稍后再试');
        }
        $paperQuestionRs = $paperQuestionRs['data'];

        if ($paperRs->random_question == 1) {
            shuffle($paperQuestionRs);
        }

        DB::beginTransaction();
        try {
            $answer = new AnswerPaperModel;
            $answer->uniacid = \YunShop::app()->uniacid;
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

            return $this->errorJson($e->getMessage());
        }

        $return = [
            'id' => $answer->id,
            'name' => $examinationRs->name,
            'url' => yz_tomedia($examinationRs->url),
            'is_question_score' => $examinationRs->is_question_score,
            'is_score' => $examinationRs->is_score,
            'is_question' => $examinationRs->is_question,
            'is_answer' => $examinationRs->is_answer,
            'question' => [],
        ];

        foreach ($paperQuestionRs as $k => $v) {
            unset($paperQuestionRs[$k]['question_id']);
            unset($paperQuestionRs[$k]['reply']);
            unset($paperQuestionRs[$k]['obtain']);
            unset($paperQuestionRs[$k]['correct']);
            unset($paperQuestionRs[$k]['answer']['answer']);
            unset($paperQuestionRs[$k]['answer']['explain']);
        }
        $return['question'] = $paperQuestionRs;
        return $this->successJson('成功', $return);
    }
}
