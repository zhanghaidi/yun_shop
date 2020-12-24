<?php

namespace Yunshop\Examination\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Exception;
use Illuminate\Support\Facades\DB;
use Yunshop\Examination\models\PaperModel;
use Yunshop\Examination\models\PaperQuestionModel;
use Yunshop\Examination\models\QuestionModel;
use Yunshop\Examination\services\ExaminationService;

class PaperController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = PaperModel::where('uniacid', \YunShop::app()->uniacid);
        if (isset($searchData['name'])) {
            $list = $list->where('name', 'like', '%' . $searchData['name'] . '%');
        }
        $list = $list->orderBy('id', 'desc')
            ->paginate($this->pageSize)->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\Examination::admin.paper.index', [
            'pluginName' => ExaminationService::get('name'),
            'data' => $list['data'],
            'search' => $searchData,
            'pager' => $pager,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            if (!isset($data['question_id']) || !is_array($data['question_id']) ||
                !isset($data['question_id'][0])
            ) {
                return $this->message('试卷必须有题目', '', 'danger');
            }
            if (count($data['question_id']) > 255) {
                return $this->message('单张试卷不能大于255道题目', '', 'danger');
            }
            if (isset($data['omission_option'])) {
                $data['omission_option'] = array_values($data['omission_option']);
            }
            foreach ($data['question_id'] as $k => $v) {
                if (!isset($data['order'][$k]) || $data['order'][$k] <= 0) {
                    return $this->message('题目顺序数据错误', '', 'danger');
                }
                if (!isset($data['score'][$k]) || $data['score'][$k] <= 0 ||
                    $data['score'][$k] > 255
                ) {
                    return $this->message('题目分值设置错误，分值必须大于0', '', 'danger');
                }
                if (!isset($data['omission_option'][$k]) || !in_array($data['omission_option'][$k], [0, 1, 2])) {
                    return $this->message('多选题漏选分设置设置错误', '', 'danger');
                }
                if (!isset($data['omission_score'][$k])) {
                    return $this->message('多选题漏选分分值设置错误', '', 'danger');
                }
            }

            $questionRs = QuestionModel::select('id', 'type', 'problem')
                ->whereIn('id', $data['question_id'])
                ->where('uniacid', \YunShop::app()->uniacid)
                ->get()->toArray();

            DB::beginTransaction();
            try {
                if (isset($data['id']) && $data['id'] > 0) {
                    $paper = PaperModel::where([
                        'id' => $data['id'],
                        'uniacid' => \YunShop::app()->uniacid,
                    ])->first();
                    if (!isset($paper->id)) {
                        throw new Exception('数据ID不存在');
                    }
                } else {
                    $paper = new PaperModel;
                    $paper->uniacid = \YunShop::app()->uniacid;
                }
                $paper->name = (isset($data['name']) && !empty($data['name'])) ? $data['name'] : '试卷 - ' . date('Y年m月d日H:i');
                $paper->random_question = isset($data['random_question']) ? $data['random_question'] : 0;
                $paper->random_answer = 0;
                $paper->random_topic = 0;
                $paper->question = count($data['question_id']);
                $paper->score = array_sum($data['score']);
                $paper->save();
                if (!isset($paper->id) || $paper->id <= 0) {
                    throw new Exception('试卷设置保存错误');
                }

                PaperQuestionModel::where('paper_id', $paper->id)->delete();

                foreach ($data['question_id'] as $k1 => $v1) {
                    $tempQuestion = [];
                    foreach ($questionRs as $v2) {
                        if ($v1 != $v2['id']) {
                            continue;
                        }
                        $v2['problem'] = strip_tags($v2['problem']);
                        $v2['problem'] = trim($v2['problem']);
                        if (mb_strlen($v2['problem']) > 50) {
                            $v2['problem'] = mb_substr($v2['problem'], 0, 45) . ' ...';
                        }
                        $tempQuestion = $v2;
                        break;
                    }
                    if (!isset($tempQuestion['id'])) {
                        continue;
                    }

                    $paperQuestion = new PaperQuestionModel;
                    $paperQuestion->paper_id = $paper->id;
                    $paperQuestion->question_id = $v1;
                    $paperQuestion->type = $tempQuestion['type'];
                    $paperQuestion->problem = $tempQuestion['problem'];
                    $paperQuestion->score = $data['score'][$k1];
                    if ($tempQuestion['type'] == 2) {
                        $option = [
                            'option' => $data['omission_option'][$k1],
                            'score' => $data['omission_score'][$k1],
                        ];
                        $paperQuestion->option = json_encode($option);
                    } else {
                        $paperQuestion->option = '';
                    }
                    $paperQuestion->order = $data['order'][$k1];
                    $paperQuestion->save();
                    if (!isset($paperQuestion->id) || $paperQuestion->id <= 0) {
                        throw new Exception('试卷问题保存错误');
                    }
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();

                return $this->message($e->getMessage(), '', 'danger');
            }

            return $this->message('保存成功', Url::absoluteWeb('plugin.examination.admin.paper.index'));
        }

        $id = (int) \YunShop::request()->id;
        $paper = [];
        if ($id > 0) {
            $paper = PaperModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (isset($paper->id)) {
                $listRs = PaperQuestionModel::select('id', 'question_id', 'type', 'problem', 'score', 'option', 'order')
                    ->where('paper_id', $paper->id)
                    ->orderBy('order', 'asc')
                    ->orderBy('id', 'asc')->get()->toArray();
                foreach ($listRs as $k => $v) {
                    if ($v['type'] == 2) {
                        $temp = json_decode($v['option'], true);
                        $listRs[$k]['omission_option'] = isset($temp['option']) ? $temp['option'] : 1;
                        $listRs[$k]['omission_score'] = isset($temp['score']) ? $temp['score'] : 1;
                    }
                }
                $paper->question = $listRs;
            } else {
                $paper = [];
            }
        }

        return view('Yunshop\Examination::admin.paper.edit', [
            'pluginName' => ExaminationService::get('name'),
            'data' => $paper,
        ]);
    }

    public function addQuestion()
    {

        $id = \YunShop::request()->id;
        $id = explode(',', $id);
        if (isset($id[0])) {
            $listRs = QuestionModel::select('id', 'type', 'problem')
                ->whereIn('id', $id)
                ->where('uniacid', \YunShop::app()->uniacid)
                ->get()->toArray();
            foreach ($listRs as $k => $v) {
                $listRs[$k]['problem'] = strip_tags($v['problem']);
            }

            return $this->successJson('成功', $listRs);
        }
        return $this->errorJson('未知请求');
    }
}
