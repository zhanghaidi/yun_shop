<?php

namespace Yunshop\Examination\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\Examination\models\PaperModel;
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
        $id = (int) \YunShop::request()->id;

        return view('Yunshop\Examination::admin.paper.edit', [
            'pluginName' => ExaminationService::get('name'),
            // 'data' => $question,
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
