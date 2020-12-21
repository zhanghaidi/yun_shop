<?php

namespace Yunshop\Examination\admin;

use app\common\components\BaseController;
use Yunshop\Examination\models\QuestionSortModel;

class QuestionSortController extends BaseController
{
    private $pageSize = 100;

    public function index()
    {
        $id = \YunShop::request()->id;
        $isAjax = \YunShop::request()->is_ajax;

        $listRs = QuestionSortModel::where('uniacid', \YunShop::app()->uniacid)
            ->where('pid', $id)
            ->orderBy('order', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($this->pageSize)->toArray();

        if ($isAjax == 1) {
            return $this->successJson('成功', $listRs['data']);
        }

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\Examination::admin.sort.index', [
            'pluginName' => ExaminationService::get('name'),
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }
}
