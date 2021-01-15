<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\MinappContent\models\LabelModel;
use Yunshop\MinappContent\services\MinappContentService;

class LabelController extends BaseController
{
    private $pageSize = 30;

    public function index()
    {
        $searchData = \YunShop::request()->search;

        $list = LabelModel::where('uniacid', \YunShop::app()->uniacid)
            ->where('type', 2);
        if (isset($searchData['keywords']) && isset(trim($searchData['keywords'])[0])) {
            $searchData['keywords'] = trim($searchData['keywords']);
            $list = $list->where('name', 'like', '%' . $searchData['keywords'] . '%');
        }
        $list = $list->orderBy('list_order', 'desc')
            ->paginate($this->pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.label.list', [
            'pluginName' => MinappContentService::get('name'),
            'search' => $searchData,
            'data' => $list['data'],
            'pager' => $pager,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);
            if (!isset($data['name']) || !isset(trim($data['name'])[0])) {
                return $this->message('标签名称不能为空', '', 'danger');
            }
            $data['name'] = trim($data['name']);

            if (isset($data['id'])) {
                $label = LabelModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($label->id)) {
                    return $this->message('体质ID参数错误', '', 'danger');
                }
            } else {
                $label = new LabelModel;
                $label->uniacid = \YunShop::app()->uniacid;
            }
            $label->name = $data['name'];
            $label->list_order = isset($data['list_order']) ? $data['list_order'] : 0;
            $label->status = isset($data['status']) ? $data['status'] : 0;
            $label->type = 2;
            $label->save();
            if (!isset($label->id) || $label->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.label.index'));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = LabelModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('标签不存在或已被删除', '', 'danger');
            }
        }

        return view('Yunshop\MinappContent::admin.label.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => isset($infoRs) ? $infoRs : null,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        LabelModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        return $this->message('删除成功');
    }
}
