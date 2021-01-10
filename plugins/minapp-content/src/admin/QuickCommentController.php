<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\MinappContent\services\MinappContentService;
use Illuminate\Support\Facades\DB;

class QuickCommentController extends BaseController
{
    private $pageSize = 20;

    /**
     * 快捷评语列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed|\think\response\View
     */
    public function index()
    {
        $input = \YunShop::request();
        $uniacid = \YunShop::app()->uniacid;
        $where[] = ['uniacid', '=', $uniacid];
        if (isset($input->search)) {
            $search = $input->search;
            if (intval($search['status']) > 0) {
                $where[] = ['status', '=', intval($search['status'])];
            }
            if (trim($search['content']) !== '') {
                $where[] = ['content', 'like', '%' . trim($search['content']) . '%'];
            }
        }

        $comments = DB::table('diagnostic_service_quick_comment')
            ->where($where)
            ->orderBy('create_time', 'desc')
            ->paginate($this->pageSize);

        $pager = PaginationHelper::show($comments->total(), $comments->currentPage(), $comments->perPage());

        return view('Yunshop\MinappContent::admin.quick_comment.quick_comment_list', [
            'pluginName' => MinappContentService::get('name'),
            'comments' => $comments,
            'pager' => $pager,
            'request' => $input,
        ]);
    }
}
