<?php

namespace Yunshop\Examination\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Exception;
use Illuminate\Support\Facades\DB;
use Yunshop\Examination\models\QuestionLogModel;
use Yunshop\Examination\models\QuestionModel;
use Yunshop\Examination\models\QuestionSortModel;
use Yunshop\Examination\services\ExaminationService;

class QuestionController extends BaseController
{
    private $pageSize = 20;

    public function index()
    {
        $questionSortRs = (new QuestionSortModel)->getList(\YunShop::app()->uniacid);
        $questionSortOrderRs = (new QuestionSortModel)->getOrderList(\YunShop::app()->uniacid);
        if (!empty($questionSortOrderRs)) {
            $questionSortTreeRs = (new QuestionSortModel)->paintTree($questionSortOrderRs);
        } else {
            $questionSortTreeRs = [];
        }

        $searchData = \YunShop::request()->search;
        $list = QuestionModel::getList();
        if (isset($searchData['sort_id'])) {
            $list = $list->where('sort_id', $searchData['sort_id']);
        }
        if (isset($searchData['type']) && in_array($searchData['type'], [1, 2, 3, 4, 5])) {
            $list = $list->where('type', $searchData['type']);
        }
        if (isset($searchData['problem'])) {
            $list = $list->where('problem', 'like', '%' . $searchData['problem'] . '%');
        }
        $list = $list->withTrashed()
            ->orderBy('id', 'desc')
            ->paginate($this->pageSize)->toArray();

        foreach ($list['data'] as $key => $question) {
            $list['data'][$key]['problem'] = strip_tags($question['problem']);
            if (mb_strlen($list['data'][$key]['problem']) > 100) {
                $list['data'][$key]['problem'] = mb_substr($list['data'][$key]['problem'], 0, 100) . ' ...';
            }
            foreach ($questionSortRs as $sort) {
                if ($question['sort_id'] != $sort['id']) {
                    continue;
                }
                $list['data'][$key]['sort_name'] = $sort['name'];
                break;
            }
        }

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\Examination::admin.question', [
            'pluginName' => ExaminationService::get('name'),
            'sort' => $questionSortTreeRs,
            'data' => $list['data'],
            'search' => $searchData,
            'pager' => $pager,
        ]);
    }

    public function add()
    {
        return view('Yunshop\Examination::admin.add', [
            'pluginName' => ExaminationService::get('name'),
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $id = isset($data['id']) ? $data['id'] : 0;
            try {
                $data = QuestionLogModel::saveDataProcess($data);
            } catch (Exception $e) {
                return $this->message($e->getMessage(), '', 'error');
            }

            DB::beginTransaction();
            try {
                if ($id > 0) {
                    $question = QuestionModel::where([
                        'id' => $id,
                        'uniacid' => \YunShop::app()->uniacid,
                    ])->first();
                    if (!isset($question->id)) {
                        throw new Exception('题目ID未找到');
                    }
                    $question->sort_id = $data['sort_id'];
                    $question->problem = $data['problem'];
                } else {
                    $question = new QuestionModel;
                    $question->uniacid = \YunShop::app()->uniacid;
                    $question->sort_id = $data['sort_id'];
                    $question->type = $data['type'];
                    $question->problem = $data['problem'];
                    $question->save();
                    if (!isset($question->id) || $question->id <= 0) {
                        throw new Exception('题目保存错误');
                    }
                }

                $log = new QuestionLogModel;
                $log->uniacid = \YunShop::app()->uniacid;
                $log->question_id = $question->id;
                $log->problem = $data['problem'];
                $log->answer = $data['answer'];
                $log->save();
                if (!isset($log->id) || $log->id <= 0) {
                    throw new Exception('答案保存错误');
                }

                $question->log_id = $log->id;
                $question->save();

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();

                return $this->message($e->getMessage(), '', 'error');
            }

            return $this->message('保存成功', Url::absoluteWeb('plugin.examination.admin.question.edit', ['id' => $question->id]));
        }

        $id = (int) \YunShop::request()->id;
        $type = (int) \YunShop::request()->type;

        $question = [];
        if ($id >= 0) {
            $questionRs = QuestionModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (isset($questionRs->id)) {
                $logRs = QuestionLogModel::where([
                    'id' => $questionRs->log_id,
                    'uniacid' => \YunShop::app()->uniacid,
                    'question_id' => $questionRs->id,
                ])->first();
                if (!isset($logRs->id)) {
                    return $this->message('题目数据错误，请联系开发或删除题目', '', 'error');
                }

                $type = $questionRs->type;
                $question = QuestionLogModel::getAdminManageInfo($questionRs, $logRs);
            } else {
                $id = 0;
            }
        }

        $questionSortOrderRs = (new QuestionSortModel)->getOrderList(\YunShop::app()->uniacid);
        if (!empty($questionSortOrderRs)) {
            $questionSortTreeRs = (new QuestionSortModel)->paintTree($questionSortOrderRs);
        } else {
            $questionSortTreeRs = [];
        }

        $typeDesc = QuestionModel::getTypeDesc($type);
        return view('Yunshop\Examination::admin.' . $typeDesc, [
            'pluginName' => ExaminationService::get('name'),
            'sort_tree' => $questionSortTreeRs,
            'data' => $question,
        ]);
    }
}
