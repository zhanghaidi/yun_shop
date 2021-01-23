<?php

namespace Yunshop\WechatComplaint\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Yunshop\WechatComplaint\models\ComplaintItemModel;
use Yunshop\WechatComplaint\services\WechatComplaintService;

class ItemController extends BaseController
{
    private $pageSize = 100;

    public function index()
    {
        $id = (int) \YunShop::request()->id;
        $isAjax = (int) \YunShop::request()->is_ajax;

        $listRs = ComplaintItemModel::where('uniacid', \YunShop::app()->uniacid)
            ->where('pid', $id)
            ->orderBy('order', 'desc')
            ->orderBy('id', 'asc')
            ->paginate($this->pageSize)->toArray();

        if ($isAjax == 1) {
            return $this->successJson('成功', $listRs['data']);
        }

        $pager = PaginationHelper::show($listRs['total'], $listRs['current_page'], $this->pageSize);

        return view('Yunshop\WechatComplaint::admin.item.index', [
            'pluginName' => WechatComplaintService::get('name'),
            'data' => $listRs['data'],
            'pager' => $pager,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            if (isset($data['id']) && $data['id'] > 0) {
                $item = ComplaintItemModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($item->id)) {
                    return $this->message('参数错误', '', 'danger');
                }
            } else {
                $item = new ComplaintItemModel;
                $item->uniacid = \YunShop::app()->uniacid;
                $item->pid = $data['pid'];
            }
            $item->type = 1;
            $item->submit_mode = 1;
            $item->name = $data['name'];
            $item->order = $data['order'];
            $item->save();

            return $this->message('保存成功', Url::absoluteWeb('plugin.wechat-complaint.admin.item.index'));
        }

        $id = (int) \YunShop::request()->id;

        $itemOrderRs = (new ComplaintItemModel)->getOrderList(\YunShop::app()->uniacid);
        if (!empty($itemOrderRs)) {
            $itemTreeRs = (new ComplaintItemModel)->paintTree($itemOrderRs);
        } else {
            $itemTreeRs = [];
        }

        if ($id > 0) {
            $infoRs = ComplaintItemModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('投诉选项数据错误，请联系开发或删除该选项', '', 'danger');
            }
        } else {
            $infoRs = null;
        }

        return view('Yunshop\WechatComplaint::admin.item.edit', [
            'pluginName' => WechatComplaintService::get('name'),
            'info' => $infoRs,
            'item' => $itemTreeRs,
        ]);
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        $itemRs = ComplaintItemModel::select('id')
            ->where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
        if (!isset($itemRs->id)) {
            return $this->message('投诉项未找到', '', 'danger');
        }

        $childRs = ComplaintItemModel::select('id')
            ->where([
                'pid' => $itemRs->id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
        if (isset($childRs->id)) {
            return $this->message('不能删除存在子分类的父分类节点', '', 'danger');
        }

        ComplaintItemModel::where('id', $id)->delete();
        return $this->message('删除成功');
    }
}
