<?php

namespace Yunshop\Examination\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\Examination\models\QuestionModel;
use Yunshop\Examination\models\QuestionSortModel;
use Yunshop\Examination\services\ExaminationService;

class QuestionSortController extends BaseController
{
    private $pageSize = 100;

    public function index()
    {
        $id = (int) \YunShop::request()->id;
        $isAjax = (int) \YunShop::request()->is_ajax;

        $listRs = QuestionSortModel::where('uniacid', \YunShop::app()->uniacid)
            ->where('pid', $id)
            ->orderBy('order', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($this->pageSize)->toArray();
        $sortIds = array_column($listRs['data'], 'id');
        if (isset($sortIds[0])) {
            $questionRs = QuestionModel::where('uniacid', \YunShop::app()->uniacid)
                ->selectRaw('sort_id, count(*) as countnum')
                ->whereIn('sort_id', $sortIds)
                ->groupBy('sort_id')->get()->toArray();
            foreach ($listRs['data'] as $k1 => $v1) {
                $listRs['data'][$k1]['number'] = 0;
                foreach ($questionRs as $v2) {
                    if ($v1['id'] != $v2['sort_id']) {
                        continue;
                    }
                    $listRs['data'][$k1]['number'] = $v2['countnum'];
                    break;
                }
            }
        }

        if ($isAjax == 1) {
            return $this->successJson('成功', $listRs['data']);
        }

        $pager = PaginationHelper::show($listRs['total'], $listRs['current_page'], $this->pageSize);

        return view('Yunshop\Examination::admin.sort.index', [
            'pluginName' => ExaminationService::get('name'),
            'data' => $listRs['data'],
            'pager' => $pager,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            if (isset($data['id']) && $data['id'] > 0) {
                $sort = QuestionSortModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($sort->id)) {
                    return $this->message('参数错误', '', 'danger');
                }
            } else {
                $sort = new QuestionSortModel;
                $sort->uniacid = \YunShop::app()->uniacid;
                $sort->pid = $data['pid'];
            }
            $sort->name = $data['name'];
            $sort->order = $data['order'];
            $sort->save();

            return $this->message('保存成功', Url::absoluteWeb('plugin.examination.admin.question-sort.index'));
        }

        $id = (int) \YunShop::request()->id;

        $questionSortOrderRs = (new QuestionSortModel)->getOrderList(\YunShop::app()->uniacid);
        if (!empty($questionSortOrderRs)) {
            $questionSortTreeRs = (new QuestionSortModel)->paintTree($questionSortOrderRs);
        } else {
            $questionSortTreeRs = [];
        }

        if ($id > 0) {
            $infoRs = QuestionSortModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('题目分类数据错误，请联系开发或删除分类', '', 'danger');
            }
        } else {
            $infoRs = null;
        }

        return view('Yunshop\Examination::admin.sort.edit', [
            'pluginName' => ExaminationService::get('name'),
            'info' => $infoRs,
            'sort' => $questionSortTreeRs,
        ]);
    }

    public function del()
    {
        $id = (int) \YunShop::request()->id;
        $sortRs = QuestionSortModel::select('id')
            ->where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
        if (!isset($sortRs->id)) {
            return $this->message('分类未找到', '', 'danger');
        }

        $childRs = QuestionSortModel::select('id')
            ->where([
                'pid' => $sortRs->id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
        if (isset($childRs->id)) {
            return $this->message('不能删除存在子分类的父分类节点', '', 'danger');
        }

        $questionRs = QuestionModel::select('id')
            ->where('sort_id', $sortRs->id)->first();
        if (isset($questionRs->id)) {
            return $this->message('当前分类下，有题库中的题目存在，不能删除', '', 'danger');
        }

        QuestionSortModel::where('id', $id)->delete();
        return $this->message('删除成功');
    }
}
