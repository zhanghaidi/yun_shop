<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use Yunshop\MinappContent\models\FeedbackModel;
use Yunshop\MinappContent\services\MinappContentService;

class FeedbackController extends BaseController
{
    private $pageSize = 30;

    public function index()
    {
        $list = FeedbackModel::selectRaw('*,count(1) as counts')
            ->where('uniacid', \YunShop::app()->uniacid)
            ->groupBy('user_id')
            ->orderBy('add_time', 'asc')
            ->paginate($this->pageSize)->toArray();
        foreach ($list['data'] as &$v) {
            $v['images'] = json_decode($v['images'], true);
        }
        unset($v);

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.feedback.list', [
            'pluginName' => MinappContentService::get('name'),
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }

    public function msg()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        $list = FeedbackModel::where('uniacid', \YunShop::app()->uniacid)
            ->where('user_id', $id)
            ->orderBy('add_time', 'asc')
            ->get()->toArray();
        foreach ($list as &$v) {
            $v['images'] = json_decode($v['images'], true);
        }
        unset($v);

        return view('Yunshop\MinappContent::admin.feedback.msg', [
            'pluginName' => MinappContentService::get('name'),
            'data' => $list,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        FeedbackModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
