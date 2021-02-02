<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use app\common\models\Goods;
use app\common\AppAppExceptions\AppAppAppException;
use app\common\AppAppExceptions\ShopAppAppException;
use Illuminate\Support\Facades\Redis;
use Yunshop\MinappContent\api\IndexController;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\AnswerModel;
use Yunshop\MinappContent\models\ArticleModel;
use Yunshop\MinappContent\models\DiseaseModel;
use Yunshop\MinappContent\models\LabelModel;
use Yunshop\MinappContent\models\QuestionBankModel;
use Yunshop\MinappContent\models\SomatoQuestionModel;
use Yunshop\MinappContent\models\SomatoTypeModel;
use Illuminate\Support\Facades\DB;

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

        $questionRs = QuestionBankModel::select(
            'id', 'list_order', 'title', 'option1_score', 'option2_score',
            'option3_score', 'option4_score', 'option5_score', 'options', 'somato_type_id'
        )->whereIn('gender', [0, $gender])
            ->where('uniacid', \YunShop::app()->uniacid)
            ->orderBy('list_order', 'asc')->get()->toArray();
        foreach ($questionRs as &$v) {
            $tempType = explode(',', $v['somato_type_id']);
            if (!isset($tempType[0])) {
                $v['somato_type'] = [];
            } else {
                $v['somato_type'] = SomatoQuestionModel::select('somato_type_id', 'question_id', 'score_sort')
                    ->whereIn('somato_type_id', $tempType)
                    ->where('question_id', $v['id'])
                    ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
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

        $tempTypeRs = SomatoTypeModel::select('id', 'name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();
        $typeRs = [];
        // 平和体质ID
        $gentleId = 0;
        foreach ($tempTypeRs as $v) {
            $typeRs[$v['id']] = $v['name'];
            if ($v['name'] == '平和质') {
                $gentleId = $v['id'];
            }
        }
        unset($tempTypeRs);

        foreach ($answer as $v) {
            if (!isset($v['somato_type_id']) || !isset($v['score'])) {
                continue;
            }
            if (!isset($typeRs[$v['somato_type_id']])) {
                continue;
            }

            !isset($somato[$v['somato_type_id']]['raw_score']) && $somato[$v['somato_type_id']]['raw_score'] = 0;
            !isset($somato[$v['somato_type_id']]['nums']) && $somato[$v['somato_type_id']]['nums'] = 0;
            !isset($somato[$v['somato_type_id']]['answer']) && $somato[$v['somato_type_id']]['answer'] = [];

            $somato[$v['somato_type_id']]['raw_score'] += $v['score'];
            $somato[$v['somato_type_id']]['nums'] += 1;
            $somato[$v['somato_type_id']]['derived_score'] = AnswerModel::derivedScore(
                $somato[$v['somato_type_id']]['raw_score'],
                $somato[$v['somato_type_id']]['nums']
            );
            $somato[$v['somato_type_id']]['answer'][] = $v;

            if ($typeRs[$v['somato_type_id']] == '平和质') {
                $somato[$v['somato_type_id']]['somato_type'] = '平和质';

                if ($somato[$v['somato_type_id']]['derived_score'] < 60) {
                    $somato[$v['somato_type_id']]['status'] = 0;
                } else {
                    $somato[$v['somato_type_id']]['status'] = 1;
                }

                $gentle[$v['somato_type_id']]['somato_type'] = $somato[$v['somato_type_id']]['somato_type'];
                $gentle[$v['somato_type_id']]['derived_score'] = $somato[$v['somato_type_id']]['derived_score'];
                $gentle[$v['somato_type_id']]['status'] = $somato[$v['somato_type_id']]['status'];
                $gentleStatus = $somato[$v['somato_type_id']]['status'];
                $gentleDerivedScore = $somato[$v['somato_type_id']]['derived_score'];
            } else {
                if (in_array($typeRs[$v['somato_type_id']], ['气虚质', '阳虚质', '阴虚质', '痰湿质', '湿热质', '血瘀质', '气郁质', '特禀质'])) {
                    $somato[$v['somato_type_id']]['somato_type'] = $typeRs[$v['somato_type_id']];
                } else {
                    continue;
                }

                if ($somato[$v['somato_type_id']]['derived_score'] < 30) {
                    $somato[$v['somato_type_id']]['status'] = 0;
                } elseif (
                    30 <= $somato[$v['somato_type_id']]['derived_score'] &&
                    $somato[$v['somato_type_id']]['derived_score'] < 40
                ) {
                    $somato[$v['somato_type_id']]['status'] = 1;
                } else {
                    $somato[$v['somato_type_id']]['status'] = 2;
                }

                $biased[$v['somato_type_id']]['type_id'] = $v['somato_type_id'];
                $biased[$v['somato_type_id']]['somato_type'] = $somato[$v['somato_type_id']]['somato_type'];
                $biased[$v['somato_type_id']]['derived_score'] = $somato[$v['somato_type_id']]['derived_score'];
                $biased[$v['somato_type_id']]['status'] = $somato[$v['somato_type_id']]['status'];
            }

            // 拿到转换分
            $score[$v['somato_type_id']]['type_id'] = $v['somato_type_id'];
            $score[$v['somato_type_id']]['somato_type'] = $somato[$v['somato_type_id']]['somato_type'];
            $score[$v['somato_type_id']]['derived_score'] = $somato[$v['somato_type_id']]['derived_score'];
            $score[$v['somato_type_id']]['status'] = $somato[$v['somato_type_id']]['status'];
        }

        // 体质结果按转换分从高到低进行排序
        $scoreSort = AnswerModel::arraySortByOneField($score, 'derived_score');
        // 偏颇体质按转化分最高排序
        $biased = AnswerModel::arraySortByOneField($biased, 'derived_score');

        // 确诊体质 分数>=40
        $trueSomatoType = AnswerModel::getTrueSomatoType($biased);
        $trueSomatoTypeName = AnswerModel::getSomatoTypeByField($trueSomatoType, 'somato_type');
        $trueSomatoTypeId = AnswerModel::getSomatoTypeByField($trueSomatoType, 'type_id');

        // 倾向体质 分数<40 >=30
        $hasSomatoType = AnswerModel::getHasSomatoType($biased);
        $hasSomatoTypeName = AnswerModel::getSomatoTypeByField($hasSomatoType, 'somato_type');
        $hasSomatoTypeId = AnswerModel::getSomatoTypeByField($hasSomatoType, 'type_id');

        // 平和体质状态判断
        $isGentleType = AnswerModel::getGentleSomatoType(
            isset($gentleStatus) ? $gentleStatus : 999,
            $hasSomatoType, $trueSomatoType
        );

        if ($isGentleType == 3) {
            // 平和质
            $tureContent = '您的体质是：平和质';
            $tureSomatoTypeId = $gentleId;
            $tureSomatoDerivedScore = isset($gentleDerivedScore) ? $gentleDerivedScore : 0;
            $hasContent = '';
            $hasSomatoTypeId = '';
        } elseif ($isGentleType == 2) {
            // 平和质 有倾向体质
            $tureContent = '您的体质基本是：平和质';
            $tureSomatoTypeId = $gentleId;
            $tureSomatoDerivedScore = isset($gentleDerivedScore) ? $gentleDerivedScore : 0;
            $hasContent = '（ 有 ' . implode(',', $hasSomatoTypeName) . ' 倾向）';
            $hasSomatoTypeId = implode(',', $hasSomatoTypeId);
        } else {
            // 非平和质 获取确认体质
            $tureName = isset($trueSomatoTypeName[0]) ? $trueSomatoTypeName[0] : $hasSomatoTypeName[0];
            $tureContent = '您的体质是: ' . $tureName;
            $tureSomatoTypeId = isset($trueSomatoTypeId[0]) ? $trueSomatoTypeId[0] : $hasSomatoTypeId[0];
            $tureSomatoDerivedScore = isset($trueSomatoType[0]['derived_score']) ? $trueSomatoType[0]['derived_score'] : $hasSomatoType[0]['derived_score'];
            // 是否有倾向体质
            if (isset($hasSomatoType[0])) {
                $hasContent = '（ 有 ' . implode(',', $hasSomatoTypeName) . ' 倾向）';
                $hasSomatoTypeId = implode(',', $hasSomatoTypeId);
            } else {
                $hasContent = '';
                $hasSomatoTypeId = '';
            }
        }

        //平和质>=60  其他8种体质转化分均﹤30分 是
        //平和质>=60  其他8种体质转化分均﹤40分 基本是
        //否则不是平和质

        //转化分≧40分    是
        //转化分30~39分    倾向是 >=30<40
        //转化分﹤30分    否
        $answerRs = AnswerModel::where([
            'user_id' => \YunShop::app()->getMemberId(),
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (isset($answerRs->id)) {
        } else {
            $answerRs = new AnswerModel;
            $answerRs->user_id = \YunShop::app()->getMemberId();
            $answerRs->uniacid = \YunShop::app()->uniacid;
        }
        $answerRs->answers = $rawAnswer;
        $answerRs->derived_score = json_encode($scoreSort);
        $answerRs->is_gentle_type = $isGentleType;
        $answerRs->ture_somato_type_id = $tureSomatoTypeId;
        $answerRs->has_somato_type_id = $hasSomatoTypeId;
        $answerRs->ture_somato_type = json_encode($trueSomatoType);
        $answerRs->has_somato_type = json_encode($hasSomatoType);
        $answerRs->ture_somato_type_content = $tureContent;
        $answerRs->has_somato_type_content = $hasContent;
        $answerRs->ture_somato_derived_score = $tureSomatoDerivedScore;
        $answerRs->save();
        if (!isset($answerRs->id) || $answerRs->id <= 0) {
            return $this->errorJson('成绩入库失败', ['answerStatus' => 0]);
        } else {
            return $this->successJson('测评成功', ['answerStatus' => 1]);
        }
    }

    public function typeReport()
    {
        $answer = AnswerModel::select(
            'id', 'ture_somato_type_id', 'ture_somato_type_content', 'ture_somato_derived_score'
        )->where([
            'user_id' => \YunShop::app()->getMemberId(),
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($answer->id)) {
            return $this->errorJson('用户未测评', ['answerStatus' => 0]);
        }

        $return = [
            'ture_somato_type_id' => $answer->ture_somato_type_id,
            'ture_somato_type_content' => $answer->ture_somato_type_content,
            'ture_somato_derived_score' => $answer->ture_somato_derived_score,
        ];

        $typeRs = SomatoTypeModel::select('id', 'title', 'name', 'description', 'symptom', 'disease')->where([
            'id' => $answer->ture_somato_type_id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (isset($typeRs->id)) {
            $return['title'] = $typeRs->title;
            $return['name'] = $typeRs->name;
            $return['description'] = $typeRs->description;
            $return['symptom'] = $typeRs->symptom;
            $return['disease'] = $typeRs->disease;

            $return['symptom'] = explode(',', $return['symptom']);
            $return['symptom'] = array_values(array_filter($return['symptom']));
            $return['disease'] = explode(',', $return['disease']);
            $return['disease'] = array_values(array_filter($return['disease']));

            if (isset($return['symptom'][0])) {
                $return['symptoms'] = LabelModel::select('id', 'name')
                    ->whereIn('id', $return['symptom'])
                    ->where('uniacid', \YunShop::app()->uniacid)->get();
            }
            if (isset($return['disease'][0])) {
                $return['diseases'] = DiseaseModel::select('id', 'name')
                    ->whereIn('id', $return['disease'])
                    ->where('uniacid', \YunShop::app()->uniacid)->get();
            }
        }
        return $this->successJson('获取体质报告成功', $return);
    }

    public function health()
    {
        $answer = AnswerModel::select('id', 'ture_somato_type_id')->where([
            'user_id' => \YunShop::app()->getMemberId(),
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($answer->id)) {
            return $this->errorJson('用户未测评', ['answerStatus' => 0]);
        }

        $somatoTypeRs = SomatoTypeModel::select(
            'id', 'name', 'content', 'recommend_goods', 'recommend_article', 'recommend_acupotion'
        )->where([
            'id' => $answer->ture_somato_type_id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($somatoTypeRs->id)) {
            return $this->errorJson('用户测评数据错误', ['answerStatus' => 0]);
        }
        $somatoTypeRs->recommend_goods = array_values(array_filter(explode(',', $somatoTypeRs->recommend_goods)));
        $somatoTypeRs->recommend_article = array_values(array_filter(explode(',', $somatoTypeRs->recommend_article)));
        $somatoTypeRs->recommend_acupotion = array_values(array_filter(explode(',', $somatoTypeRs->recommend_acupotion)));

        $somatoTypeRs->content = html_entity_decode($somatoTypeRs->content);
        $somatoTypeRs->to_type_id = 5;
        $somatoTypeRs->acupotions = [];
        $somatoTypeRs->goods = [];
        $somatoTypeRs->articles = [];
        if (isset($somatoTypeRs->recommend_acupotion[0])) {
            $somatoTypeRs->acupotions = AcupointModel::select('id', 'name', 'get_position', 'image')
                ->whereIn('id', $somatoTypeRs->recommend_acupotion)
                ->where('uniacid', \YunShop::app()->uniacid)->get();
        }
        if (isset($somatoTypeRs->recommend_goods[0])) {
            $somatoTypeRs->goods = Goods::select('id', 'title', 'thumb', 'status', 'virtual_sales', 'show_sales', 'price', 'deleted_at')
                ->whereIn('id', $somatoTypeRs->recommend_goods)
                ->where('uniacid', \YunShop::app()->uniacid)
                ->where('status', 1)
                ->whereNull('deleted_at')->get();
        }
        if (isset($somatoTypeRs->recommend_article[0])) {
            $somatoTypeRs->articles = ArticleModel::select('id', 'title', 'description', 'thumb')
                ->whereIn('id', $somatoTypeRs->recommend_article)
                ->where('uniacid', \YunShop::app()->uniacid)->get();
            foreach ($somatoTypeRs->articles as &$v) {
                $v->thumb = explode(',', $v->thumb);
                $v->image = isset($v->thumb[0]) ? $v->thumb[0] : '';
            }
            unset($v);
        }
        return $this->successJson('调取用户体质报告成功', $somatoTypeRs);
    }

    public function share()
    {
        $memberId = \YunShop::app()->getMemberId();
        $scene = 'mid=' . $memberId;
        $page = 'pages/homework/test/homework';
        try {
            $qrcode = IndexController::qrcodeCreateUnlimit($memberId, $scene, $page, isset(\YunShop::request()->os) ? \YunShop::request()->os : '');
            if (!isset($qrcode->id) || !isset($qrcode->qrcode)) {
                throw new AppException('小程序码生成错误');
            }
        } catch (AppException $e) {
            Log::info("生成小程序码失败", [
                'qrcode' => isset($qrcode) ? $qrcode : '',
                'page' => $page,
                'scene' => $scene,
                'msg' => $e->getMessage(),
            ]);
            return $this->errorJson($e->getMessage());
        }
        return $this->successJson('二维码生成成功', [
            'id' => $qrcode->id,
            'qrcode' => yz_tomedia($qrcode->qrcode),
        ]);
    }
}
