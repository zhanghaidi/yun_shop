<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\MinappContent\models\QuestionBankModel;
use Yunshop\MinappContent\models\SomatoQuestionModel;
use Yunshop\MinappContent\models\SomatoTypeModel;
use Yunshop\MinappContent\services\MinappContentService;

class QuestionController extends BaseController
{
    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = QuestionBankModel::where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        return view('Yunshop\MinappContent::admin.question.list', [
            'pluginName' => MinappContentService::get('name'),
            'data' => $list,
        ]);
    }

    public function edit()
    {
        $typeRs = SomatoTypeModel::select('id', 'name')
            ->where('uniacid', \YunShop::app()->uniacid)->get()->toArray();

        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['title']) || !isset(trim($data['title'])[0])) {
                return $this->message('题目不能为空', '', 'danger');
            }
            $data['title'] = trim($data['title']);

            $somatoIds = $somatoNames = [];
            if (isset($data['somato_type_id']) && is_array($data['somato_type_id']) &&
                isset($data['somato_type_id'][0]) && $data['somato_type_id'][0] > 0
            ) {
                foreach ($typeRs as $v) {
                    if (in_array($v['id'], $data['somato_type_id'])) {
                        $somatoIds[] = $v['id'];
                        $somatoNames[] = $v['name'];
                    }
                }
            }

            if (isset($data['id'])) {
                $question = QuestionBankModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($question->id)) {
                    return $this->message('体质ID参数错误', '', 'danger');
                }
            } else {
                $question = new QuestionBankModel;
                $question->uniacid = \YunShop::app()->uniacid;
            }
            $question->list_order = isset($data['list_order']) ? $data['list_order'] : 0;
            $question->title = $data['title'];
            $question->option1_score = 1;
            $question->option2_score = 2;
            $question->option3_score = 3;
            $question->option4_score = 4;
            $question->option5_score = 5;
            $question->somato_type_name = implode(',', $somatoNames);
            $question->somato_type_id = implode(',', $somatoIds);
            $question->save();
            if (!isset($question->id) || $question->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            if (isset($data['id'])) {
                SomatoQuestionModel::where([
                    'uniacid' => \YunShop::app()->uniacid,
                    'question_id' => $question->id,
                ])->delete();
            }

            $insertData = array();
            $nowTime = time();
            foreach ($somatoIds as $v) {
                $insertData[] = [
                    'uniacid' => \YunShop::app()->uniacid,
                    'somato_type_id' => $v,
                    'question_id' => $question->id,
                    'add_time' => $nowTime,
                    'score_sort' => 1,
                ];
            }
            if (isset($insertData[0])) {
                SomatoQuestionModel::insert($insertData);
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.question.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = QuestionBankModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('测评题目不存在或已被删除', '', 'danger');
            }
            $infoRs->somato_type_id = explode(',', $infoRs->somato_type_id);
        }

        return view('Yunshop\MinappContent::admin.question.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => isset($infoRs) ? $infoRs : null,
            'type' => $typeRs,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        QuestionBankModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        SomatoQuestionModel::where([
            'uniacid' => \YunShop::app()->uniacid,
            'question_id' => $id,
        ])->delete();

        return $this->message('删除成功');
    }
}
