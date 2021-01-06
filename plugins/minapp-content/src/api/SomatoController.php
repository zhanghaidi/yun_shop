<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use Illuminate\Support\Facades\Redis;
use Yunshop\MinappContent\models\AnswerModel;
use Yunshop\MinappContent\models\QuestionBankModel;
use Yunshop\MinappContent\models\SomatoQuestionModel;

class SomatoController extends ApiController
{
    protected $publicAction = ['question'];
    protected $ignoreAction = ['question'];

    public function question()
    {
        $gender = intval(\YunShop::request()->gender);
        if (!in_array($gender, [1, 2])) {
            return $this->errorJson('性别获取失败');
        }

        $cacheKey = 'AJX:MAC:A:SOMATOC:Q:' . $gender;
        $result = Redis::get($cacheKey);
        if ($result !== false && $result !== null) {
            return $this->successJson('获取题库成功', json_decode($result, true));
        }

        // TODO 暂不区分题库 uniacid
        $questionRs = QuestionBankModel::select(
            'id', 'list_order', 'title', 'option1_score', 'option2_score',
            'option3_score', 'option4_score', 'option5_score', 'options', 'somato_type_id'
        )->whereIn('gender', [0, $gender])
            ->orderBy('list_order', 'asc')->get()->toArray();
        foreach ($questionRs as &$v) {
            $tempType = explode(',', $v['somato_type_id']);
            if (!isset($tempType[0])) {
                $v['somato_type'] = [];
            } else {
                $v['somato_type'] = SomatoQuestionModel::select('somato_type_id', 'question_id', 'score_sort')
                    ->whereIn('somato_type_id', $tempType)
                    ->where('question_id', $v['id'])->get()->toArray();
            }

            $v['options'] = [
                [
                    'name' => 'A: 没有（根本不）',
                    'score' => $v['option1_score'],
                ],
                [
                    'name' => 'B: 很少（有一点）',
                    'score' => $v['option2_score'],
                ],
                [
                    'name' => 'C: 有时（有些）',
                    'score' => $v['option3_score'],
                ],
                [
                    'name' => 'D: 经常（相当）',
                    'score' => $v['option4_score'],
                ],
                [
                    'name' => 'E: 总是（非常）',
                    'score' => $v['option5_score'],
                ],
            ];
        }
        unset($v);
        Redis::setex($cacheKey, mt_rand(300, 600), json_encode($questionRs));

        return $this->successJson('获取题库成功', $questionRs);
    }

    public function answerStatus()
    {
        $answer = AnswerModel::select('id')->where([
            'user_id' => \YunShop::app()->getMemberId(),
            'uniacid' => \YunShop::app()->uniacid,
        ])->where('ture_somato_type_id', '>', 0)->first();
        if (isset($answer->id)) {
            return $this->successJson('用户已测评', ['answerStatus' => 1]);
        } else {
            return $this->successJson('用户未测评', ['answerStatus' => 0]);
        }
    }

    public function answer()
    {
        $rawAnswer = \YunShop::request()->answer;
        if (!isset($rawAnswer)) {
            return $this->errorJson('答题参数丢失');
        }
        $rawAnswer = html_entity_decode($rawAnswer);
        $answer = json_decode($rawAnswer, true);
        if ($answer == false) {
            return $this->errorJson('答题参数错误');
        }
        // 体质数组
        $somato = array();
        // 所有体质转换分数组
        $score = array();
        // 平和体质数组
        $gentle = array();
        // 偏颇体质数组
        $biased = array();

        var_dump($answer);
        var_dump(isset($answer));
        exit;
    }
}
