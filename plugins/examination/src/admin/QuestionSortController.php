<?php

namespace Yunshop\Examination\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
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
}
